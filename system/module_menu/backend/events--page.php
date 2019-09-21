<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\page;
          use \effcore\tree_item;
          use \effcore\tree;
          use \effcore\url;
          abstract class events_page {

  static function on_show_block_tree_sql($page, $args) {
    if (!empty($args['id_tree'])) {
      return tree::select($args['id_tree']);
    }
  }

  static function on_breadcrumbs_build_before($event, $breadcrumbs) {
    $entity_name   = page::get_current()->args_get('entity_name'  );
    $instance_id   = page::get_current()->args_get('instance_id'  );
    $category_id   = page::get_current()->args_get('category_id'  );
    $back_return_0 = page::get_current()->args_get('back_return_0');
    $back_return_n = page::get_current()->args_get('back_return_n');
    if ($entity_name == 'tree_item') {
      if (page::get_current()->id == 'instance_select' ||
          page::get_current()->id == 'instance_insert' ||
          page::get_current()->id == 'instance_update' ||
          page::get_current()->id == 'instance_delete') {
        if ($category_id) {                                                    $tree = tree::select($category_id       );}
        if ($instance_id) {$tree_item = tree_item::select($instance_id, null); $tree = tree::select($tree_item->id_tree);}
        $breadcrumbs->link_update('entity', 'Tree items', '/manage/data/menu/tree_item');
        if (isset($tree)) { # p.s. $tree is undefined on "insert instance" page
          $breadcrumbs->link_insert('category', $tree->title,
            $back_return_0 ?: (url::back_url_get() ?: ($back_return_n ?: '/manage/data/menu/tree_item///'.$tree->id))
          );
        }
      }
    }
  }

}}