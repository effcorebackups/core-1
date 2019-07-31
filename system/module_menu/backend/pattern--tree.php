<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class tree extends node {

  public $template = 'tree';
  public $attributes = ['role' => 'tree'];
  public $id;
  public $title = '';
  public $title_state; # hidden | cutted
  public $access;
  public $type = 'nosql'; # nosql | sql | dynamic
  public $managing_mode; # null | simple | simple-draggable

  function __construct($title = '', $id = null, $access = null, $attributes = [], $weight = 0) {
    if ($title ) $this->title  = $title;
    if ($id    ) $this->id     = $id;
    if ($access) $this->access = $access;
    parent::__construct($attributes, [], $weight);
  }

  function build() {
    if (!$this->is_builded) {
      event::start('on_tree_build_before', $this->id, [&$this]);
      $this->attribute_insert('data-id',            $this->id           );
      $this->attribute_insert('data-managing_mode', $this->managing_mode);
      foreach (tree_item::select_all_by_id_tree($this->id) as $c_item) {
        if ($c_item->id_tree   == $this->id &&
            $c_item->id_parent == null) {
          $this->child_insert($c_item, $c_item->id);
          $c_item->build();}}
      event::start('on_tree_build_after', $this->id, [&$this]);
      $this->is_builded = true;
    }
  }

  function render() {
    if (access::check($this->access)) {
      static::init();
      $this->build();
      return parent::render();
    }
  }

  function render_self() {
    if ($this->title) {
      switch ($this->title_state) {
        case 'cutted': return '';
        case 'hidden': return (new markup('h2', ['aria-hidden' => 'true'], $this->title))->render();
        default:       return (new markup('h2', [                       ], $this->title))->render();
      }
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;
  static protected $is_init_nosql = false;
  static protected $is_init___sql = false;

  static function cache_cleaning() {
    static::$cache         = null;
    static::$is_init_nosql = false;
    static::$is_init___sql = false;
  }

  static function init() {
    if (!static::$is_init_nosql) {
         static::$is_init_nosql = true;
      foreach (storage::get('files')->select('trees') as $c_module_id => $c_trees) {
        foreach ($c_trees as $c_row_id => $c_tree) {
          if (isset(static::$cache[$c_tree->id])) console::log_insert_about_duplicate('tree', $c_tree->id, $c_module_id);
          static::$cache[$c_tree->id] = $c_tree;
          static::$cache[$c_tree->id]->module_id = $c_module_id;
          static::$cache[$c_tree->id]->type = 'nosql';
        }
      }
    }
  }

  static function init_sql($id = null) {
    if ($id && isset(static::$cache[$id])) return;
    if (!static::$is_init___sql) {
         static::$is_init___sql = true;
      foreach (entity::get('tree')->instances_select() as $c_instance) {
        $c_tree = new static(
          $c_instance->title,
          $c_instance->id,
          $c_instance->access, [], 0);
        static::$cache[$c_tree->id] = $c_tree;
        static::$cache[$c_tree->id]->module_id = 'menu';
        static::$cache[$c_tree->id]->type = 'sql';
      }
    }
  }

  static function select_all($type = null) {
    if ($type == 'nosql') {static::init    ();                    }
    if ($type ==   'sql') {static::init_sql();                    }
    if ($type ==    null) {static::init    (); static::init_sql();}
    $result = static::$cache ?? [];
    if ($type)
      foreach ($result as $c_id => $c_item)
        if ($c_item->type != $type)
          unset($result[$c_id]);
    return $result;
  }

  static function select($id) {
    static::init    (   );
    static::init_sql($id);
    return static::$cache[$id] ?? null;
  }

  static function insert($title = '', $id, $access = null, $attributes = [], $weight = 0, $module_id = null) {
    static::init    (   );
    static::init_sql($id);
    $new_tree = new static($title, $id, $access, $attributes, $weight);
           static::$cache[$id] = $new_tree;
           static::$cache[$id]->module_id = $module_id;
           static::$cache[$id]->type = 'dynamic';
    return static::$cache[$id];
  }

  static function delete($id, $with_items = true) {
    static::init    (   );
    static::init_sql($id);
    if ($with_items)
      foreach (tree_item::select_all_by_id_tree($id) as $c_item)
        tree_item::delete($c_item->id, $id);
    unset(static::$cache[$id]);
  }

}}