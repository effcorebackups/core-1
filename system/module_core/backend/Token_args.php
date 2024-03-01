<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Token_args {

    public $text = null;
    public $args = [];
    public $args_by_name = [];

    function __construct($text) {
        $this->text = $text;
        if (strlen($text)) {
            $this->args = preg_split('%(?<!\\\\)\\|%S', $text);
            foreach ($this->args as $c_id => $c_arg) {
                $c_arg = static::arg_decode($c_arg);
                if (str_contains($c_arg, '=')) {
                    $c_matches = [];
                    preg_match('%^(?<name>[a-z0-9_]{1,64})=(?<value>.*)$%S', $c_arg, $c_matches);
                    if (array_key_exists('name', $c_matches) && array_key_exists('value', $c_matches)) {
                        $this->args_by_name[$c_matches['name']] = $c_matches['value'];
                    }
                }
                $this->args[$c_id] = $c_arg;
            }
        }
    }

    function get_count() {
        return count($this->args);
    }

    function get($num) {
        if ($num < 0) $num = count($this->args) + $num;
        return $this->args[$num] ?? null;
    }

    function get_all() {
        return $this->args;
    }

    function get_named($name) {
        return $this->args_by_name[$name] ?? null;
    }

    function get_named_all() {
        return $this->args_by_name;
    }

    ###########################
    ### static declarations ###
    ###########################

    static function arg_decode($text) {
        return str_replace(['\\(', '\\)', '\\|'], ['(', ')', '|'], $text);
    }

}
