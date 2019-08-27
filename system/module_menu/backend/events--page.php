<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\page;
          use \effcore\translation;
          use \effcore\tree;
          abstract class events_page {

  static function on_show_block_tree_sql($page, $args) {
    if (!empty($args['id_tree'])) {
      return tree::select($args['id_tree']);
    }
  }

  static function on_breadcrumbs_build_before($event, $breadcrumbs) {
    $entity_name = page::get_current()->args_get('entity_name');
    $category_id = page::get_current()->args_get('category_id');
    if (page::get_current()->id == 'instance_insert' && $entity_name == 'tree_item') {
      $trees = tree::select_all('sql');
      if (!empty($trees[$category_id]))
           $breadcrumbs->link_update('entity', translation::get('Items for: %%_title', ['title' => translation::get($trees[$category_id]->title)]), '/manage/data/menu/tree_item///'.$category_id);
      else $breadcrumbs->link_delete('entity');
    }
  }

}}