<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class message {

  static protected $cache;

  static function init($reset = false) {
    if (static::$cache == null || $reset)
        static::$cache = [];
  }

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function cleaning($id_session = null) {
    if ($id_session) $condition = ['id_!f'      => 'id_session', '=', 'id_!v'      => $id_session         ];
    else             $condition = ['expired_!f' => 'expired',    '<', 'expired_!v' => core::datetime_get()];
    entity::get('message')->instances_delete([
      'conditions' => $condition
    ]);
  }

  static function select_all($with_storage = true) {
    static::init();
    if ($with_storage) {
      $storage = storage::get(entity::get('message')->storage_name);
      if ($storage->is_available()) {
        foreach (static::select_from_storage() as $c_type => $c_messages) {
          if (!isset(static::$cache[$c_type]))
                     static::$cache[$c_type] = [];
          foreach ($c_messages as $c_message) {
            static::$cache[$c_type][] = $c_message;
          }
        }
      }
    }
    return static::$cache;
  }

  static function select_from_storage() {
    $result = [];
    $instances = entity::get('message')->instances_select(['conditions' => [
      'id_!f'    => 'id_session',
      'operator' => '=',
      'id_!v'    => session::id_get()
    ]]);
    if (count($instances)) {
      foreach ($instances as $c_instance)
        $result[$c_instance->type][] = unserialize($c_instance->data);
      static::cleaning(
        session::id_get()
      );
    }
    return $result;
  }

  static function insert($message, $type = 'ok') {
    static::init();
    if (!isset(static::$cache[$type]))
               static::$cache[$type] = [];
    static::$cache[$type][] = is_string($message) ?
                               new text($message) :
                                        $message;
  }

  static function insert_to_storage($message, $type = 'ok', $period = 30) {
    (new instance('message', [
      'id_session' => session::id_get(),
      'type'       => $type,
      'expired'    => core::datetime_get('+'.$period.' second'),
      'data'       => serialize(is_string($message) ?
                                 new text($message) :
                                          $message)
    ]))->insert();
  }

  static function markup_get() {
    $messages = new markup('x-messages');
    $non_duplicates = [];
    foreach (static::select_all() as $c_type => $c_messages) {
      if (!$messages->child_select($c_type))
           $messages->child_insert(new markup('ul', ['class' => [$c_type => $c_type]]), $c_type);
      if (!isset($non_duplicates[$c_type]))
                 $non_duplicates[$c_type] = [];
      $c_grpoup = $messages->child_select($c_type);
      foreach ($c_messages as $c_message) {
        if (!in_array($c_message->render(), $non_duplicates[$c_type])) {
          $non_duplicates[$c_type][] = $c_message->render();
          if ($c_type == 'error' || $c_type == 'warning')
               $c_grpoup->child_insert(new markup('li', [], new markup('p', ['role' => 'alert'], $c_message)));
          else $c_grpoup->child_insert(new markup('li', [], new markup('p', [                 ], $c_message)));
        }
      }
    }
    return $messages->children_select_count() ?
           $messages : new node();
  }

}}