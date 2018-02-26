<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class event {

  public $for;
  public $handler;
  public $weight = 0;

  ######################
  ### static methods ###
  ######################

  static protected $cache;

  static function init() {
    console::add_log('event', 'init.', 'event system was initialized', '-');
    foreach (storage::get('files')->select('events') as $c_module_id => $c_module_events) {
      foreach ($c_module_events as $c_row_id => $c_events) {
        foreach ($c_events as $c_event) {
          static::$cache[$c_row_id][] = $c_event;
        }
      }
    }
    foreach (static::$cache as &$c_events) {
      if (count($c_events) > 1) {
        factory::array_sort_by_weight($c_events);
      }
    }
  }

  static function get_all() {
    if   (!static::$cache) static::init();
    return static::$cache;
  }

  static function start($type, $id = null, $args = []) {
    $return = [];
    if (!empty(static::get_all()[$type])) {
      foreach (static::get_all()[$type] as $c_event) {
        if ($id == $c_event->for || $id == null) {
          timer::tap('event call: '.$c_event->for);
          $return[$c_event->handler][] = $c_return = call_user_func_array($c_event->handler, $args);
          timer::tap('event call: '.$c_event->for);
          console::add_log('event', 'call', ltrim($c_event->handler, '\\'), $c_return ? 'ok' : '-',
            timer::get_period('event call: '.$c_event->for, -1, -2)
          );
        }
      }
    }
    return $return;
  }

}}