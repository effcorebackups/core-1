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

  # public $path    - for type == 'file'
  # public $handler - for type == 'code'

  function __construct($name, $args = []) {
    $template = static::get($name);
    $this->name = $name;
    $this->type = $template->type;
    switch ($this->type) {
      case 'file': $this->data    = (new file( module::get($template->module_id)->path.$template->path ))->load(); break;
      case 'text': $this->data    = $template->data;                                                               break;
      case 'node': $this->data    = $template->data;                                                               break;
      case 'code': $this->handler = $template->handler;                                                            break;
    }
  # prepare argument
    foreach ($args as $c_name => $c_value) {
      static::arg_set($c_name,   $c_value);
    }
  # return new instance of template
    return $this;
  }

  function arg_set($name, $value) {
    switch ($this->type) {
      case 'text':
      case 'file':
      case 'code': $this->args[$name] = $value; break;
      case 'node': break;
    }
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
      case 'node': return $this->data->render();
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