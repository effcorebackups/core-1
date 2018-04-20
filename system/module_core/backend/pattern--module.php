<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class module {

  public $id;
  public $title;
  public $description;
  public $version;
  public $state;
  public $path;

  function get_id()          {return $this->id;}
  function get_title()       {return $this->title;}
  function get_description() {return $this->description;}
  function get_version()     {return $this->version;}
  function get_state()       {return $this->state;}
  function get_path()        {return $this->path;}

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function init() {
    static::$cache = storage::get('files')->select('module');
  }

  static function get($module_id) {
    if   (!static::$cache) static::init();
    return static::$cache[$module_id];
  }

  static function get_all() {
    if   (!static::$cache) static::init();
    return static::$cache;
  }

}}