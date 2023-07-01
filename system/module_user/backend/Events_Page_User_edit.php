<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\user;

use const effcore\BR;
use effcore\Access;
use effcore\Instance;
use effcore\Response;
use effcore\Text_multiline;
use effcore\User;

abstract class Events_Page_User_edit {

    static function on_check_access_and_existence_and_set_page_args($event, $page) {
        $user = (new Instance('user', [
            'nickname' => $page->args_get('nickname')
        ]))->select();
        if ($user) {
            $page->args_set('instance_id', $user->id);
            if ($user->id === User::get_current()->id ||                      # owner
                Access::check((object)['roles' => ['admins' => 'admins']])) { # admin
            } else Response::send_header_and_exit('access_forbidden');
        }     else Response::send_header_and_exit('page_not_found', null, new Text_multiline(['wrong user nickname', 'go to <a href="/">front page</a>'], [], BR.BR));
    }

}
