<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\markup;
          use \effcore\text;
          use \effcore\tree_item;
          use \effcore\tree;
          abstract class events_page {

  static function on_show_block_tree_sql($page, $args) {
    if (!empty($args['id_tree'])) {
      return tree::select($args['id_tree']);
    }
  }

  static function on_show_block_breadcrumbs_main($page, $args) {
    $branches = [];
    foreach (tree_item::select_all_by_id_tree('main') as $c_item) {
      if ($c_item->is_active      () ||
          $c_item->is_active_trail()) {
        $branches[][$c_item->id] = $c_item;
      }
    }
  # resolve all branches (find all parents)
    foreach ($branches as &$c_branch) {
      $counter = 0;
      while (true) {
        if ($counter++ >= 15) break;
        $c_parent_id = end($c_branch)->id_parent;
        if ($c_parent_id) {
            $c_parent = tree_item::select($c_parent_id, 'main');
            $c_branch[$c_parent->id] = $c_parent;}
        else break;
      }
    }
  # find the longest branch
    $found = [];
    foreach ($branches as $c_branch) {
      if (count($c_branch) > count($found)) {
        $found = $c_branch;
      }
    }
  # make markup
    $result = new markup('x-breadcrumbs');
    foreach (array_reverse($found) as $c_item) {
      $result->child_insert(
        new markup('a', ['href' => $c_item->href_get() ?: false],
          new text($c_item->title, [], true, true)
        ), $c_item->id
      );
    }
    return $result;
  }

}}