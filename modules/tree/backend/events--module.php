<?php

  #############################################################
  ### Copyright © 2017 Maxim Rysevets. All rights reserved. ###
  #############################################################

namespace effectivecore\modules\tree {
          use \effectivecore\factory;
          use \effectivecore\messages_factory as messages;
          use \effectivecore\modules\storage\storages_factory as storages;
          abstract class events_module extends \effectivecore\events_module {

  static function on_start() {
  # link all parents for tree_items
    foreach (storages::get('settings')->select('tree_items') as $c_items) {
      foreach ($c_items as $item_id => $c_item) {
        if (!empty($c_item->parent)) {
          $c_parent = factory::npath_get_object($c_item->parent, storages::get('settings')->select());
          if ($c_parent) {
            $c_parent->children[$item_id] = $c_item;
          }
        }
      }
    }
  }

  static function on_install() {
    foreach (storages::get('settings')->select('entities')['tree'] as $c_entity) $c_entity->install();
    messages::add_new('Database for module "tree" was installed');
  }

}}