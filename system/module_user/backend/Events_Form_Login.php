<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\user;

use effcore\Core;
use effcore\Instance;
use effcore\Message;
use effcore\Module;
use effcore\Session;
use effcore\Text_multiline;
use effcore\Text;
use effcore\Url;

abstract class Events_Form_Login {

    static function on_init($event, $form, $items) {
        $settings = Module::settings_get('user');
        $items['#session_params:is_long_session']->attributes['title'] = new Text_multiline([
            'Short session: %%_min day%%_plural(min|s) | long session: %%_max day%%_plural(max|s)'], [
            'min' => $settings->session_duration_min,
            'max' => $settings->session_duration_max], '', true, true);
        if (!isset($_COOKIE['cookies_is_enabled'])) {
            Message::insert(new Text_multiline([
                'Cookies are disabled. You cannot log in!',
                'Enable cookies before login.']), 'warning'
            );
        }
    }

    static function on_validate($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'login':
                if (!$form->has_error()) {
                    $user = (new Instance('user', [
                        'email' => $items['#email']->value_get()
                    ]))->select();
                    if (!$user || !hash_equals($user->password_hash, $items['#password']->value_get())) {
                        $items['#email'   ]->error_set();
                        $items['#password']->error_set();
                        $form->error_set('Incorrect email or password!');
                    }
                }
                break;
        }
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'login':
                $user = (new Instance('user', [
                    'email' => $items['#email']->value_get()
                ]))->select();
                if ($user && hash_equals($user->password_hash, $items['#password']->value_get())) {
                    Session::insert($user->id, Core::array_keys_map($items['*session_params']->value_get()));
                    Message::insert(new Text('Welcome, %%_nickname!', ['nickname' => $user->nickname]));
                    Url::go(Url::back_url_get() ?: '/user/'.$user->nickname);
                }
                break;
        }
    }

}
