<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Widget_Access extends Control implements Control_complex {

    public $tag_name = 'x-group';
    public $title = 'Access';
    public $title_attributes = ['data-group-title' => true];
    public $description = 'Access settings are not applicable if no one role is active!';
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
        if ($roles) {
            if (!empty($options['return_serialized']))
                 return serialize((object)['roles' => Core::array_keys_map($roles)]);
            else return           (object)['roles' => Core::array_keys_map($roles)];
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
        if ($value) {
            return new Text_multiline(
                ['Role IDs', ': ', implode(', ', $value->roles)], [], ''
            );
        } else {
            return new Text('No restrictions.');
        }
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
        # relate new controls with the widget
        $widget->controls['*roles'] = $group_roles;
        $result->child_insert($group_roles, 'group_roles');
        return $result;
    }

}
