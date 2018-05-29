<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class dynamic {

  const type = 'data';
  const directory = dir_dynamic;
  const dir_files = dir_dynamic.'files/';
  static public $info = [];
  static public $data = [];

  static function select_info() {
    return static::$info;
  }

  static function select($name, $sub_dirs = '') {
    if (!isset(static::$data[$name])) {
      $file = new file(static::directory.$sub_dirs.static::type.'--'.$name.'.php');
      if ($file->is_exist()) {
        $file->insert();
      }
    }
    return isset(static::$data[$name]) ?
                 static::$data[$name] : null;
  }

  static function update($name, $data, $sub_dirs = '', $info = null) {
    static::$data[$name] = $data;
    $file = new file(static::directory.$sub_dirs.static::type.'--'.$name.'.php');
    if ($info) static::$info[$name] = $info;
    if (file::mkdir_if_not_exist($file->get_dirs()) &&
                     is_writable($file->get_dirs())) {
      $file->set_data(
        '<?php'.nl.nl.'namespace effcore { # '.static::type.' for '.$name.nl.nl.($info ?
           core::data_to_code($info, '  '.core::class_get_short_name(static::class).'::$info[\''.$name.'\']') : '').
           core::data_to_code($data, '  '.core::class_get_short_name(static::class).'::$data[\''.$name.'\']').nl.
        '}');
      if (!$file->save()) {
        static::show_message($file);
        return false;
      }
      if (function_exists('opcache_invalidate')) {
        opcache_invalidate($file->get_path());
      }
      return true;
    } else {
      static::show_message($file);
      return false;
    }
  }

  static function delete($name, $sub_dirs = '') {
    if (isset(static::$data[$name]))
        unset(static::$data[$name]);
    $file = new file(static::directory.$sub_dirs.static::type.'--'.$name.'.php');
    if ($file->is_exist()) {
      return unlink($file->get_path());
    }
  }

  static function show_message($file) {
    message::insert(
      'Can not write file "'.$file->get_file().'" to the directory "'.$file->get_dirs_relative().'"!'.br.
      'Check file (if exists) and directory permissions.', 'error'
    );
  }

}}