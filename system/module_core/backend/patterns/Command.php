<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Command {

    function run($name, $args = []) {
        static::init();
        call_user_func_array(
            static::$cache[$name]->handler, ['args' => $args]
        );
    }

    ###########################
    ### static declarations ###
    ###########################

    protected static $cache;

    static function cache_cleaning() {
        static::$cache = null;
    }

    static function init() {
        if (static::$cache === null) {
            foreach (Storage::get('data')->select_array('commands') as $c_module_id => $c_commands) {
                foreach ($c_commands as $c_command) {
                    if (isset(static::$cache[$c_command->name])) Console::report_about_duplicate('commands', $c_command->name, $c_module_id, static::$cache[$c_command->name]);
                              static::$cache[$c_command->name] = $c_command;
                              static::$cache[$c_command->name]->origin = 'nosql';
                              static::$cache[$c_command->name]->module_id = $c_module_id;
                }
            }
        }
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function get($name) {
        static::init();
        return static::$cache[$name] ?? null;
    }

    static function get_all() {
        static::init();
        return static::$cache;
    }

}
