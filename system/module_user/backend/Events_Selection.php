<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\user;

use effcore\Role;
use effcore\Text_multiline;
use effcore\User;

abstract class Events_Selection {

    static function handler__user__roles($c_row_id, $c_row, $c_instance, $settings = []) {
        $roles_by_user = User::related_roles_select($c_instance->id);
        if (count($roles_by_user)) {
            $roles_with_title = [];
            $roles = Role::get_all();
            foreach ($roles_by_user as $c_id_user_role)
                $roles_with_title[$c_id_user_role] =
                           $roles[$c_id_user_role]->title ??
                                  $c_id_user_role;
            return new Text_multiline(
                $roles_with_title, [], ', '
            );
        } else {
            return '—';
        }
    }

}
