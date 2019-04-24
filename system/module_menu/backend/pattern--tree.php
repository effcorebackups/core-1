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
  public $is_nosql = true;

  function __construct($title = '', $id = null, $access = null, $attributes = [], $weight = 0) {
    if ($title ) $this->title  = $title;
    if ($id    ) $this->id     = $id;
    if ($access) $this->access = $access;
    parent::__construct($attributes, [], $weight);
  }

  function build() {
    foreach (tree_item::select_all() as $c_item) {
      if ($c_item->id_parent == 'M:'.$this->id) {
        $this->child_insert($c_item, $c_item->id);
        $c_item->build();
      }
    }
  }

  function render() {
    if ($this->attribute_select('id') == null && $this->id)
        $this->attribute_insert('id',    'tree-'.$this->id);
    if (static::$cache == null) static::init();
    if ($this->access === null || access::check($this->access)) {
      if ($this->children_select_count() == 0)
          $this->build();
      return parent::render();
    }
  }

  function render_self() {
    if ($this->title) {
      switch ($this->title_state) {
        case 'cutted': return '';
        case 'hidden': return (new markup('h2', ['class' => ['hidden' => 'hidden']], $this->title))->render();
        default:       return (new markup('h2', [],                                  $this->title))->render();
      }
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    foreach (storage::get('files')->select('trees') as $c_module_id => $c_trees) {
      foreach ($c_trees as $c_row_id => $c_tree) {
        if (isset(static::$cache[$c_tree->id])) console::log_insert_about_duplicate('tree', $c_tree->id, $c_module_id);
        static::$cache[$c_tree->id] = $c_tree;
        static::$cache[$c_tree->id]->module_id = $c_module_id;
      }
    }
  # load from storage
    foreach (entity::get('tree')->instances_select() as $c_instance) {
      $c_tree = new static($c_instance->title, $c_instance->id, unserialize($c_instance->access), [], 0);
      static::$cache[$c_tree->id] = $c_tree;
      static::$cache[$c_tree->id]->module_id = 'menu';
      static::$cache[$c_tree->id]->is_nosql = false;
    }
  }

  static function select_all($is_only_nosql = false) {
    if       (static::$cache == null) static::init();
    $result = static::$cache ?? [];
    if ($is_only_nosql)
      foreach ($result as $c_id => $c_item)
        if ($c_item->is_nosql == false)
          unset($result[$c_id]);
    return $result;
  }

  static function select($id) {
    if    (static::$cache == null) static::init();
    return static::$cache[$id] ?? null;
  }

  static function select_parent($id_parent) {
    if ($id_parent[0] == 'M' &&
        $id_parent[1] == ':')
         return static   ::select(substr($id_parent, 2));
    else return tree_item::select(       $id_parent    );
  }

  static function insert($title = '', $id, $access = null, $attributes = [], $weight = 0, $module_id = null) {
    $new_tree = new static($title, $id, $access, $attributes, $weight);
    if    (static::$cache == null) static::init();
           static::$cache[$id] = $new_tree;
           static::$cache[$id]->module_id = $module_id;
           static::$cache[$id]->is_nosql = false;
    return static::$cache[$id];
  }

  static function delete($id) {
    if   (static::$cache == null) static::init();
    unset(static::$cache[$id]);
  }

}}