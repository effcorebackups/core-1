<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class tabs extends node {

  public $id;
  public $template = 'tabs';

  function render() {
    return (new template($this->template, [
      'attributes' => factory::data_to_attr($this->attribute_select_all()),
      'top_items'  => $this->render_top_items(),
      'sub_items'  => $this->render_sub_items()
    ]))->render();
  }

  function render_top_items() {
    $rendered = '';
    foreach ($this->children_select() as $c_item) {
      if (!empty($c_item->parent_is_tab)) {
        $c_clone = clone $c_item;
        $c_clone->children = [];
        $rendered.= $c_clone->render();
      }
    }
    return $rendered ? (new template('tabs_top_items', [
      'children' => $rendered
    ]))->render() : '';
  }

  function render_sub_items() {
    $rendered = '';
    foreach ($this->children_select() as $c_item) {
      if (!empty($c_item->parent_is_tab)) {
        $c_href = page::get_current()->args_get('base').'/'.$c_item->action_name;
        if (url::is_active_trail($c_href)) {
          foreach ($c_item->children_select() as $c_child) {
            $rendered.= $c_child->render();
          }
          break;
        }
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
        static::$cache_tabs[$c_tab->id] = $c_tab;
        static::$cache_tabs[$c_tab->id]->module_id = $c_module_id;
      }
    }
    foreach (storage::get('files')->select('tabs_items') as $c_module_id => $c_tabs_items) {
      foreach ($c_tabs_items as $c_row_id => $c_item) {
        static::$cache_tabs_items[$c_item->id] = $c_item;
        static::$cache_tabs_items[$c_item->id]->module_id = $c_module_id;
      }
    }
  }

  static function get($id) {
    return isset(static::$cache_tabs[$id]) ?
                 static::$cache_tabs[$id] : null;
  }

  static function get_all() {
    return static::$cache_tabs;
  }

  static function get_item($id) {
    return isset(static::$cache_tabs_items[$id]) ?
                 static::$cache_tabs_items[$id] : null;
  }

  static function get_item_all() {
    return static::$cache_tabs_items;
  }

  static function build() {
    foreach(static::get_item_all() as $c_item) {
      if ($c_item->id_parent) {
        $c_parent = !empty($c_item->parent_is_tab) ?
            tabs::get     ($c_item->id_parent) :
            tabs::get_item($c_item->id_parent);
        $c_parent->child_insert($c_item, $c_item->id);
      }
    };
  }

}}