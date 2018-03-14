<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class dynamic {

  static public $type = 'data';
  static public $directory = dir_dynamic.'data/';
  static public $info = [];
  static public $data = [];

  static function select_info() {
    return static::$info;
  }

  static function select($name) {
    if (!isset(static::$data[$name])) {
      $file = new file(static::$directory.static::$type.'--'.$name.'.php');
      if ($file->is_exist()) {
        $file->insert();
      }
    }
    return isset(static::$data[$name]) ?
                 static::$data[$name] : null;
  }

  static function update($name, $data, $info = null) {
    static::$data[$name] = $data;
    $file = new file(static::$directory.static::$type.'--'.$name.'.php');
    if ($info) static::$info[$name] = $info;
    if (is_writable($file->get_dirs()) && ((
        is_writable($file->get_path()) && $file->is_exist()) ||
                                          $file->is_exist() == false)) {
      $file->set_data(
        '<?php'.nl.nl.'namespace effcore { # '.static::$type.' for '.$name.nl.nl.($info ?
           factory::data_export($info, '  '.factory::class_get_short_name(static::class).'::$info[\''.$name.'\']') : '').
           factory::data_export($data, '  '.factory::class_get_short_name(static::class).'::$data[\''.$name.'\']').nl.
        '}');
      $file->save();
      if (function_exists('opcache_invalidate')) {
        opcache_invalidate($file->get_path());
      }
      return true;
    } else {
      message::insert(
        'Can not write file "'.$file->get_file().'" to the directory "'.$file->get_dirs_relative().'"!'.br.
        'The system cannot save dynamic file and will work slowly!'.br.
        (!is_writable($file->get_dirs()) ? 'Directory "'.$file->get_dirs_relative().'" should be writable!'.br : '').
        (!is_writable($file->get_path()) && $file->is_exist() ? 'File "'.$file->get_file().'" should be writable!' : ''), 'warning'
      );
    }
  }

  static function delete($name) {
    if (isset(static::$data[$name]))
        unset(static::$data[$name]);
    $file = new file(static::$directory.static::$type.'--'.$name.'.php');
    if ($file->is_exist()) {
      return unlink($file->get_path());
    }
  }

}}