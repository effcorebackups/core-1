<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\user;

use effcore\Access;
use effcore\User;

abstract class Events_Token {

    static function on_apply($name, $args) {
        User::init(false);
        if (Access::check((object)['roles' => ['registered' => 'registered']])) {
            switch ($name) {
                case 'user_id'    : return User::get_current()->id;
                case 'nickname'   : return User::get_current()->nickname;
                case 'email'      : return User::get_current()->email;
                case 'avatar_path': return User::get_current()->avatar_path;
            }
        }
    }

}
