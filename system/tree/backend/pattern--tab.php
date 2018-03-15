<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class tab extends \effcore\node {

  public $id;
  public $title = '';
  public $template = 'tab';

  function __construct($title = '', $attributes = [], $children = [], $weight = 0) {
    if ($title) $this->title = $title;
    parent::__construct($attributes, $children, $weight);
  }

  ######################
  ### static methods ###
  ######################

  static protected $cache_tabs;
  static protected $cache_tab_items;

  static function init() {
    $tabs      = storage::get('files')->select('tabs');
    $tab_items = storage::get('files')->select('tab_items');
    foreach ($tabs as $c_module_id => $c_module_tabs) {
      foreach ($c_module_tabs as $c_row_id => $c_tab) {
        static::$cache_tabs[$c_tab->id] = $c_tab;
      }
    }
    foreach ($tab_items as $c_module_id => $c_module_tab_items) {
      foreach ($c_module_tab_items as $c_row_id => $c_item) {
        static::$cache_tab_items[$c_item->id] = $c_item;
      }
    }
  }

  static function get_tabs()        {return static::$cache_tabs;}
  static function get_tab_items()   {return static::$cache_tab_items;}
  static function get_tab($id)      {return static::$cache_tabs[$id];}
  static function get_tab_item($id) {return static::$cache_tab_items[$id];}

}}