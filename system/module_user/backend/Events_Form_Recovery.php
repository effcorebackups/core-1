<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\user;

use effcore\Instance;
use effcore\Mail;
use effcore\Message;
use effcore\Url;
use effcore\User;

abstract class Events_Form_Recovery {

    static function on_validate($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'recovery':
                if (!$form->has_error()) {
                    if (!(new Instance('user', ['email' => $items['#email']->value_get()]))->select()) {
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
                $user = (new Instance('user', [
                    'email' => $items['#email']->value_get()
                ]))->select();
                if ($user) {
                    $new_password = User::password_generate();
                    $user->password_hash = User::password_hash($new_password);
                    if ($user->update()) {
                        $domain = Url::get_current()->domain;
                        if (Mail::send('recovery', 'no-reply@'.$domain, $user, ['domain' => $domain], ['domain' => $domain, 'new_password' => $new_password], $form, $items)) {
                            Message::insert('A new password was sent to the selected Email.');
                            Url::go(Url::back_url_get() ?: '/login');
                        }
                    }
                }
                break;
        }
    }

}
