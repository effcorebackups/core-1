<?php

######################################################################
### Copyright © 20NN—20NN Author/Rightholder. All rights reserved. ###
######################################################################

namespace effcore\modules\profile_classic;

use effcore\Instance;
use effcore\Page;
use effcore\Url;

abstract class Events_Form_Login {

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'login':
                if (!Url::back_url_get() && Page::get_current()->id === 'login_ru') {
                    $user = (new Instance('user', [
                        'email' => $items['#email']->value_get()
                    ]))->select();
                    if ($user && hash_equals($user->password_hash, $items['#password']->value_get())) {
                        Url::back_url_set('back', '/ru/user/'.$user->nickname);
                    }
                }
                break;
        }
    }

}
