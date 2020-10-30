<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\access;
          use \effcore\block_preset;
          use \effcore\entity;
          use \effcore\frontend;
          use \effcore\markup;
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
    if ($id === null                                          ) {foreach (tree::select_all('sql') as $c_item)            block_preset::insert('block__tree_sql__'.$c_item->id, 'Menus', $c_item->title ?: 'NO TITLE', [ /* all areas */ ], ['type' => 'code', 'source' => '\\effcore\\modules\\menu\\events_page::block_markup__tree_get', 'args' => ['instance_id' => $c_item->id, 'entity_name' => 'tree'], 'has_admin_tree_menu' => true, 'attributes' => ['data-block' => true, 'data-id' => 'block__tree_sql__'.$c_item->id]], 0, 'menu');}
    if ($id !== null && strpos($id, 'block__tree_sql__') === 0) {$c_item__id = substr($id, strlen('block__tree_sql__')); block_preset::insert('block__tree_sql__'.$c_item__id, 'Menus',                   'NO TITLE', [ /* all areas */ ], ['type' => 'code', 'source' => '\\effcore\\modules\\menu\\events_page::block_markup__tree_get', 'args' => ['instance_id' => $c_item__id, 'entity_name' => 'tree'], 'has_admin_tree_menu' => true, 'attributes' => ['data-block' => true, 'data-id' => 'block__tree_sql__'.$c_item__id]], 0, 'menu');}
  }

  static function on_block_build_after($event, $block) {
    if (url::get_current()->query_arg_select('manage_layout') === 'true') {
      if (access::check((object)['roles' => ['registered' => 'registered']])) {
        if (!empty($block->has_admin_tree_menu)) {
          $instance_id = $block->args['instance_id'];
          $entity_name = $block->args['entity_name'];
          if ($entity_name === 'tree'                                &&
              access::check(entity::get('tree_item')->access_select) &&
              access::check(entity::get('tree_item')->access_update)) {
            $block->extra_t = new markup('x-admin-actions', ['data-entity_name' => $entity_name],
              new markup('a', ['data-id' => 'update', 'href' => '/manage/data/menu/tree_item///'.$instance_id.'?'.url::back_part_make()], 'edit')
            );
          }
        }
      }
    }
  }

  static function block_markup__tree_get($page, $args) {
    if (!empty($args['instance_id'])) {
      return tree::select($args['instance_id']);
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