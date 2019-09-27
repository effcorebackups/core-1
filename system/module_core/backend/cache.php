<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class cache extends dynamic {

  const directory = dir_dynamic.'cache/';
  static public $info = []; # own cache info space
  static public $data = []; # own cache data space

  static function cleaning() {
    static::$info = [];
    static::$data = [];
    foreach (file::select_recursive(static::directory, '', true) as $c_path => $c_object) {
      if ($c_path != static::directory.'readme.md') {
        if  ($c_object instanceof file)
             $c_result = @unlink($c_path);
        else             @rmdir ($c_path);
        if (!$c_result) {
          $c_file = new file($c_path);
          message::insert(
            'Can not delete file "'.$c_file->file_get().'" in the directory "'.$c_file->dirs_get_relative().'"!'.br.
            'Check directory permissions.', 'error'
          );
        }
      }
    }
  }

  static function update($name, $data, $sub_dirs = '', $info = null) {
    if (parent::update($name, $data, $sub_dirs, $info)) {
      console::log_insert('storage', 'cache', 'cache for '.$name.' was rebuilded', 'ok', 0);
    }
  }

  static function update_global($include_paths = []) {
    static::cleaning();                                 # delete dynamic/cache/*.php
    core::structures_select($include_paths);            # create dynamic/cache/structures.php
    storage_nosql_files::cache_update($include_paths);  # create dynamic/cache/data--*.php
    core::structures_cache_cleaning_after_on_install(); # method *::cache_cleaning() call for each class which implements "should_clear_cache_after_on_install"
  }

}}