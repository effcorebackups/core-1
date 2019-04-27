<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use \effcore\core;
          use \effcore\tabs_item;
          use \effcore\tree_item;
          use \effcore\tree;
          use \effcore\url;
          abstract class events_page_trees_nosql {

  static function on_page_init($page) {
    $trees = tree::select_all(true);
    $id = $page->args_get('id');
    core::array_sort_by_title($trees);
    if (!isset($trees[$id])) url::go($page->args_get('base').'/select/'.reset($trees)->id);
    foreach ($trees as $c_tree) {
      tabs_item::insert(      $c_tree->title,
        'trees_nosql_select_'.$c_tree->id,
        'trees_nosql_select',
        'trees_nosql', 'select/'.$c_tree->id);
    }
  }

  static function on_show_block_tree($page) {
    $id = $page->args_get('id');
    if ($id) {
      $tree = clone tree::select($id);
      $tree->build();
      $tree_items = $tree->children_select_recursive();
      $tree_managed_id = 'managed-'.$id;
      $tree_managed = tree::insert($tree->title ?? '', $tree_managed_id);
      $tree_managed->attribute_insert('data-tree-is-managed', 'true');
      $tree_managed->title_state = 'cutted';
      foreach ($tree_items as $c_item) {
        $c_tree_item = tree_item::insert($c_item->title,
          $tree_managed_id.'-'.$c_item->id, $c_item->id_parent !== null ?
          $tree_managed_id.'-'.$c_item->id_parent : null,
          $tree_managed_id,
          $c_item->url, null,
          $c_item->attributes,
          $c_item->element_attributes,
          $c_item->weight, 'develop');
        $c_tree_item->is_managed = true;
      }
      return $tree_managed;
    }
  }

}}
