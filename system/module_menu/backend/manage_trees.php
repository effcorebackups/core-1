<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class manage_trees {

  static function tree_select($page) {
    $trees = tree::all_get();
    $id = $page->args_get('id');
    core::array_sort_by_property($trees, 'title');
    if (!isset($trees[$id])) url::go($page->args_get('base').'/select/'.reset($trees)->id);
    foreach ($trees as $c_tree) {
      tabs::item_insert(         $c_tree->title,
        'tree_select_'.          $c_tree->id,
        'tree_select', 'select/'.$c_tree->id, null, ['class' => [
                       'select-'.$c_tree->id =>
                       'select-'.$c_tree->id]]);
    }
    if ($id) {
      $tree = clone tree::get($id);
      $tree->build();
      $tree = core::deep_clone($tree, [__NAMESPACE__.'\\tree_item' => __NAMESPACE__.'\\tree_item_managed']);
      $tree->attribute_delete('class');
      $tree->attribute_insert('class', ['managed' => 'managed']);
      $tree->title_state = 'cutted';
      return $tree;
    }
  }

  static function tree_insert($page) {
    return new text('tree_insert is UNDER CONSTRUCTION');
  }

}}