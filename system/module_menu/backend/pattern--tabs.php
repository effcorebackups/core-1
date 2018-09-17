<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class tabs extends node {

  public $id;
  public $template = 'tabs';

  function insert_item() {
  }

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
      $c_href = page::current_get()->args_get('base').'/'.$c_item->action_name;
      if (url::is_active_trail($c_href)) {
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

  static function item_get($id) {
    return static::$cache_tabs_items[$id] ?? null;
  }

  static function items_get() {
    return static::$cache_tabs_items;
  }

  static function build() {
    foreach(static::items_get() as $c_item) {
      if ($c_item->id_parent) {
        $c_parent = $c_item->id_parent[0] == 'T' &&
                    $c_item->id_parent[1] == ':' ?
                    tabs::get(substr($c_item->id_parent, 2)) :
                    tabs::item_get  ($c_item->id_parent);
        $c_parent->child_insert($c_item, $c_item->id);
      }
    };
  }

}}