<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class template {

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

  static function get_copied_properties() {
    return [
      'module_id' => 'module_id',
      'data'      => 'data'
    ];
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
    $template = static::get($name);
    $class_name = __NAMESPACE__.'\\template_'.$template->type;
    $result = new $class_name($name, $args);
    foreach ($class_name::get_copied_properties() as $c_property_name) {
      if (property_exists($template, $c_property_name)) {
        $result->{$c_property_name} = $template->{$c_property_name};
      }
    }
    return $result;
  }

}}