<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\core;
          use \effcore\entity;
          use \effcore\page;
          use \effcore\tabs_item;
          use \effcore\url;
          abstract class events_page_instance_select_multiple {

  # URLs variants:
  # ─────────────────────────────────────────────────────────────────────
  # multiple select: / manage / data
  # multiple select: / manage / data / %%_managing_group_id / %%_entity_name
  # multiple select: / manage / data / %%_managing_group_id / %%_entity_name / …………………………………… / ……………………………………………………… / %%_category_id
  #          insert: / manage / data / %%_managing_group_id / %%_entity_name / …………………………………… / %%_action_name=insert / %%_category_id
  #          insert: / manage / data / %%_managing_group_id / %%_entity_name / …………………………………… / %%_action_name=insert
  #          select: / manage / data / %%_managing_group_id / %%_entity_name / %%_instance_id
  #          update: / manage / data / %%_managing_group_id / %%_entity_name / %%_instance_id / %%_action_name=update
  #          delete: / manage / data / %%_managing_group_id / %%_entity_name / %%_instance_id / %%_action_name=delete
  # ─────────────────────────────────────────────────────────────────────

  static function on_tab_build_before($event, $tab) {
    $managing_group_id = page::get_current()->args_get('managing_group_id');
    $entity_name       = page::get_current()->args_get('entity_name'      );
    $groups   = entity::groups_managing_get();
    $entities = entity::get_all();
    $entities_by_groups = [];
  # collect manageable entities
    foreach ($entities as $c_entity) {
      if ($c_entity->managing_is_on) {
        $entities_by_groups[$c_entity->group_managing_get_id()]
                           [$c_entity->name                   ] = $c_entity->title;
      }
    }
  # remove empty groups
    foreach ($groups as $c_group_id => $c_group_title) {
      if (empty($entities_by_groups[$c_group_id])) {
          unset($groups            [$c_group_id]);
      }
    }
  # sorting
    core::array_sort_text($groups);
    foreach ($entities_by_groups as $c_group_id => &$c_entities) {
      core::array_sort_text                        ($c_entities);
    }
  # redirect if required
    if (empty($entities_by_groups[$managing_group_id][$entity_name])) {
      $managing_group_id = isset($entities_by_groups[$managing_group_id]              ) ? $managing_group_id : key($groups                                );
      $entity_name       = isset($entities_by_groups[$managing_group_id][$entity_name]) ? $entity_name       : key($entities_by_groups[$managing_group_id]);
      url::go(page::get_current()->args_get('base').'/'.$managing_group_id.'/'.$entity_name);
    }
  # make tabs
    foreach ($groups as $c_group_id => $c_group_title) {
      tabs_item::insert($c_group_title,
        'data_'.$c_group_id, null,
        'data', $c_group_id);
      foreach ($entities_by_groups[$c_group_id] as $c_entity_name => $c_entity_title) {
        tabs_item::insert($c_entity_title,
          'data_'.$c_group_id.'_'.$c_entity_name,
          'data_'.$c_group_id,
          'data', $c_group_id.'/'.$c_entity_name
        );
      }
    }
  }

}}