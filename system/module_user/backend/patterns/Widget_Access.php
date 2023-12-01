<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Widget_Access extends Control implements Control_complex {

    public $tag_name = 'x-group';
    public $title = 'Access';
    public $title_attributes = ['data-group-title' => true];
    public $description = 'Access settings are not applied if nothing is selected!';
    public $name_complex = 'access';
    public $checked_roles = [];
    public $attributes = [
        'data-type' => 'access',
        'role'      => 'group'
    ];

    function build() {
        if (!$this->is_builded) {
            $this->child_insert(static::widget_manage_get($this), 'manage');
            $this->is_builded = true;
        }
    }

    function value_get($options = []) { # @return: null | object | serialize(object)
        $roles = $this->controls['*roles']->value_get();
        $users = $this->controls['#users']->value_get();
        if ($roles || $users) {
            $users = strlen($users) ? preg_split('%[, ]+%', $users, -1, PREG_SPLIT_NO_EMPTY) : [];
            $users = array_filter($users, function ($value) { return (bool)User::select((int)$value); });
            $users = array_map(function ($value) { return (int)$value; }, $users);
            if (!empty($options['return_serialized']))
                 return serialize((object)['roles' => Core::array_keys_map($roles), 'users' => Core::array_keys_map($users)]);
            else return           (object)['roles' => Core::array_keys_map($roles), 'users' => Core::array_keys_map($users)];
        }
    }

    function value_set($value) {
        $this->value_set_initial($value);
        if (Core::data_is_serialized($value)) $value = unserialize($value);
        if ($value === null) $value = (object)['roles' => []];
        if ($value ===  '' ) $value = (object)['roles' => []];
        if (is_object($value)) {
            $this->controls['*roles']->value_set(
                Core::array_keys_map($value->roles ?? [])
            );
            $this->controls['#users']->value_set(
                isset($value->users) && is_array($value->users) ?
                                   implode(', ', $value->users) : null
            );
        }
    }

    function name_get_complex() {
        return $this->name_complex;
    }

    function disabled_get() {
        return false;
    }

    ###########################
    ### static declarations ###
    ###########################

    static function value_to_markup($value) {
        $report = new Node();
        if ($value) {
            # roles
            if (!empty($value->roles)) {
                $roles_report = new Markup('ul', ['data-type' => 'roles']);
                $roles = Role::get_all();
                foreach ($value->roles as $c_role_name) {
                    $roles_report->child_insert(
                        new Markup('li', ['data-name' => $c_role_name], [
                            $roles[$c_role_name]->title, ' ('.$c_role_name.')'
                        ]), $c_role_name
                    );
                }
                $report->child_insert(new Markup('h3', [], 'Roles'), 'roles_title');
                $report->child_insert($roles_report, 'roles_report');
            }
            # users
            if (!empty($value->users)) {
                $users_report = new Markup('ul', ['data-type' => 'users']);
                $users = User::select_multiple($value->users);
                foreach ($value->users as $c_user_id) {
                    $users_report->child_insert(
                        new Markup('li', ['data-id' => $c_user_id], [
                            $users[$c_user_id]->nickname ?? 'n/a', ' ('.$c_user_id.')'
                        ]), $c_user_id
                    );
                }
                $report->child_insert(new Markup('h3', [], 'Users'), 'users_title');
                $report->child_insert($users_report, 'users_report');
            }
        } else {
            $report->child_insert(
                new Text('No restrictions.'), 'no_restrictions'
            );
        }
        return $report;
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function widget_manage_get($widget) {
        $result = new Node;
        # control for roles
        $group_roles = new Group_Switchers;
        $group_roles->element_attributes['name'] = $widget->name_get_complex().'__roles[]';
        $group_roles_items = [];
        foreach (Role::get_all() as $c_role)
            $group_roles_items[$c_role->id] = $c_role->title;
        $group_roles->items_set($group_roles_items);
        $group_roles->value_set(Core::array_keys_map($widget->checked_roles));
        # control for users
        $field_users = new Field_Text('User IDs');
        $field_users->cform = $widget->cform;
        $field_users->build();
        $field_users->name_set($widget->name_get_complex().'__users');
        $field_users->pattern_set('^[0-9, ]*$');
        $field_users->maxlength_set(65535);
        $field_users->required_set(false);
        # relate new controls with the widget
        $widget->controls['*roles'] = $group_roles;
        $widget->controls['#users'] = $field_users;
        $result->child_insert($group_roles, 'group_roles');
        $result->child_insert($field_users, 'field_users');
        return $result;
    }

}
