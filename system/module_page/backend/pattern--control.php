<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class control extends container {

    public $name_prefix = '';
    public $cform;
    public $entity_name;
    public $entity_field_name;
    public $is_validate_uniqueness = false;
    protected $initial_value;

    function value_get()       {} # abstract method
    function value_set($value) {  # abstract method
        $this->value_set_initial($value);
    }

    function value_get_initial() {
        return $this->initial_value;
    }

    function value_set_initial($value, $reset = false) {
        if ($this->initial_value === null || $reset === true)
            $this->initial_value = $value;
    }

    function value_is_unique_in_storage_sql($value) { # @return: null | false | instance
        if ($this->entity_name &&
            $this->entity_field_name) {
            $result = entity::get($this->entity_name)->instances_select(['conditions' => [
                'field_!f' => $this->entity_field_name,
                'operator' => '=',
                'field_!v' => $value], 'limit' => 1]);
            return reset($result);
        }
    }

}
