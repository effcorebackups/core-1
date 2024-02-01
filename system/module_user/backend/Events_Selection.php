<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\user;

use effcore\Core;
use effcore\Markup;
use effcore\Node;
use effcore\Permission;
use effcore\Role;
use effcore\Session;
use effcore\Text_simple;
use effcore\Text;
use effcore\User;

abstract class Events_Selection {

    ##################################
    ### handlers for 'user' entity ###
    ##################################

    static function handler__user__avatar_paths($c_cell_id, $c_row, $c_instance, $origin) {
        if (array_key_exists('avatar_path', $c_instance->values_get())) {
            if ($c_instance->avatar_path) {
                $result = new Markup('ul', ['data-type' => 'paths']);
                $result->child_insert(new Markup('li', ['data-name' => 'original'], new Text_simple(Core::to_url_from_path($c_instance->avatar_path)               )), 'original');
                $result->child_insert(new Markup('li', ['data-name' => 'small'   ], new Text_simple(Core::to_url_from_path($c_instance->avatar_path).'?thumb=small')), 'small'   );
                   return $result;
            } else return '';
        }     else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'avatar_path']);
    }

    static function handler__user__avatar_paths_as_links($c_cell_id, $c_row, $c_instance, $origin) {
        if (array_key_exists('avatar_path', $c_instance->values_get())) {
            if ($c_instance->avatar_path) {
                $result = new Markup('ul', ['data-type' => 'paths']);
                $result->child_insert(new Markup('li', ['data-name' => 'original'], new Markup('a', ['href' => Core::to_url_from_path($c_instance->avatar_path)               , 'target' => '_blank'], new Text_simple(Core::to_url_from_path($c_instance->avatar_path)               ))), 'original');
                $result->child_insert(new Markup('li', ['data-name' => 'small'   ], new Markup('a', ['href' => Core::to_url_from_path($c_instance->avatar_path).'?thumb=small', 'target' => '_blank'], new Text_simple(Core::to_url_from_path($c_instance->avatar_path).'?thumb=small'))), 'small'   );
                   return $result;
            } else return '';
        }     else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'avatar_path']);
    }

    static function handler__user__roles($c_cell_id, $c_row, $c_instance, $origin) {
        if (array_key_exists('id', $c_instance->values_get())) {
            $roles_by_user = User::related_roles_select($c_instance->id);
            if (count($roles_by_user)) {
                $roles_report = new Markup('ul', ['data-type' => 'roles']);
                $roles = Role::get_all();
                foreach ($roles_by_user as $c_role_name) {
                    $roles_report->child_insert(
                        new Markup('li', ['data-name' => $c_role_name], [
                            new Text($roles[$c_role_name]->title ?? 'n/a', [], $origin->is_apply_translation), ' ('.$c_role_name.')'
                        ]), $c_role_name
                    );
                }
                   return $roles_report;
            } else return '';
        }     else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'id']);
    }

    static function handler__user__permissions($c_cell_id, $c_row, $c_instance, $origin) {
        if (array_key_exists('id', $c_instance->values_get())) {
            $roles_by_user = User::related_roles_select($c_instance->id);
            if (count($roles_by_user)) {
                $permissions_report = new Markup('ul', ['data-type' => 'permissions']);
                $permissions_by_roles = Role::related_permissions_by_roles_select($roles_by_user);
                $permissions = Permission::get_all();
                if (count($permissions_by_roles)) {
                    foreach ($permissions_by_roles as $c_permission_name) {
                        $permissions_report->child_insert(
                            new Markup('li', ['data-name' => $c_permission_name], [
                                new Text($permissions[$c_permission_name]->title ?? 'n/a', [], $origin->is_apply_translation), ' ('.$c_permission_name.')'
                            ]), $c_permission_name
                        );
                    }
                       return $permissions_report;
                } else return '';
            }     else return '';
        }         else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'id']);
    }

    #####################################
    ### handlers for 'session' entity ###
    #####################################

    static function handler__session__is_current($c_cell_id, $c_row, $c_instance, $origin) {
        if (array_key_exists('id', $c_instance->values_get())) {
            return Core::format_logic(
                 $c_instance->id === Session::select()->id
            );
        } else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'id']);
    }

    static function handler__session__data($c_cell_id, $c_row, $c_instance, $origin) {
        if (array_key_exists('data', $c_instance->values_get())) {
            if (!empty($c_instance->data->user_agent)) {
                $report = new Node();
                $report->child_insert(new Markup('h3', [], new Text('User agent', [], $origin->is_apply_translation)), 'user_agent_title');
                $report->child_insert(new Text_simple(Core::html_entity_encode($c_instance->data->user_agent)), 'user_agent_report');
                   return $report;
            } else return '';
        }     else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'data']);
    }

}
