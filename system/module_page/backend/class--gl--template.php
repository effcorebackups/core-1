<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class template {

  public $name;
  public $markup;
  public $vars = [];

  function __construct($name, $vars = []) {
    $this->name = $name;
  # save vars
    foreach ($vars as $c_name => $c_value) {
      static::set_var($c_name, $c_value);
    }
  # find template
    foreach (static::get_templates() as $c_module_id => $c_templates) {
      foreach ($c_templates as $c_name => $c_info) {
        if ($name == $c_name) {
          switch ($c_info->type) {
            case 'file':
              $path = module::get($c_module_id)->get_path().$c_info->path;
              $file = new file($path);
              $this->markup = $file->load(false);
              return $this;
            case 'inline':
              $this->markup = $c_info->markup;
              return $this;
          }
        }
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
    static::$cache = storage::get('files')->select('templates');
  }

  static function get_templates() {
    if   (!static::$cache) static::init();
    return static::$cache;
  }

}}