<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class group_checkboxes extends group_radiobuttons {

    public $attributes = [
        'data-type' => 'checkboxes',
        'role'      => 'group'];
    public $field_class = '\\effcore\\field_checkbox';
    public $field_attributes = [
        'data-type' => 'checkbox'
    ];

    function value_get($options = []) { # @return: array | serialize(array)
        $result = [];
        foreach ($this->children_select() as $c_id => $c_child) {
            if (is_object($c_child)                    &&
                $c_child instanceof $this->field_class &&
                $c_child->checked_get() === true) {
                $result[$c_id] = $c_child->value_get();
            }
        }
        if (!empty($options['return_serialized']))
             return serialize($result);
        else return           $result;
    }

    function value_set($value, $options = []) {
        $this->value_set_initial($value);
        if (core::data_is_serialized($value)) $value = unserialize($value);
        if ($value === null) $value = [];
        if ($value ===  '' ) $value = [];
        if (is_array($value)) {
            foreach ($this->children_select() as $c_child) if (is_object($c_child) && $c_child instanceof $this->field_class) $c_child->checked_set(false);
            foreach ($this->children_select() as $c_child) if (is_object($c_child) && $c_child instanceof $this->field_class) {
                if (is_array($value) && in_array($c_child->value_get(), $value)) {
                    $c_child->checked_set(true);
                }
            }
        }
    }

    ###########################
    ### static declarations ###
    ###########################

    static function validate_required_any($group, $form, $npath) {
        if ($group->required_any && count($group->items) !== count($group->disabled) && $group->value_get() === []) {
            $group->error_set_in();
            $form->error_set(
                'Group "%%_title" should contain at least one selected item!', ['title' => (new text($group->title))->render() ]
            );
        } else {
            return true;
        }
    }

}
