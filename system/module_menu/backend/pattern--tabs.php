<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class tabs extends node implements has_cache_cleaning {

  public $id;
  public $template = 'tabs';

  function render() {
    return (new template($this->template, [
      'attributes' => core::data_to_attr($this->attributes_select()),
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
    return $rendered ? (new template('tabs_top_items', [
      'children' => $rendered
    ]))->render() : '';
  }

  function render_sub_items() {
    $rendered = '';
    foreach ($this->children_select() as $c_item) {
      $c_url = rtrim(page::current_get()->args_get('base').'/'.$c_item->action_name, '/');
      if (url::is_active_trail($c_url)) {
        foreach ($c_item->children_select() as $c_child) {
          $rendered.= $c_child->render();
        }
        break;
      }
    }
    return $rendered ? (new template('tabs_sub_items', [
      'children' => $rendered
    ]))->render() : '';
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache_tabs;
  static protected $cache_tabs_items;

  static function cache_cleaning() {
    static::$cache_tabs       = null;
    static::$cache_tabs_items = null;
    static::init();
    static::build();
  }

  static function init() {
    foreach (storage::get('files')->select('tabs') as $c_module_id => $c_tabs) {
      foreach ($c_tabs as $c_row_id => $c_tab) {
        if (isset(static::$cache_tabs[$c_tab->id])) console::log_about_duplicate_add('tab', $c_tab->id);
        static::$cache_tabs[$c_tab->id] = $c_tab;
        static::$cache_tabs[$c_tab->id]->module_id = $c_module_id;
      }
    }
    foreach (storage::get('files')->select('tabs_items') as $c_module_id => $c_tabs_items) {
      foreach ($c_tabs_items as $c_row_id => $c_item) {
        if (isset(static::$cache_tabs_items[$c_item->id])) console::log_about_duplicate_add('tab_item', $c_item->id);
        static::$cache_tabs_items[$c_item->id] = $c_item;
        static::$cache_tabs_items[$c_item->id]->module_id = $c_module_id;
      }
    }
  }

  static function get($id) {
    return static::$cache_tabs[$id] ?? null;
  }

  static function all_get() {
    return static::$cache_tabs;
  }

  static function parent_get($id_parent) {
    if ($id_parent[0] == 'T' &&
        $id_parent[1] == ':')
         return static::$cache_tabs[substr($id_parent, 2)] ?? null;
    else return static::$cache_tabs_items [$id_parent]     ?? null;
  }

  static function item_select($id) {
    return static::$cache_tabs_items[$id] ?? null;
  }

  static function items_select() {
    return static::$cache_tabs_items;
  }

  static function item_insert($title, $id, $id_parent, $action_name, $action_name_default = null, $attributes = [], $hidden = false, $weight = 0) {
    $new_item = new tabs_item($title, $id, $id_parent, $action_name, $action_name_default, $attributes, $hidden, $weight);
    static::$cache_tabs_items[$id] = $new_item;
    static::$cache_tabs_items[$id]->module_id = null;
    static::build([$new_item]);
  }

  static function item_delete($id) {
    if (isset(static::$cache_tabs_items[$id])) {
      $id_parent = static::$cache_tabs_items[$id]->id_parent;
             unset(static::$cache_tabs_items[$id]);
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