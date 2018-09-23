<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class manage_trees {

  static function tree_select($page) {
    $trees = tree::all_get();
    core::array_sort_by_property($trees, 'title');
    $id = $page->args_get('id');
    if (!$id) url::go($page->args_get('base').'/select/'.reset($trees)->id);
    foreach ($trees as $c_tree) {
      tabs::item_insert($c_tree->title, 'tree_select_'.$c_tree->id, 'tree_select', 'select/'.$c_tree->id);
    }
    if ($id) {
      $tree = clone tree::get($id);
      $tree->children = core::array_clone_deep($tree->children);
      $tree->attribute_delete('class');
      $tree->attribute_insert('class', ['managed' => 'managed']);
      $tree->title_state = 'cutted';
      foreach ($tree->children_select_recursive() as $c_item) {
        $c_url_info = new markup('x-url', [], $c_item->href ? str_replace('/', (new markup('em', [], '/'))->render(), $c_item->href) : 'no url', 100000);
        $c_item->child_insert_first($c_url_info, 'x_url');
        $c_item->access = null;
        $c_item->href = '';
      }
      return $tree;
    }
  }

  static function tree_insert($page) {
    return new text('tree_insert is UNDER CONSTRUCTION');
  }

}}