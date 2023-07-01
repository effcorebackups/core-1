<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Template_node extends Template {

    public $pointers = [];

    function &target_get($name, $get_parent = false) {
        $dpath = $this->pointers[$name];
        $pointers = Core::dpath_get_pointers($this->data->children, $dpath, true);
        if ($get_parent) return $pointers[count($pointers) - 2];
        else             return $pointers[count($pointers) - 1];
    }

    function render() {
        foreach ($this->args as $c_key => $c_value) {
            $c_target_parent = &$this->target_get($c_key, true);
            Core::arrobj_insert_value($c_target_parent, $c_key, $c_value);
        }
        return $this->data->render();
    }

    ###########################
    ### static declarations ###
    ###########################

    static function copied_properties_get() {
        return ['pointers' => 'pointers'] + parent::copied_properties_get();
    }

}
