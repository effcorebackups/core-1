<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use \effcore\tree_item;
          abstract class events_page {

  static function on_before_render($page, $template) {
  }

  static function on_decorator_before_build($decorator) {
  }

  static function on_decorator_after_build($decorator) {
  }

  static function on_selection_before_build($selection) {
  }

  static function on_selection_after_build($selection) {
  }

  static function on_tab_before_build($tab) {
  }

  static function on_tab_after_build($tab) {
  }

  static function on_tree_before_build($tree) {
    if ($tree->id == 'demo_nosql') {
      tree_item::insert('item #1.2.3 (from code)', 'demo_item_1_2_3','demo_item_1_2', 'demo_nosql', '/develop/demo/embedded/trees/item_1/item_1_2/item_1_2_3');
      tree_item::insert('item #3 (from code)',     'demo_item_3',     null,           'demo_nosql', '/develop/demo/embedded/trees/item_3'                    );
    } elseif ($tree->id == 'demo_sql') {
      tree_item::insert('item #1.2.3 (from code)', '6',               '3',            'demo_sql',   '/develop/demo/embedded/trees/item_1/item_1_2/item_1_2_3');
      tree_item::insert('item #3 (from code)',     '9',               null,           'demo_sql',   '/develop/demo/embedded/trees/item_3'                    );
    }
  }

  static function on_tree_after_build($tree) {
  }

}}
