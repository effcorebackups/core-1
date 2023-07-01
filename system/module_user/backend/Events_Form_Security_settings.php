<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\user;

use effcore\Message;
use effcore\Module;
use effcore\Storage;

abstract class Events_Form_Security_settings {

    static function on_init($event, $form, $items) {
        $settings = Module::settings_get('user');
        $items['#cookie_domain'         ]->value_set($settings->cookie_domain);
        $items['#login_attempts'        ]->value_set($settings->login_attempts);
        $items['#blocked_until'         ]->value_set($settings->blocked_until);
        $items['#session_duration_min'  ]->value_set($settings->session_duration_min);
        $items['#session_duration_max'  ]->value_set($settings->session_duration_max);
        $items['#send_password_to_email']->value_set($settings->send_password_to_email);
    }

    static function on_validate($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'save':
                if (!$form->has_error()) {
                    if ($items['#session_duration_min']->value_get() >=
                        $items['#session_duration_max']->value_get()) {
                        $items['#session_duration_min']->error_set();
                        $items['#session_duration_max']->error_set();
                        $form->error_set('The minimum value cannot be greater than or equal to the maximum!');
                    }
                }
                break;
        }
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'save':
                $result = true;
                $result&= Storage::get('data')->changes_insert('user', 'update', 'settings/user/cookie_domain',               $items['#cookie_domain'         ]->value_get(), false);
                $result&= Storage::get('data')->changes_insert('user', 'update', 'settings/user/login_attempts',         (int)$items['#login_attempts'        ]->value_get(), false);
                $result&= Storage::get('data')->changes_insert('user', 'update', 'settings/user/blocked_until',          (int)$items['#blocked_until'         ]->value_get(), false);
                $result&= Storage::get('data')->changes_insert('user', 'update', 'settings/user/session_duration_min',   (int)$items['#session_duration_min'  ]->value_get(), false);
                $result&= Storage::get('data')->changes_insert('user', 'update', 'settings/user/session_duration_max',   (int)$items['#session_duration_max'  ]->value_get(), false);
                $result&= Storage::get('data')->changes_insert('user', 'update', 'settings/user/send_password_to_email', (int)$items['#send_password_to_email']->value_get());
                if ($result) Message::insert('Changes was saved.'             );
                else         Message::insert('Changes was not saved!', 'error');
                break;
            case 'reset':
                $result = true;
                $result&= Storage::get('data')->changes_delete('user', 'update', 'settings/user/cookie_domain',        false);
                $result&= Storage::get('data')->changes_delete('user', 'update', 'settings/user/login_attempts',       false);
                $result&= Storage::get('data')->changes_delete('user', 'update', 'settings/user/blocked_until',        false);
                $result&= Storage::get('data')->changes_delete('user', 'update', 'settings/user/session_duration_min', false);
                $result&= Storage::get('data')->changes_delete('user', 'update', 'settings/user/session_duration_max', false);
                $result&= Storage::get('data')->changes_delete('user', 'update', 'settings/user/send_password_to_email');
                if ($result) Message::insert('Changes was deleted.'             );
                else         Message::insert('Changes was not deleted!', 'error');
                static::on_init(null, $form, $items);
                break;
        }
    }

}
