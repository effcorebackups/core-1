<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class tabs extends \effcore\node {

  public $id;
  public $template = 'tabs';

  ######################
  ### static methods ###
  ######################

  static protected $cache_tabs;
  static protected $cache_tabs_items;

  static function init() {
    $tabs       = storage::get('files')->select('tabs');
    $tabs_items = storage::get('files')->select('tabs_items');
    foreach ($tabs as $c_module_id => $c_module_tabs) {
      foreach ($c_module_tabs as $c_row_id => $c_tab) {
        static::$cache_tabs[$c_tab->id] = $c_tab;
      }
    }
    foreach ($tabs_items as $c_module_id => $c_module_tabs_items) {
      foreach ($c_module_tabs_items as $c_row_id => $c_item) {
        static::$cache_tabs_items[$c_item->id] = $c_item;
      }
    }
  }

  static function build() {
    foreach(static::get_item() as $c_item) {
      if ($c_item->id_parent) {
        $c_parent = !empty($c_item->parent_is_tab) ?
            tabs::get     ($c_item->id_parent) :
            tabs::get_item($c_item->id_parent);
        $c_parent->child_insert($c_item, $c_item->id);
      }
    };
  }

  static function get($id = null) {
    return $id ? static::$cache_tabs[$id] :
                 static::$cache_tabs;
  }

  static function get_item($id = null) {
    return $id ? static::$cache_tabs_items[$id] :
                 static::$cache_tabs_items;
  }

}}