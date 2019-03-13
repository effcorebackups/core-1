<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\block;
          use \effcore\core;
          use \effcore\entity;
          use \effcore\selection;
          use \effcore\tabs;
          use \effcore\url;
          abstract class events_page_instances_manage {

  # function() ←→ url mapping:
  # ──┬──────────────────────────────────────────────────────────────────────────────────
  # ? │ /manage/instances/select → /manage/instances/select/%%_entity_name
  # ? │ /manage/instances/insert → /manage/instances/insert/%%_entity_name
  # ? │                            /manage/instances/select/%%_entity_name/%%_instance_id
  # ? │                            /manage/instances/update/%%_entity_name/%%_instance_id
  # ? │                            /manage/instances/delete/%%_entity_name/%%_instance_id
  # ──┴──────────────────────────────────────────────────────────────────────────────────

  # ─────────────────────────────────────────────────────────────────────
  # select multiple instances
  # ─────────────────────────────────────────────────────────────────────

  static function on_show_block_instance_select_multiple($page) {
    $entities = entity::all_get(false);
    $entity_name = $page->args_get('entity_name');
    core::array_sort_by_title($entities);
    if (!isset($entities[$entity_name])) url::go($page->args_get('base').'/select/'.reset($entities)->name);
    foreach ($entities as $c_entity) {
      tabs::item_insert(             $c_entity->title_plural,
        'instance_select_'.          $c_entity->name,
        'instance_select', 'select/'.$c_entity->name, null, ['class' => [
                           'select-'.$c_entity->name =>
                           'select-'.$c_entity->name]]
      );
    }
  # create selection
    $entity = entity::get($entity_name);
    if ($entity) {
      $selection = new selection;
      $selection->is_paged = true;
      foreach ($entity->fields as $c_name => $c_field) {
        if (!empty($c_field->show_in_manager)) {
          $selection->field_entity_insert(null, $entity->name, $c_name);
        }
      }
      $selection->field_checkbox_insert(null, '', 80);
      $selection->field_action_insert();
      return new block('', ['class' => [
        $entity->name =>
        $entity->name]],
        $selection
      );
    }
  }

}}