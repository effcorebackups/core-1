<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\block_preset;
          use \effcore\frontend;
          use \effcore\page;
          use \effcore\tree_item;
          use \effcore\tree;
          use \effcore\url;
          abstract class events_page {

  static function on_breadcrumbs_build_before($event, $breadcrumbs) {
    if (page::get_current()->id === 'instance_select' ||
        page::get_current()->id === 'instance_insert' ||
        page::get_current()->id === 'instance_update' ||
        page::get_current()->id === 'instance_delete') {
      $entity_name = page::get_current()->args_get('entity_name');
      $instance_id = page::get_current()->args_get('instance_id');
      $category_id = page::get_current()->args_get('category_id');
      if ($entity_name === 'tree_item') {
        if ($category_id) {                                                    $tree = tree::select($category_id       );}
        if ($instance_id) {$tree_item = tree_item::select($instance_id, null); $tree = tree::select($tree_item->id_tree);}
        if (isset($tree)) { # p.s. $tree is undefined on 'insert instance' page
          $breadcrumbs->link_insert('category', $tree->title, '/manage/data/menu/tree_item///'.$tree->id);
        }
      }
    }
  }

  static function on_block_presets_dynamic_build($event, $id = null) {
    if ($id === null                                  ) {foreach (tree::select_all('sql') as $c_item)    block_preset::insert('tree_sql_'.$c_item->id, 'Menus', $c_item->title ?: 'NO TITLE', [ /* no areas */ ], /* display = */ null, 'code', '\\effcore\\modules\\menu\\events_page::block_tree_sql', [ /* no properties */ ], ['id' => $c_item->id], 0, 'menu');}
    if ($id !== null && strpos($id, 'tree_sql_') === 0) {$c_item__id = substr($id, strlen('tree_sql_')); block_preset::insert('tree_sql_'.$c_item__id, 'Menus',                   'NO TITLE', [ /* no areas */ ], /* display = */ null, 'code', '\\effcore\\modules\\menu\\events_page::block_tree_sql', [ /* no properties */ ], ['id' => $c_item__id], 0, 'menu');}
  }

  static function block_tree_sql($page, $args) {
    if (!empty($args['id'])) {
      return tree::select($args['id']);
    }
  }

  static function on_tree_build_after($event, $tree) {
    if (!frontend::select('tree_menu'         )                         ) frontend::insert('tree_menu',   null, 'styles', ['path' => 'frontend/tree.cssd',        'attributes' => ['rel' => 'stylesheet', 'media' => 'all']], 'tree_style', 'menu');
    if (!frontend::select('tree_system'       ) && $tree->id == 'system') frontend::insert('tree_system', null, 'styles', ['path' => 'frontend/tree-system.cssd', 'attributes' => ['rel' => 'stylesheet', 'media' => 'all']], 'tree_style', 'menu');
    if (!frontend::select('tree_rearrangeable') && $tree->visualization_mode == 'decorated-rearrangeable') {
      frontend::insert('tree_rearrangeable', null, 'scripts', ['path'  => 'frontend/tree-rearrangeable.js',   'attributes' => ['defer' => true]],                         'tree_script_rearrangeable', 'menu');
      frontend::insert('tree_rearrangeable', null, 'styles',  ['path'  => 'frontend/tree-rearrangeable.cssd', 'attributes' => ['rel' => 'stylesheet', 'media' => 'all']], 'tree_style_rearrangeable',  'menu');
    }
  }

}}