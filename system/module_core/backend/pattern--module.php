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

  function id_get()          {return $this->id;}
  function title_get()       {return $this->title;}
  function description_get() {return $this->description;}
  function version_get()     {return $this->version;}
  function state_get()       {return $this->state;}
  function path_get()        {return $this->path;}

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

  static function all_get() {
    if   (!static::$cache) static::init();
    return static::$cache;
  }

}}