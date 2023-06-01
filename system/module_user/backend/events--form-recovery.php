<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\user;

use effcore\instance;
use effcore\mail;
use effcore\message;
use effcore\url;
use effcore\user;

abstract class events_form_recovery {

    static function on_validate($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'recovery':
                if (!$form->has_error()) {
                    if (!(new instance('user', ['email' => $items['#email']->value_get()]))->select()) {
                        $items['#email']->error_set(
                            'User with this Email was not registered!'
                        );
                        return;
                    }
                }
                break;
        }
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'recovery':
                $user = (new instance('user', [
                    'email' => $items['#email']->value_get()
                ]))->select();
                if ($user) {
                    $new_password = user::password_generate();
                    $user->password_hash = user::password_hash($new_password);
                    if ($user->update()) {
                        $domain = url::get_current()->domain;
                        if (mail::send('recovery', 'no-reply@'.$domain, $user, ['domain' => $domain], ['domain' => $domain, 'new_password' => $new_password], $form, $items)) {
                            message::insert('A new password was sent to the selected Email.');
                            url::go(url::back_url_get() ?: '/login');
                        }
                    }
                }
                break;
        }
    }

}
