<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\core;
          use \effcore\page;
          use \effcore\tabs_item;
          use \effcore\translation;
          use \effcore\tree;
          abstract class events_page_instance_select_multiple {

  static function on_tab_build_before($event, $tab) {
    $action_name      = page::get_current()->args_get('action_name'       );
    $managing_group   = page::get_current()->args_get('managing_group'    );
    $entity_name      = page::get_current()->args_get('entity_name'       );
    $group_by_id_tree = page::get_current()->args_get('instances_group_by');
    $trees = tree::select_all('sql');
    core::array_sort_by_text_property($trees);
    if ($entity_name == 'tree_item' && !isset($trees[$group_by_id_tree])) core::send_header_and_exit('page_not_found');
    tabs_item::delete('data_menu_tree_item');
    foreach ($trees as $c_tree) {
      tabs_item::insert(translation::get('Items for: %%_title', ['title' => translation::get($c_tree->title)]),
        'data_menu_tree_item_'.$c_tree->id,
        'data_menu',
        'data', 'select_multiple/menu/tree_item/'.$c_tree->id
      );
    }
  }

}}