<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\user;

use effcore\Core;
use effcore\Message;
use effcore\Selection;
use effcore\Session;
use effcore\Text;
use effcore\URL;
use effcore\User;

abstract class Events_Form_Logout {

    static function on_build($event, $form) {
        $selection = Selection::get('user_sessions');
        $selection = Core::deep_clone($selection);
        $selection->build();
        $form->child_select('sessions')->children_delete();
        $form->child_select('sessions')->child_insert($selection, 'selection');
    }

    static function on_init($event, $form, $items) {
        $session_active = Session::select();
        if (isset($items['#is_checked:'.$session_active->id])) {
            $items['#is_checked:'.$session_active->id]->checked_set();
        }
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'logout':
                $messages       = [];
                $has_choice     = false;
                $sessions       = Session::select_all_by_id_user(User::get_current()->id);
                $session_active = Session::select();
                foreach ($sessions as $c_session) {
                    if ($items['#is_checked:'.$c_session->id]->checked_get()) {
                        $has_choice = true;
                        if (Session::delete(User::get_current()->id, $session_active->id === $c_session->id ? null /* for regenerate */ : $c_session->id))
                             $messages['ok'     ][] = new Text('Session with ID = "%%_id" was deleted.'    , ['id' => $c_session->id]);
                        else $messages['warning'][] = new Text('Session with ID = "%%_id" was not deleted!', ['id' => $c_session->id]);
                    }
                }
                if ($has_choice) {
                    if (Session::select()) {
                        foreach ($messages as $c_type => $c_messages_by_type)
                            foreach ($c_messages_by_type as $c_message)
                                Message::insert($c_message, $c_type);
                        $form->components_build();
                        $form->components_init();
                    } else URL::go(URL::back_url_get() ?: '/');
                } else {
                    Message::insert('No one item was selected!', 'warning');
                    foreach ($sessions as $c_session) {
                        $items['#is_checked:'.$c_session->id]->error_set();
                    }
                }
                break;
        }
    }

}
