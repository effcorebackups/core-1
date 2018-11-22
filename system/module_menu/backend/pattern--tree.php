<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class tree extends node implements has_cache_cleaning {

  public $id;
  public $title = '';
  public $title_state; # hidden | cutted
  public $template = 'tree';

  function __construct($title = '', $attributes = [], $children = [], $weight = 0) {
    if ($title) $this->title = $title;
    parent::__construct($attributes, $children, $weight);
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

  static protected $cache_trees;
  static protected $cache_tree_items;

  static function cache_cleaning() {
    static::$cache_trees      = null;
    static::$cache_tree_items = null;
    static::init();
    static::build();
  }

  static function init() {
    foreach (storage::get('files')->select('trees') as $c_module_id => $c_trees) {
      foreach ($c_trees as $c_row_id => $c_tree) {
        if (isset(static::$cache_trees[$c_tree->id])) console::log_about_duplicate_add('tree', $c_tree->id);
        static::$cache_trees[$c_tree->id] = $c_tree;
        static::$cache_trees[$c_tree->id]->module_id = $c_module_id;
      }
    }
    foreach (storage::get('files')->select('tree_items') as $c_module_id => $c_tree_items) {
      foreach ($c_tree_items as $c_row_id => $c_tree_item) {
        if (isset(static::$cache_tree_items[$c_tree_item->id])) console::log_about_duplicate_add('tree_item', $c_tree_item->id);
        static::$cache_tree_items[$c_tree_item->id] = $c_tree_item;
        static::$cache_tree_items[$c_tree_item->id]->module_id = $c_module_id;
      }
    }
  }

  static function get($id) {
    return static::$cache_trees[$id] ?? null;
  }

  static function all_get() {
    return static::$cache_trees;
  }

  static function parent_get($id_parent) {
    if ($id_parent[0] == 'M' &&
        $id_parent[1] == ':')
         return static::$cache_trees[substr($id_parent, 2)] ?? null;
    else return static::$cache_tree_items  [$id_parent]     ?? null;
  }

  static function item_select($id) {
    return static::$cache_tree_items[$id] ?? null;
  }

  static function items_select() {
    return static::$cache_tree_items;
  }

  static function item_insert($title, $id, $id_parent, $url = null, $attributes = [], $weight = 0) {
    $new_item = new tree_item($title, $id, $id_parent, $url, $attributes, $weight);
    static::$cache_tree_items[$id] = $new_item;
    static::$cache_tree_items[$id]->module_id = null;
    static::build([$new_item]);
  }

  static function item_delete($id) {
    if (isset(static::$cache_tree_items[$id])) {
      $id_parent = static::$cache_tree_items[$id]->id_parent;
             unset(static::$cache_tree_items[$id]);
      static::parent_get($id_parent)->child_delete($id);
    }
  }

  static function build($items = null) {
    foreach ($items ?: static::items_select() as $c_item) {
      if ($c_item->id_parent) {
        $c_parent = static::parent_get($c_item->id_parent);
        $c_parent->child_insert($c_item, $c_item->id);
      }
    };
  }

}}