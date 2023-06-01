<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\user;

use effcore\core;
use effcore\decorator;
use effcore\field_checkbox;
use effcore\locale;
use effcore\markup;
use effcore\message;
use effcore\session;
use effcore\text;
use effcore\url;
use effcore\user;

abstract class events_form_logout {

    static function on_init($event, $form, $items) {
        $sessions       = session::select_all_by_id_user(user::get_current()->id);
        $session_active = session::select();
        $decorator = new decorator('table-adaptive');
        $decorator->id = 'sessions_logout';
        $form->child_select('sessions')->children_delete();
        $form->child_select('sessions')->child_insert(new markup('h2', [], 'Sessions'), 'title');
        $form->child_select('sessions')->child_insert($decorator, 'decorator');
        foreach ($sessions as $c_session) {
            $c_checkbox = new field_checkbox;
            $c_checkbox->build();
            $c_checkbox->name_set('is_checked[]');
            $c_checkbox->value_set($c_session->id);
            $c_checkbox->checked_set($c_session->id === $session_active->id);
            $c_user_agent_filtered = $c_session->data->user_agent ? core::return_htmlspecialchars_encoded($c_session->data->user_agent) : null;
            $decorator->data[$c_session->id] = [
                'checkbox'    => ['value' => $c_checkbox,                                                'title' => ''               ],
                'is_current'  => ['value' => core::format_logic($c_session->id === $session_active->id), 'title' => 'Is current'     ],
                'is_fixed_ip' => ['value' => core::format_logic($c_session->is_fixed_ip),                'title' => 'Is fixed IP'    ],
                'expired'     => ['value' => locale::format_datetime($c_session->expired),               'title' => 'Expiration date'],
                'info'        => ['value' => $c_user_agent_filtered,                                     'title' => 'User agent'     ]
            ]; }
        $decorator->build();
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'logout':
                $messages       = [];
                $has_choice     = false;
                $sessions       = session::select_all_by_id_user(user::get_current()->id);
                $session_active = session::select();
                foreach ($sessions as $c_session) {
                    if ($items['#is_checked:'.$c_session->id]->checked_get()) {
                        $has_choice = true;
                        if (session::delete(user::get_current()->id, $session_active->id === $c_session->id ? null /* for regenerate */ : $c_session->id))
                             $messages['ok'     ][] = new text('Session with ID = "%%_id" was deleted.',     ['id' => $c_session->id]);
                        else $messages['warning'][] = new text('Session with ID = "%%_id" was not deleted!', ['id' => $c_session->id]);
                    }
                }
                if ($has_choice) {
                    if (session::select()) {
                        foreach ($messages as $c_type => $c_messages_by_type)
                            foreach ($c_messages_by_type as $c_message)
                                message::insert($c_message, $c_type);
                        static::on_init(null, $form, $items);
                    } else url::go(url::back_url_get() ?: '/');
                } else {
                    message::insert('No one item was selected!', 'warning');
                    foreach ($sessions as $c_session) {
                        $items['#is_checked:'.$c_session->id]->error_set();
                    }
                }
                break;
        }
    }

}
