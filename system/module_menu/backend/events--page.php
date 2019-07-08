<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\core;
          use \effcore\entity;
          use \effcore\page;
          use \effcore\tabs_item;
          use \effcore\translation;
          use \effcore\tree;
          abstract class events_page {

  static function on_tab_before_build($tab) {
    $trees = tree::select_all('sql');
    $entity_name      = page::get_current()->args_get('entity_name'       );
    $group_by_id_tree = page::get_current()->args_get('instances_group_by');
    core::array_sort_by_title($trees);
    if ($entity_name == 'tree_item' && !isset($trees[$group_by_id_tree])) core::send_header_and_exit('page_not_found');
    tabs_item::delete('manage_instances_menu_tree_item');
    foreach ($trees as $c_tree) {
      tabs_item::insert(translation::get('Items for: %%_title', ['title' => translation::get($c_tree->title)]),
        'manage_instances_menu_tree_item_'.$c_tree->id,
        'manage_instances_menu',
        'manage_instances', 'menu/tree_item/'.$c_tree->id
      );
    }
  }

  static function on_show_block_tree_sql($page, $args) {
    if (!empty($args['id_tree'])) {
      return tree::select($args['id_tree']);
    }
  }

}}