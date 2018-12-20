<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
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
    return static::$data[$name] ?? null;
  }

  static function update($name, $data, $sub_dirs = '', $info = null) {
    static::$data[$name] = $data;
    $file = new file(static::directory.$sub_dirs.static::type.'--'.$name.'.php');
    if ($info) static::$info[$name] = $info;
    if (file::mkdir_if_not_exist($file->dirs_get()) &&
                     is_writable($file->dirs_get())) {
      $file->data_set(
        '<?php'.nl.nl.'namespace '.__NAMESPACE__.' { # '.static::type.' for '.$name.nl.nl.($info ?
           core::data_to_code($info, '  '.core::structure_part_name_get(static::class).'::$info[\''.$name.'\']') : '').
           core::data_to_code($data, '  '.core::structure_part_name_get(static::class).'::$data[\''.$name.'\']').nl.
        '}');
      if (!$file->save()) {
        static::message_insert_show($file);
        return false;
      }
      if (function_exists('opcache_invalidate')) {
      # reset opcache before load related dynamic files (styles, scripts and etc.)
        opcache_invalidate($file->path_get());
      }
      return true;
    } else {
      static::message_insert_show($file);
      return false;
    }
  }

  static function delete($name, $sub_dirs = '') {
    if (isset(static::$data[$name]))
        unset(static::$data[$name]);
    $file = new file(static::directory.$sub_dirs.static::type.'--'.$name.'.php');
    if ($file->is_exist()) {
      $result = @unlink($file->path_get());
      if   (!$result) static::message_delete_show($file);
      return $result;
    }
  }

  static function message_insert_show($file) {
    message::insert(
      'Can not insert or update file "'.$file->file_get().'" in the directory "'.$file->dirs_relative_get().'"!'.br.
      'Check file (if exists) and directory permissions.', 'error'
    );
  }

  static function message_delete_show($file) {
    message::insert(
      'Can not delete file "'.$file->file_get().'" in the directory "'.$file->dirs_relative_get().'"!'.br.
      'Check directory permissions.', 'error'
    );
  }

}}