<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\user;

use const effcore\BR;
use effcore\access;
use effcore\instance;
use effcore\response;
use effcore\text_multiline;
use effcore\user;

abstract class events_page_user_edit {

    static function on_check_access_and_existence_and_set_page_args($event, $page) {
        $user = (new instance('user', [
            'nickname' => $page->args_get('nickname')
        ]))->select();
        if ($user) {
            $page->args_set('instance_id', $user->id);
            if ($user->id === user::get_current()->id ||                      # owner
                access::check((object)['roles' => ['admins' => 'admins']])) { # admin
            } else response::send_header_and_exit('access_forbidden');
        }     else response::send_header_and_exit('page_not_found', null, new text_multiline(['wrong user nickname', 'go to <a href="/">front page</a>'], [], BR.BR));
    }

}
