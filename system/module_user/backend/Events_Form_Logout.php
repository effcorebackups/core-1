<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\user;

use effcore\Core;
use effcore\Decorator;
use effcore\Field_Checkbox;
use effcore\Locale;
use effcore\Markup;
use effcore\Message;
use effcore\Session;
use effcore\Text;
use effcore\Url;
use effcore\User;

abstract class Events_Form_Logout {

    static function on_init($event, $form, $items) {
        $sessions       = Session::select_all_by_id_user(User::get_current()->id);
        $session_active = Session::select();
        $decorator = new Decorator('table-adaptive');
        $decorator->id = 'sessions_logout';
        $form->child_select('sessions')->children_delete();
        $form->child_select('sessions')->child_insert(new Markup('h2', [], 'Sessions'), 'title');
        $form->child_select('sessions')->child_insert($decorator, 'decorator');
        foreach ($sessions as $c_session) {
            $c_checkbox = new Field_Checkbox;
            $c_checkbox->build();
            $c_checkbox->name_set('is_checked[]');
            $c_checkbox->value_set($c_session->id);
            $c_checkbox->checked_set($c_session->id === $session_active->id);
            $c_user_agent_filtered = $c_session->data->user_agent ? Core::html_entity_encode($c_session->data->user_agent) : null;
            $decorator->data[$c_session->id] = [
                'checkbox'    => ['value' => $c_checkbox,                                                'title' => ''               ],
                'is_current'  => ['value' => Core::format_logic($c_session->id === $session_active->id), 'title' => 'Is current'     ],
                'is_fixed_ip' => ['value' => Core::format_logic($c_session->is_fixed_ip),                'title' => 'Is fixed IP'    ],
                'expired'     => ['value' => Locale::format_datetime($c_session->expired),               'title' => 'Expiration date'],
                'info'        => ['value' => $c_user_agent_filtered,                                     'title' => 'User agent'     ]
            ]; }
        $decorator->build();
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
                             $messages['ok'     ][] = new Text('Session with ID = "%%_id" was deleted.',     ['id' => $c_session->id]);
                        else $messages['warning'][] = new Text('Session with ID = "%%_id" was not deleted!', ['id' => $c_session->id]);
                    }
                }
                if ($has_choice) {
                    if (Session::select()) {
                        foreach ($messages as $c_type => $c_messages_by_type)
                            foreach ($c_messages_by_type as $c_message)
                                Message::insert($c_message, $c_type);
                        static::on_init(null, $form, $items);
                    } else Url::go(Url::back_url_get() ?: '/');
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
