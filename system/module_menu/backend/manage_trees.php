<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class manage_trees {

  static function tree_select($page) {
    $id = $page->args_get('id');
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

}}