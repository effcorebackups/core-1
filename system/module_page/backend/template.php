<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class template implements has_cache_cleaning {

  public $name;
  public $data;
  public $args = [];
  public $module_id;

  function __construct($name, $args = []) {
    $this->name = $name;
    foreach ($args as $c_name => $c_value) {
      $this->arg_set($c_name, $c_value);
    }
  }

  function arg_set($name, $value) {
    $this->args[$name] = $value;
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    foreach (storage::get('files')->select('templates') as $c_module_id => $c_templates) {
      foreach ($c_templates as $c_row_id => $c_template) {
        if (isset(static::$cache[$c_template->name])) console::log_about_duplicate_insert('template', $c_template->name);
        static::$cache[$c_template->name] = $c_template;
        static::$cache[$c_template->name]->module_id = $c_module_id;
      }
    }
  }

  static function get($row_id) {
    if    (static::$cache == null) static::init();
    return static::$cache[$row_id] ?? null;
  }

  static function all_get() {
    if    (static::$cache == null) static::init();
    return static::$cache;
  }

  static function make_new($name, $args = []) {
    $result = null;
    $template = static::get($name);
    if ($template->type == 'text') $result = new template_text($name, $args);
    if ($template->type == 'file') $result = new template_file($name, $args);
    if ($template->type == 'code') $result = new template_code($name, $args);
    if ($template->type == 'node') $result = new template_node($name, $args);
    if (isset($template->module_id)) $result->module_id = $template->module_id; # for each type
    if (isset($template->data     )) $result->data      = $template->data;      # for each type
    if (isset($template->path     )) $result->path      = $template->path;      # for file type
    if (isset($template->handler  )) $result->handler   = $template->handler;   # for code type
    if (isset($template->pointers )) $result->pointers  = $template->pointers;  # for node type
    return $result;
  }

}}