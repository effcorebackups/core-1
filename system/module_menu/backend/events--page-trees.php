<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\core;
          use \effcore\tabs;
          use \effcore\tree;
          use \effcore\url;
          abstract class events_page_trees {

  static function on_page_init($page) {
    $trees = tree::all_get();
    $id = $page->args_get('id');
    core::array_sort_by_title($trees);
    if (!isset($trees[$id])) url::go($page->args_get('base').'/select/'.reset($trees)->id);
    foreach ($trees as $c_tree) {
      tabs::item_insert(         $c_tree->title,
        'tree_select_'.          $c_tree->id,
        'tree_select', 'select/'.$c_tree->id, null, ['class' => [
                       'select-'.$c_tree->id =>
                       'select-'.$c_tree->id]]);
    }
  }

}}
