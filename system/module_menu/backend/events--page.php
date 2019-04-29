<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\entity;
          use \effcore\tabs_item;
          use \effcore\tree;
          abstract class events_page {

  static function on_show_block_tree_sql($page, $args) {
    if (!empty($args['id_tree'])) {
      return tree::select($args['id_tree']);
    }
  }

  static function on_tab_before_build($tab) {
    foreach (entity::get('tree')->instances_select() as $c_tree) {
      tabs_item::insert($c_tree->title,
        'manage_instances_menu_tree_item_'.$c_tree->id,
        'manage_instances_menu_tree_item',
        'manage_instances', 'menu/tree_item/'.$c_tree->id);
    }
  }

}}