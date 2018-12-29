<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class message {

  static protected $cache;

  static function init() {
    static::$cache = [];
  }

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function select_all() {
    if    (static::$cache == null) static::init();
    return static::$cache;
  }

  static function insert($message, $type = 'ok') {
    if (static::$cache == null) static::init();
    if (!isset(static::$cache[$type]))
               static::$cache[$type] = [];
    if (!in_array($message, static::$cache[$type]))
                            static::$cache[$type][] = $message;
  }

  static function markup_get() {
    $messages = new markup('x-messages');
    foreach (static::select_all() as $c_type => $c_messages) {
      if (!$messages->child_select($c_type))
           $messages->child_insert(new markup('ul', ['class' => [$c_type => $c_type]]), $c_type);
      $c_grpoup = $messages->child_select($c_type);
      foreach ($c_messages as $c_message) {
        $c_grpoup->child_insert(
          new markup('li', [], $c_message)
        );
      }
    }
    return $messages->children_count() ?
           $messages : new node();
  }

}}