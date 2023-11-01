<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

abstract class Cache extends Dynamic {

    const DIRECTORY = parent::DIRECTORY.'cache/';
    public static $info = []; # own cache info space
    public static $data = []; # own cache data space

    static function cleaning() {
        static::$info = [];
        static::$data = [];
        foreach (Directory::items_select(static::DIRECTORY, '', true) as $c_path => $c_object) {
            if ($c_path !== static::DIRECTORY.'readme.md') {
                if ($c_object instanceof File) {
                    if (!File::delete($c_path)) {
                        $c_path_relative = (new File($c_path))->path_get_relative();
                        Message::insert(new Text_multiline([
                            'File "%%_file" was not deleted!',
                            'Directory permissions are too strict!'], [
                            'file' => $c_path_relative]), 'error'
                        );
                    }
                } else {
                    Directory::delete($c_path);
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
        static::cleaning();                              # delete dynamic/cache/*.php
        Core::structures_select($modules_to_include);    # create dynamic/cache/structures.php
        Storage_Data::cache_update($modules_to_include); # create dynamic/cache/data--*.php
        Core::structures_cache_cleaning_after_install(); # method *::cache_cleaning() call for each class which implements "cache_cleaning_after_install"
    }

}
