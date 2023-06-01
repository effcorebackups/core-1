<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

abstract class dynamic {

    const DIRECTORY = DIR_DYNAMIC;
    const DIR_FILES = DIR_DYNAMIC.'files/';
    static public $info = [];
    static public $data = [];

    static function get_file_by_name($name, $sub_dirs = '') {
        $name = core::sanitize_file_part($name);
        return new file(static::DIRECTORY.$sub_dirs.$name.'.php');
    }

    static function is_exists($name, $sub_dirs = '') {
        $file = static::get_file_by_name($name, $sub_dirs);
        return $file->is_exists();
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function select_info($name) {
        return static::$info[$name] ?? null;
    }

    static function select($name, $sub_dirs = '') {
        if (!isset(static::$data[$name])) {
            $file = static::get_file_by_name($name, $sub_dirs);
            if ($file->is_exists()) {
                $file->require();
            }
        }
        return static::$data[$name] ?? null;
    }

    static function update($name, $data, $sub_dirs = '', $info = null) {
        static::$data[$name] = $data;
        $file = static::get_file_by_name($name, $sub_dirs);
        if ($info) static::$info[$name] = $info;
        if (file::mkdir_if_not_exists($file->dirs_get()) &&
                          is_writable($file->dirs_get())) {
            $file->data_set('<?php'.NL.NL.'namespace effcore;'.NL.NL.'# '.$name.NL.NL.($info ?
                core::data_to_code($info, core::structure_get_part_name(static::class).'::$info[\''.$name.'\']') : '').
                core::data_to_code($data, core::structure_get_part_name(static::class).'::$data[\''.$name.'\']')
            );
            if (!$file->save()) {
                static::message_on_error_insert($file);
                return false;
            }
            if (function_exists('opcache_invalidate')) {
                # reset OPCache before load related dynamic files (styles, scripts and etc.)
                @opcache_invalidate($file->path_get());
            }
            return true;
        } else {
            static::message_on_error_insert($file);
            return false;
        }
    }

    static function delete($name, $sub_dirs = '') {
        if (isset(static::$data[$name]))
            unset(static::$data[$name]);
        $file = static::get_file_by_name($name, $sub_dirs);
        if ($file->is_exists()) {
            $result = @unlink($file->path_get());
            if   (!$result) static::message_on_error_delete($file);
            return $result;
        }
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function message_on_error_insert($file) {
        message::insert(new text_multiline([
            'File "%%_file" was not written to disc!',
            'File permissions (if the file exists) and directory permissions should be checked.'], [
            'file' => $file->path_get_relative()]), 'error'
        );
    }

    static function message_on_error_delete($file) {
        message::insert(new text_multiline([
            'File "%%_file" was not deleted!',
            'Directory permissions should be checked.'], [
            'file' => $file->path_get_relative()]), 'error'
        );
    }

}
