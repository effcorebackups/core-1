<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\tree {
          use \effcore\tab;
          use \effcore\tree;
          use \effcore\entity;
          use \effcore\message;
          use \effcore\translation;
          abstract class events_module extends \effcore\events_module {

  static function on_start() {
    tree::init();
    foreach(tree::get_tree_items() as $c_item) {
      if ($c_item->id_parent) {
        $c_parent = !empty($c_item->parent_is_tree) ?
            tree::get_tree     ($c_item->id_parent) :
            tree::get_tree_item($c_item->id_parent);
        $c_parent->child_insert($c_item, $c_item->id);
      }
    };
    tab::init();
    foreach(tab::get_tab_items() as $c_item) {
      if ($c_item->id_parent) {
        $c_parent = !empty($c_item->parent_is_tab) ?
             tab::get_tab     ($c_item->id_parent) :
             tab::get_tab_item($c_item->id_parent);
        $c_parent->child_insert($c_item, $c_item->id);
      }
    };
  }

  static function on_install() {
  # install entities
    foreach (entity::get_all_by_module('tree') as $c_entity) {
      if ($c_entity->install()) message::insert(translation::get('Entity %%_name was installed.',     ['name' => $c_entity->get_name()]));
      else                      message::insert(translation::get('Entity %%_name was not installed!', ['name' => $c_entity->get_name()]), 'error');
    }
  }

}}