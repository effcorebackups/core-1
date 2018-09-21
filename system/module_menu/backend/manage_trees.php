<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class manage_trees {

  static function tree_select($page) {
    $trees = tree::all_get();
    if (!$page->args_get('id')) url::go($page->args_get('base').'/select/'.reset($trees)->id);
    foreach ($trees as $c_tree) {
      tabs::item_insert($c_tree->title, 'tree_select_'.$c_tree->id, 'tree_select', 'select/'.$c_tree->id);
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