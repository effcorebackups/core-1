<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\user;

use const effcore\BR;
use effcore\Access;
use effcore\Instance;
use effcore\Response;
use effcore\Role;
use effcore\Text_multiline;
use effcore\User;

abstract class Events_Page_User {

    static function on_check_access_and_existence($event, $page) {
        $user = (new Instance('user', [
            'nickname' => $page->args_get('nickname')
        ]))->select();
        if ($user) {
            if ($user->id === User::get_current()->id ||                      # owner
                Access::check((object)['roles' => ['admins' => 'admins']])) { # admin
            } else Response::send_header_and_exit('access_forbidden');
        }     else Response::send_header_and_exit('page_not_found', null, new Text_multiline(['wrong user nickname', 'go to <a href="/">front page</a>'], [], BR.BR));
    }

    static function on_show_user_roles($c_row_id, $c_row, $c_instance, $settings = []) {
        $roles_with_title = [];
        $roles = Role::get_all();
        $roles_by_user = User::related_roles_select($c_instance->id);
        foreach ($roles_by_user as $c_id_user_role)
            $roles_with_title[$c_id_user_role] =
                       $roles[$c_id_user_role]->title ??
                              $c_id_user_role;
        return new Text_multiline(
            $roles_with_title, [], ', '
        );
    }

}
