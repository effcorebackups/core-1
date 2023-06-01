<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

abstract class user {

    static protected $cache;

    static function cache_cleaning() {
        static::$cache = null;
    }

    static function init($is_load_roles = true, $is_load_permissions = false) {
        if (static::$cache === null) {
            static::$cache = new instance('user');
            static::$cache->nickname = null;
            static::$cache->id       = null;
            static::$cache->roles    = ['anonymous' => 'anonymous'];
            $session = session::select();
            if ($session &&
                $session->id_user) {
                $user = static::select(
                    $session->id_user,
                    $is_load_roles,
                    $is_load_permissions);
                if ($user) {
                    static::$cache = $user;
                }
            }
        }
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function get_current() {
        static::init();
        return static::$cache;
    }

    static function select($id, $is_load_roles = false, $is_load_permissions = false) {
        $user = new instance('user', ['id' => $id]);
        if ($user->select()) {
            $user->roles = $is_load_roles ?
                ['registered' => 'registered'] + static::related_roles_select($id) :
                ['registered' => 'registered'];
            $user->permissions = $is_load_permissions ?
                role::related_permissions_by_roles_select($user->roles) : [];
            return $user;
        }
    }

    static function insert($values) {
        return (new instance('user', $values))->insert();
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function related_roles_select($id_user) {
        $result = [];
        $items = entity::get('relation_role_ws_user')->instances_select(['conditions' => [
            'id_user_!f'       => 'id_user',
            'id_user_operator' => '=',
            'id_user_!v'       => $id_user]]);
        foreach ($items as $c_item)
            $result[$c_item->id_role] =
                    $c_item->id_role;
        return $result;
    }

    static function related_roles_insert($id_user, $roles, $module_id = null) {
        foreach ($roles as $c_id_role) {
            (new instance('relation_role_ws_user', [
                'id_role'   => $c_id_role,
                'id_user'   =>   $id_user,
                'module_id' => $module_id
            ]))->insert();
        }
    }

    static function related_roles_delete($id_user) {
        entity::get('relation_role_ws_user')->instances_delete(['conditions' => [
            'id_user_!f'       => 'id_user',
            'id_user_operator' => '=',
            'id_user_!v'       => $id_user
        ]]);
    }

    static function related_role_delete($id_user, $id_role) {
        (new instance('relation_role_ws_user', [
            'id_user' => $id_user,
            'id_role' => $id_role
        ]))->delete();
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function key_get($name) {
        return storage::get('data')->select('settings/user/keys/'.$name);
    }

    static function keys_install() {
        return storage::get('data')->changes_insert('user', 'update', 'settings/user/keys', [
            'cron' => core::random_bytes_generate(40, module::settings_get('user')->hash_characters),
            'salt' => core::random_bytes_generate(40, module::settings_get('user')->key_characters),
            'form' => core::random_bytes_generate(40, module::settings_get('user')->key_characters),
            'user' => core::random_bytes_generate(40, module::settings_get('user')->key_characters),
            'args' => core::random_bytes_generate(40, module::settings_get('user')->key_characters),
        ], true, false);
    }

    static function signature_get($string, $key_name, $length = 40) {
        $key = static::key_get($key_name);
        if ($key) return substr(hash('sha3-512', hash('sha3-512', $string).$key), 0, $length);
        else message::insert(new text('Key "%%_key" does not exist!', ['key' => $key_name]), 'error');
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function password_generate($length = 8) {
        return core::random_bytes_generate($length,
            module::settings_get('user')->password_characters
        );
    }

    static function password_hash($password) {
        return hash('sha3-512', hash('sha3-512', $password).static::key_get('salt'));
    }

    static function password_verify($password, $hash) {
        return hash_equals($hash, static::password_hash($password));
    }

}
