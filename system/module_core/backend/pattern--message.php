<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class message extends markup {

  public $tag_name = 'x-messages';

  function build() {
    if (!$this->is_builded) {
      $non_duplicates = [];
      foreach (static::select_all() as $c_type => $c_messages) {
        if (!$this->child_select($c_type))
             $this->child_insert(new markup('ul', ['data-type' => $c_type]), $c_type);
        if (!isset($non_duplicates[$c_type]))
                   $non_duplicates[$c_type] = [];
        $c_grpoup = $this->child_select($c_type);
        foreach ($c_messages as $c_message) {
          if (!in_array($c_message->render(), $non_duplicates[$c_type])) {
            $non_duplicates[$c_type][] = $c_message->render();
            if ($c_type === 'error' || $c_type === 'warning')
                 $c_grpoup->child_insert(new markup('li', [], new markup('p', ['role' => 'alert'], $c_message)));
            else $c_grpoup->child_insert(new markup('li', [], new markup('p', [                 ], $c_message)));
          }
        }
      }
      $this->is_builded = true;
    }
  }

  function render() {
    $this->build();
    return $this->children_select_count() ?
           parent::render() : '';
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function init() {
    if (static::$cache === null)
        static::$cache = [];
  }

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function cleaning($id_session = null) {
    if ($id_session) $condition = [     'id_!f' => 'id_session', 'operator' => '=',      'id_!v' => $id_session];
    else             $condition = ['expired_!f' => 'expired',    'operator' => '<', 'expired_!v' => time()     ];
    entity::get('message')->instances_delete([
      'conditions' => $condition
    ]);
  }

  static function select_all($with_storage = true) {
    static::init();
    if ($with_storage) {
      if (entity::get('message')->storage_get()->is_available()) {
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
      'id_!f'       => 'id_session',
      'id_operator' => '=',
      'id_!v'       => session::id_get()
    ]]);
    if (count($instances)) {
      foreach ($instances as $c_instance)
        $result[$c_instance->type][] = $c_instance->data;
      static::cleaning(session::id_get());
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
      'expired'    => time() + $period,
      'data'       => is_string($message) ?
                       new text($message) : $message
    ]))->insert();
  }

}}