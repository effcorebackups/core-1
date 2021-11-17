<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\project {
          use \effcore\block_preset;
          use \effcore\core;
          use \effcore\entity;
          use \effcore\selection;
          use \effcore\token;
          abstract class events_page {

  static function on_block_presets_dynamic_build($event, $id = null) {
    if ($id === null                                                     ) {foreach (entity::get('project')->instances_select() as $c_item)    block_preset::insert('block__release_current_sql__'.$c_item->id, 'Release (current)', 'id_project = '.$c_item->id, [ /* all areas */ ], ['title' => 'Release (current)', 'title_is_visible' => false, 'type' => 'code', 'source' => '\\effcore\\modules\\project\\events_page::block_markup__selection_release_current_make', 'args' => ['id_project' => $c_item->id], 'has_admin_menu' => false, 'attributes' => ['data-block' => true, 'data-id' => 'block__release_current_sql__'.$c_item->id]], 0, 'project');}
    if ($id === null                                                     ) {foreach (entity::get('project')->instances_select() as $c_item)    block_preset::insert('block__releases_sql__'.       $c_item->id, 'Releases',          'id_project = '.$c_item->id, [ /* all areas */ ], ['title' => 'Releases',          'title_is_visible' => false, 'type' => 'code', 'source' => '\\effcore\\modules\\project\\events_page::block_markup__selection_releases_make',        'args' => ['id_project' => $c_item->id], 'has_admin_menu' => false, 'attributes' => ['data-block' => true, 'data-id' => 'block__releases_sql__'.       $c_item->id]], 0, 'project');}
    if ($id !== null && strpos($id, 'block__release_current_sql__') === 0) {$c_item__id = substr($id, strlen('block__release_current_sql__')); block_preset::insert('block__release_current_sql__'.$c_item__id, 'Release (current)', 'id_project = '.$c_item__id, [ /* all areas */ ], ['title' => 'Release (current)', 'title_is_visible' => false, 'type' => 'code', 'source' => '\\effcore\\modules\\project\\events_page::block_markup__selection_release_current_make', 'args' => ['id_project' => $c_item__id], 'has_admin_menu' => false, 'attributes' => ['data-block' => true, 'data-id' => 'block__release_current_sql__'.$c_item__id]], 0, 'project');}
    if ($id !== null && strpos($id, 'block__releases_'.    'sql__') === 0) {$c_item__id = substr($id, strlen('block__releases_'.    'sql__')); block_preset::insert('block__releases_sql__'.       $c_item__id, 'Releases',          'id_project = '.$c_item__id, [ /* all areas */ ], ['title' => 'Releases',          'title_is_visible' => false, 'type' => 'code', 'source' => '\\effcore\\modules\\project\\events_page::block_markup__selection_releases_make',        'args' => ['id_project' => $c_item__id], 'has_admin_menu' => false, 'attributes' => ['data-block' => true, 'data-id' => 'block__releases_sql__'.       $c_item__id]], 0, 'project');}
  }

  static function block_markup__selection_release_current_make($page, $args = []) {
    if (!empty($args['id_project'])) {
      token::insert('id_project_context', 'text', $args['id_project'], null, 'project');
      $selection = core::deep_clone(selection::get('release_current'));
      $selection->title = $selection->title->render();
      $selection->build();
      return $selection;
    }
  }

  static function block_markup__selection_releases_make($page, $args = []) {
    if (!empty($args['id_project'])) {
      token::insert('id_project_context', 'text', $args['id_project'], null, 'project');
      $selection = core::deep_clone(selection::get('releases'));
      $selection->title = $selection->title->render();
      $selection->build();
      return $selection;
    }
  }

}}