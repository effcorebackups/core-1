<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class permission {

    public $id;
    public $title;

    ###########################
    ### static declarations ###
    ###########################

    static protected $cache;
    static protected $is_init___sql = false;

    static function cache_cleaning() {
        static::$cache         = null;
        static::$is_init___sql = false;
    }

    static function init_sql() {
        if (!static::$is_init___sql) {
             static::$is_init___sql = true;
            foreach (entity::get('permission')->instances_select() as $c_instance) {
                $c_permission = new static;
                foreach ($c_instance->values_get() as $c_key => $c_value)
                    $c_permission->                  {$c_key} = $c_value;
                static::$cache[$c_permission->id] = $c_permission;
                static::$cache[$c_permission->id]->origin = 'sql';
            }
        }
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function get_all() {
        static::init_sql();
        return static::$cache;
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function related_roles_select($id_permission) {
        $result = [];
        $items = entity::get('relation_role_ws_permission')->instances_select(['conditions' => [
            'id_permission_!f'       => 'id_permission',
            'id_permission_operator' => '=',
            'id_permission_!v'       => $id_permission]]);
        foreach ($items as $c_item)
            $result[$c_item->id_role] =
                    $c_item->id_role;
        return $result;
    }

    static function related_roles_insert($id_permission, $roles, $module_id = null) {
        foreach ($roles as $c_id_role) {
            (new instance('relation_role_ws_permission', [
                'id_role'       => $c_id_role,
                'id_permission' => $id_permission,
                'module_id'     => $module_id
            ]))->insert();
        }
    }

    static function related_roles_delete($id_permission) {
        entity::get('relation_role_ws_permission')->instances_delete(['conditions' => [
            'id_permission_!f'       => 'id_permission',
            'id_permission_operator' => '=',
            'id_permission_!v'       => $id_permission
        ]]);
    }

    static function related_role_delete($id_permission, $id_role) {
        (new instance('relation_role_ws_permission', [
            'id_permission' => $id_permission,
            'id_role'       => $id_role
        ]))->delete();
    }

}
