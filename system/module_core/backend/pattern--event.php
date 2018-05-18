<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class event {

  public $for;
  public $handler;
  public $weight = 0;

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function init() {
    console::add_log('event', 'init.', 'event system was initialized', '-');
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