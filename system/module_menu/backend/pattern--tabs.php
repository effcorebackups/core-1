<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class tabs extends node {

  public $id;
  public $template = 'tabs';
  public $attributes = ['role' => 'tablist'];

  function __construct($id = null, $attributes = [], $weight = 0) {
    if ($id) $this->id = $id;
    parent::__construct($attributes, [], $weight);
  }

  function build() {
    foreach (tabs_item::select_all() as $c_item) {
      if ($c_item->id_parent == 'T:'.$this->id) {
        $this->child_insert($c_item, $c_item->id);
        $c_item->build();
      }
    }
  }

  function render() {
    if (static::$cache == null) static::init();
    if ($this->children_select_count() == 0)
        $this->build();
    return (template::make_new($this->template, [
      'attributes' => $this->render_attributes(),
      'top_items'  => $this->render_top_items(),
      'sub_items'  => $this->render_sub_items()
    ]))->render();
  }

  function render_top_items() {
    $rendered = '';
    foreach ($this->children_select() as $c_item) {
      $c_clone = clone $c_item;
      $c_clone->children = [];
      $rendered.= $c_clone->render();
    }
    return $rendered ? (template::make_new('tabs_top_items', [
      'children' => $rendered
    ]))->render() : '';
  }

  function render_sub_items() {
    $rendered = '';
    foreach ($this->children_select() as $c_item) {
      $c_url = rtrim(page::get_current()->args_get('base').'/'.$c_item->action_name, '/');
      if (url::is_active_trail($c_url)) {
        foreach ($c_item->children_select() as $c_child) {
          $rendered.= $c_child->render();
        }
        break;
      }
    }
    return $rendered ? (template::make_new('tabs_sub_items', [
      'children' => $rendered
    ]))->render() : '';
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    foreach (storage::get('files')->select('tabs') as $c_module_id => $c_tabs) {
      foreach ($c_tabs as $c_row_id => $c_tab) {
        if (isset(static::$cache[$c_tab->id])) console::log_insert_about_duplicate('tabs', $c_tab->id, $c_module_id);
        static::$cache[$c_tab->id] = $c_tab;
        static::$cache[$c_tab->id]->module_id = $c_module_id;
      }
    }
  }

  static function select_all() {
    if    (static::$cache == null) static::init();
    return static::$cache ?? [];
  }

  static function select($id) {
    if    (static::$cache == null) static::init();
    return static::$cache[$id] ?? null;
  }

  static function select_parent($id_parent) {
    if ($id_parent[0] == 'T' &&
        $id_parent[1] == ':')
         return static   ::select(substr($id_parent, 2));
    else return tabs_item::select(       $id_parent    );
  }

  static function insert($id, $attributes = [], $weight = 0, $module_id = null) {
    $new_tab = new static($id, $attributes, $weight);
    if (static::$cache == null) static::init();
        static::$cache[$id] = $new_tab;
        static::$cache[$id]->module_id = $module_id;
  }

  static function delete($id) {
    unset(static::$cache[$id]);
  }

}}