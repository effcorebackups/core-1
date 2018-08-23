<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class template {

  public $name;
  public $type;
  public $args = [];
  public $data;

  function __construct($name, $args = []) {
    $this->name = $name;
  # prepare arguments
    foreach ($args as $c_name => $c_value) {
      static::arg_set($c_name, $c_value);
    }
  # prepare additional properties
    $info = static::get($name);
    $this->type = $info->type;
    switch ($this->type) {
      case 'file':
        $path = module::get($info->module_id)->path_get().$info->path;
        $file = new file($path);
        $this->data = $file->load();
        return $this;
      case 'text':
        $this->data = $info->data;
        return $this;
      case 'code':
        $this->handler = $info->handler;
        return $this;
    }
  }

  function arg_set($name, $value) {
    $this->args[$name] = $value;
  }

  function render() {
    if ($this->type == 'text' ||
        $this->type == 'file') {
      $rendered = $this->data;
      $rendered = preg_replace_callback('%(?<spacer>[ ]*)'.
                                         '(?<prefix>\\%\\%_)'.
                                         '(?<name>[a-z0-9_]+)'.
                                         '(?<args>\\{[a-z0-9_,]+\\}|)%S', function($c_match) {
        return isset($c_match['prefix']) &&
               isset($c_match['name']) &&
               isset($this->args[$c_match['name']]) &&
                     $this->args[$c_match['name']] !== '' ? $c_match['spacer'].
                     $this->args[$c_match['name']] : '';
      }, $rendered);
      return $rendered;
    }
    if ($this->type == 'code') {
      return call_user_func($this->handler, $this->args);
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function init() {
    foreach (storage::get('files')->select('templates') as $c_module_id => $c_templates) {
      foreach ($c_templates as $c_row_id => $c_template) {
        if (isset(static::$cache[$c_template->name])) console::log_about_duplicate_add('template', $c_template->name);
        static::$cache[$c_template->name] = $c_template;
        static::$cache[$c_template->name]->module_id = $c_module_id;
      }
    }
  }

  static function get($row_id) {
    if   (!static::$cache) static::init();
    return static::$cache[$row_id] ?? null;
  }

  static function all_get() {
    if   (!static::$cache) static::init();
    return static::$cache;
  }

}}