<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class manage_trees {

  static function tree_select($page) {
    $trees = tree::all_get();
    $id = $page->args_get('id');
    if (!$id) url::go($page->args_get('base').'/select/'.reset($trees)->id);
    foreach ($trees as $c_tree) {
      tabs::item_insert($c_tree->title, 'tree_select_'.$c_tree->id, 'tree_select', 'select/'.$c_tree->id);
    }
    if ($id) {
      $tree = clone tree::get($id);
      $tree->attribute_delete('class');
      $tree->title_state = 'cutted';
      foreach ($tree->children_select_recursive() as $c_item) {
        $c_item->access = null;
        $c_item->href = '';
      }
      return $tree;
    }
  }

  static function tree_insert($page) {
    return new markup('div', [], 'tree_insert');
  }

  static function tree_update($page) {
  }

  static function tree_delete($page) {
  }

}}