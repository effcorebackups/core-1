<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class template {

  public $name;
  public $type;
  public $vars = [];
  public $markup;

  function __construct($name, $vars = []) {
    $this->name = $name;
  # save vars
    foreach ($vars as $c_name => $c_value) {
      static::set_var($c_name, $c_value);
    }
  # prepare template
    $info = static::get($name);
    if ($info) {
      switch ($info->type) {
        case 'file':
          $path = module::get($info->module_id)->get_path().$info->path;
          $file = new file($path);
          $this->markup = $file->load();
          return $this;
        case 'inline':
          $this->markup = $info->markup;
          return $this;
      }
    }
  }

  function set_var($name, $value) {
    $this->vars[$name] = $value;
  }

  function render() {
    $rendered = $this->markup;
    $rendered = preg_replace_callback('%(?<spacer>[ ]*)'.
                                       '(?<prefix>\\%\\%_)'.
                                       '(?<name>[a-z0-9_]+)'.
                                       '(?<args>\\{[a-z0-9_,]+\\}|)%S', function($matches) {
      return isset($matches['prefix']) &&
             isset($matches['name']) &&
             isset($this->vars[$matches['name']]) &&
                   $this->vars[$matches['name']] !== '' ? $matches['spacer'].
                   $this->vars[$matches['name']] : '';
    }, $rendered);
    return $rendered;
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function init() {
    foreach (storage::get('files')->select('templates') as $c_module_id => $c_templates) {
      foreach ($c_templates as $c_row_id => $c_template) {
        static::$cache[$c_row_id] = $c_template;
        static::$cache[$c_row_id]->module_id = $c_module_id;
      }
    }
  }

  static function get($row_id) {
    if         (!static::$cache) static::init();
    return isset(static::$cache[$row_id]) ?
                 static::$cache[$row_id] : null;
  }

  static function get_all() {
    if   (!static::$cache) static::init();
    return static::$cache;
  }

}}