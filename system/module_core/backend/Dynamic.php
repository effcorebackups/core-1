<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

abstract class Dynamic {

    const DIRECTORY = DIR_DYNAMIC;
    const DIR_FILES = DIR_DYNAMIC.'files/';
    public static $info = [];
    public static $data = [];

    static function get_file_by_name($name, $sub_dirs = '') {
        $name = Security::sanitize_file_part($name);
        return new File(static::DIRECTORY.$sub_dirs.$name.'.php');
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
        if (Directory::create($file->dirs_get()) && Directory::is_writable($file->dirs_get())) {
            # make '*.data'-file content
            $file->data_set('<?php'.NL.NL.'namespace effcore;'.NL.NL.'# '.$name.NL.NL.($info ?
                Core::data_to_code($info, Core::structure_get_part_name(static::class).'::$info[\''.$name.'\']') : '').
                Core::data_to_code($data, Core::structure_get_part_name(static::class).'::$data[\''.$name.'\']')
            );
            if (!$file->save()) {
                Message::insert(new Text_multiline([
                    'File "%%_file" was not written to disc!',
                    'File permissions are too strict!'], [
                    'file' => $file->path_get_relative()]), 'error'
                );
                return false;
            }
            # reset OPCache before load related dynamic files (styles, scripts and etc.)
            if (function_exists('opcache_invalidate')) {
                @opcache_invalidate($file->path_get());
            }
            return true;
        } else {
            Message::insert(new Text_multiline([
                'File "%%_file" was not written to disc!',
                'Directory permissions are too strict!'], [
                'file' => $file->path_get_relative()]), 'error'
            );
            return false;
        }
    }

    static function delete($name, $sub_dirs = '') {
        if (isset(static::$data[$name]))
            unset(static::$data[$name]);
        $file = static::get_file_by_name($name, $sub_dirs);
        if ($file->is_exists()) {
            $result = File::delete($file->path_get());
            if (!$result) {
                Message::insert(new Text_multiline([
                    'File "%%_file" was not deleted!',
                    'Directory permissions are too strict!'], [
                    'file' => $file->path_get_relative()]), 'error'
                );
            }
            return $result;
        }
    }

}
