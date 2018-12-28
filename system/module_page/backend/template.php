<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class template implements has_cache_cleaning {

  public $name;
  public $type;
  public $data;
  public $args = [];

  # public $path     - for type == 'file'
  # public $handler  - for type == 'code'
  # public $pointers - for type == 'node'

  function __construct($name, $args = []) {
    $template = static::get($name);
  # copy all properties
    foreach ($template as $c_property => $c_value) {
      $this->{$c_property} = $c_value;
    }
  # special cases
    if ($this->type == 'file') {
      $path = module::get($template->module_id)->path.$template->path;
      $file = new file($path);
      $this->data = $file->load();
    }
  # prepare argument
    foreach ($args as $c_name => $c_value) {
      static::arg_set($c_name,   $c_value);
    }
  # return new instance of template
    return $this;
  }

  function arg_set($name, $value) {
    $this->args[$name] = $value;
  }

  function render() {
    switch ($this->type) {
      case 'text':
      case 'file':
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
        },     $rendered);
                   return $rendered;
      case 'code': return call_user_func($this->handler, $this->args);
      case 'node':
        foreach ($this->args as $c_name => $c_value) {
          $c_dpath = $this->pointers[$c_name];
          $c_pointers = core::dpath_pointers_get($this->data->children, $c_dpath, true);
          $c_pointer_last = &$c_pointers[count($c_pointers) - 1];
          core::arrobj_value_insert($c_pointer_last, $c_name, $c_value);
        }
        return $this->data->render();
    }
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

}}