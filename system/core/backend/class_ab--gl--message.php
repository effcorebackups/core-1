<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class message {

  static protected $cache;

  static function init() {
    static::$cache = [];
  }

  static function select_all() {
    if   (!static::$cache) static::init();
    return static::$cache;
  }

  static function insert($message, $type = 'notice') {
    if (!static::$cache) static::init();
    if (!isset(static::$cache[$type]))
               static::$cache[$type] = [];
    if (!in_array($message, static::$cache[$type])) {
      static::$cache[$type][] = $message;
    }
  }

  static function render_all() {
    $groups = [];
    foreach (static::select_all() as $c_type => $c_messages) {
      $c_grpoup = new markup('ul', ['class' => [$c_type => $c_type]]);
      foreach ($c_messages as $c_message) {
        $c_grpoup->child_insert(
          new markup('li', [], $c_message)
        );
      }
      $groups[] = $c_grpoup;
    }
    return count($groups) ? (
      new markup('x-messages', [], $groups)
    )->render() : '';
  }

}}