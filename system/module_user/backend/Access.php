<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

abstract class Access {

    protected static $cache = [];

    static function cache_cleaning() {
        static::$cache = [];
    }

    static function check($access, $user = null) {
        if ($access === null) return true;
        if (  $user === null) $user = User::get_current();
        if (isset($access->roles      )) foreach ($user->roles as $c_role) if (isset($access->roles[ $c_role ])) {Console::log_insert('access', 'checking', 'access allowed by role "%%_role"'   , 'ok', 0, ['role' =>  $c_role ]); return true;}
        if (isset($access->users      ))                                   if (isset($access->users[$user->id])) {Console::log_insert('access', 'checking', 'access allowed by user ID "%%_user"', 'ok', 0, ['user' => $user->id]); return true;}
        if (isset($access->permissions)) {
            if (!isset(static::$cache[$user->id]['permissions']))
                       static::$cache[$user->id]['permissions'] = Role::related_permissions_by_roles_select($user->roles);
            foreach ($access->permissions as $c_id_permission) {
                if (isset(static::$cache[$user->id]['permissions'][$c_id_permission])) {
                    Console::log_insert('access', 'checking', 'access allowed by permission "%%_permission"', 'ok', 0, ['permission' => $c_id_permission]);
                    return true;
                }
            }
        }
        if (isset($access->permissions_match)) {
            if (!isset(static::$cache[$user->id]['permissions']))
                       static::$cache[$user->id]['permissions'] = Role::related_permissions_by_roles_select($user->roles);
            foreach ($access->permissions_match as $c_permission_expression) {
                foreach (static::$cache[$user->id]['permissions'] as $c_user_permission) {
                    if (preg_match($c_permission_expression, $c_user_permission)) {
                        Console::log_insert('access', 'checking', 'access allowed by permission "%%_permission"', 'ok', 0, ['permission' => $c_permission_expression]);
                        return true;
                    }
                }
            }
        }
    }

}
