<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

abstract class Cache extends Dynamic {

    const DIRECTORY = DIR_DYNAMIC.'cache/';
    public static $info = []; # own cache info space
    public static $data = []; # own cache data space

    static function cleaning() {
        static::$info = [];
        static::$data = [];
        foreach (File::select_recursive(static::DIRECTORY, '', true) as $c_path => $c_object) {
            if ($c_path !== static::DIRECTORY.'readme.md') {
                if ($c_object instanceof File) {
                    if (!@unlink($c_path)) {
                        $c_file = new File($c_path);
                        static::message_on_error_delete($c_file);
                    }
                } else {
                    @rmdir($c_path);
                }
            }
        }
    }

    static function update($name, $data, $sub_dirs = '', $info = null) {
        if (parent::update($name, $data, $sub_dirs, $info)) {
            Console::log_insert('storage', 'cache', 'cache for '.$name.' was rebuild', 'ok');
            return true;
        }
    }

    static function update_global($modules_to_include = []) {
        static::cleaning();                                    # delete dynamic/cache/*.php
        Core::structures_select($modules_to_include);          # create dynamic/cache/structures.php
        Storage_NoSQL_data::cache_update($modules_to_include); # create dynamic/cache/data--*.php
        Core::structures_cache_cleaning_after_on_install();    # method *::cache_cleaning() call for each class which implements "Should_clear_cache_after_on_install"
    }

}
