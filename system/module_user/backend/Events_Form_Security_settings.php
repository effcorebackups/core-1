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
        $items['#session_duration_short']->value_set($settings->session_duration_short);
        $items['#session_duration_long' ]->value_set($settings->session_duration_long);
        $items['#login_attempts'        ]->value_set($settings->login_attempts);
        $items['#login_blocked_until'   ]->value_set($settings->login_blocked_until);
        $items['#send_password_to_email']->value_set($settings->send_password_to_email);
    }

    static function on_validate($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'save':
                if (!$form->has_error()) {
                    if ($items['#session_duration_short']->value_get() >=
                        $items['#session_duration_long' ]->value_get()) {
                        $items['#session_duration_short']->error_set();
                        $items['#session_duration_long' ]->error_set();
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
                $result&= Storage::get('data')->changes_register('user', 'update', 'settings/user/cookie_domain'         ,      $items['#cookie_domain'         ]->value_get(), false);
                $result&= Storage::get('data')->changes_register('user', 'update', 'settings/user/session_duration_short', (int)$items['#session_duration_short']->value_get(), false);
                $result&= Storage::get('data')->changes_register('user', 'update', 'settings/user/session_duration_long' , (int)$items['#session_duration_long' ]->value_get(), false);
                $result&= Storage::get('data')->changes_register('user', 'update', 'settings/user/login_attempts'        , (int)$items['#login_attempts'        ]->value_get(), false);
                $result&= Storage::get('data')->changes_register('user', 'update', 'settings/user/login_blocked_until'   , (int)$items['#login_blocked_until'   ]->value_get(), false);
                $result&= Storage::get('data')->changes_register('user', 'update', 'settings/user/send_password_to_email', (int)$items['#send_password_to_email']->value_get());
                if ($result) Message::insert('Changes was saved.'             );
                else         Message::insert('Changes was not saved!', 'error');
                break;
            case 'reset':
                $result = true;
                $result&= Storage::get('data')->changes_unregister('user', 'update', 'settings/user/cookie_domain'         , false);
                $result&= Storage::get('data')->changes_unregister('user', 'update', 'settings/user/session_duration_short', false);
                $result&= Storage::get('data')->changes_unregister('user', 'update', 'settings/user/session_duration_long' , false);
                $result&= Storage::get('data')->changes_unregister('user', 'update', 'settings/user/login_attempts'        , false);
                $result&= Storage::get('data')->changes_unregister('user', 'update', 'settings/user/login_blocked_until'   , false);
                $result&= Storage::get('data')->changes_unregister('user', 'update', 'settings/user/send_password_to_email');
                if ($result) Message::insert('Changes was deleted.'             );
                else         Message::insert('Changes was not deleted!', 'error');
                static::on_init(null, $form, $items);
                break;
        }
    }

}
