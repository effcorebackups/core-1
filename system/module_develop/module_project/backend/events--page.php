<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\project {
          use \effcore\block_preset;
          use \effcore\core;
          use \effcore\entity;
          use \effcore\project;
          use \effcore\selection;
          use \effcore\token;
          abstract class events_page {

  static function on_block_presets_dynamic_build($event, $id = null) {
    if ($id === null                                                             ) {foreach (entity::get('project')->instances_select() as $c_item)            block_preset::insert('block__project_release_current_sql__'.$c_item->id, 'Release (current)', 'id_project = '.$c_item->id, [ /* all areas */ ], ['title' => 'Release (current)', 'title_is_visible' => false, 'type' => 'code', 'source' => '\\effcore\\modules\\project\\events_page::block_markup__selection_project_release_current_make', 'args' => ['id_project' => $c_item->id], 'has_admin_menu' => false, 'attributes' => ['data-id' => 'block__project_release_current_sql__'.$c_item->id]], 0, 'project');}
    if ($id === null                                                             ) {foreach (entity::get('project')->instances_select() as $c_item)            block_preset::insert('block__project_releases_sql__'       .$c_item->id, 'Releases',          'id_project = '.$c_item->id, [ /* all areas */ ], ['title' => 'Releases',          'title_is_visible' => false, 'type' => 'code', 'source' => '\\effcore\\modules\\project\\events_page::block_markup__selection_project_releases_make',        'args' => ['id_project' => $c_item->id], 'has_admin_menu' => false, 'attributes' => ['data-id' => 'block__project_releases_sql__'       .$c_item->id]], 0, 'project');}
    if ($id !== null && strpos($id, 'block__project_release_current_sql__') === 0) {$c_item__id = substr($id, strlen('block__project_release_current_sql__')); block_preset::insert('block__project_release_current_sql__'.$c_item__id, 'Release (current)', 'id_project = '.$c_item__id, [ /* all areas */ ], ['title' => 'Release (current)', 'title_is_visible' => false, 'type' => 'code', 'source' => '\\effcore\\modules\\project\\events_page::block_markup__selection_project_release_current_make', 'args' => ['id_project' => $c_item__id], 'has_admin_menu' => false, 'attributes' => ['data-id' => 'block__project_release_current_sql__'.$c_item__id]], 0, 'project');}
    if ($id !== null && strpos($id, 'block__project_releases_sql__'       ) === 0) {$c_item__id = substr($id, strlen('block__project_releases_sql__'       )); block_preset::insert('block__project_releases_sql__'       .$c_item__id, 'Releases',          'id_project = '.$c_item__id, [ /* all areas */ ], ['title' => 'Releases',          'title_is_visible' => false, 'type' => 'code', 'source' => '\\effcore\\modules\\project\\events_page::block_markup__selection_project_releases_make',        'args' => ['id_project' => $c_item__id], 'has_admin_menu' => false, 'attributes' => ['data-id' => 'block__project_releases_sql__'       .$c_item__id]], 0, 'project');}
  }

  static function block_markup__selection_project_release_current_make($page, $args = []) {
    if (!empty($args['id_project'])) {
      $project = project::select($args['id_project']);
      if ($project) {
        token::insert('project_id_context',          'text', $project->id,          null, 'project');
        token::insert('project_title_context',       'text', $project->title,       null, 'project');
        token::insert('project_description_context', 'text', $project->description, null, 'project');
        token::insert('project_created_context',     'text', $project->created,     null, 'project');
        token::insert('project_updated_context',     'text', $project->updated,     null, 'project');
        $selection = core::deep_clone(selection::get('project_release_current'));
        if ($selection) {
          $selection->title = $selection->title->render();
          $selection->build();
          return $selection;
        }
      }
    }
  }

  static function block_markup__selection_project_releases_make($page, $args = []) {
    if (!empty($args['id_project'])) {
      $project = project::select($args['id_project']);
      if ($project) {
        token::insert('project_id_context',          'text', $project->id,          null, 'project');
        token::insert('project_title_context',       'text', $project->title,       null, 'project');
        token::insert('project_description_context', 'text', $project->description, null, 'project');
        token::insert('project_created_context',     'text', $project->created,     null, 'project');
        token::insert('project_updated_context',     'text', $project->updated,     null, 'project');
        $selection = core::deep_clone(selection::get('project_releases'));
        if ($selection) {
          $selection->title = $selection->title->render();
          $selection->build();
          return $selection;
        }
      }
    }
  }

}}