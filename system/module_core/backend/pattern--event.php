<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class event implements should_clear_cache_after_on_install {

  public $for;
  public $handler;
  public $weight = 0;

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    console::log_insert('event', 'init.', 'event system was initialized', '-');
    foreach (storage::get('files')->select('events') as $c_module_id => $c_type_group) {
      foreach ($c_type_group as $c_type => $c_events) {
        foreach ($c_events as $c_row_id => $c_event) {
          $c_event->module_id = $c_module_id;
          static::$cache[$c_type][] = $c_event;
        }
      }
    }
    foreach (static::$cache as &$c_group) {
      if (count($c_group) > 1) {
        core::array_sort_by_weight($c_group);
      }
    }
  }

  static function all_get() {
    if    (static::$cache == null) static::init();
    return static::$cache;
  }

  static function start($type, $id = null, $args = []) {
    $result = [];
    if (!empty(static::all_get()[$type])) {
      foreach (static::all_get()[$type] as $c_event) {
        if ($id == $c_event->for || $id == null) {
          timer::tap('event call: '.$c_event->for);
          $result[$c_event->handler][] = $c_return = call_user_func_array($c_event->handler, $args);
          timer::tap('event call: '.$c_event->for);
          console::log_insert('event', 'call', ltrim($c_event->handler, '\\'), $c_return ? 'ok' : '-',
            timer::period_get('event call: '.$c_event->for, -1, -2)
          );
        }
      }
    }
    return $result;
  }

}}