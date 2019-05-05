<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\core;
          use \effcore\entity;
          use \effcore\page;
          use \effcore\tabs_item;
          use \effcore\url;
          abstract class events_page_instance_select_multiple {

  # URLs for manage:
  # ─────────────────────────────────────────────────────────────────────────────────
  # /manage/instances/select → /manage/instances/select/%%_entity_group_id/%%_entity_name/%%_instances_group_by
  # /manage/instance /insert → /manage/instance /insert/%%_entity_name
  #                            /manage/instance /select/%%_entity_name/%%_instance_id
  #                            /manage/instance /update/%%_entity_name/%%_instance_id
  #                            /manage/instance /delete/%%_entity_name/%%_instance_id
  # ─────────────────────────────────────────────────────────────────────────────────

  static function on_tab_before_build($tab) {
    $entity_group_id = page::get_current()->args_get('entity_group_id');
    $entity_name     = page::get_current()->args_get('entity_name'    );
    $entities = entity::get_all   ();
    $groups   = entity::groups_get();
    $entities_by_groups = [];
    core::array_sort_text($groups);
    foreach ($groups as $c_grp_id => $c_title) {
      foreach ($entities as $c_name => $c_entity)
        if ($c_grp_id == $c_entity->group_get_id())
          $entities_by_groups[$c_grp_id][$c_name] = $c_entity;
      core::array_sort_by_title(
        $entities_by_groups[$c_grp_id]
      );
    }
  # ┌──────────────────────────────────────────────────────────────────┬────────────────────────────────────────────────┐
  # │ /manage/instances/select                                         │ entity_group_id != true && entity_name != true │
  # │ /manage/instances/select/      entity_group_id                   │ entity_group_id == true && entity_name != true │
  # │ /manage/instances/select/      entity_group_id/      entity_name │ entity_group_id == true && entity_name == true │
  # │ /manage/instances/select/wrong_entity_group_id                   │ entity_group_id != true && entity_name != true │
  # │ /manage/instances/select/wrong_entity_group_id/      entity_name │ entity_group_id != true && entity_name == true │
  # │ /manage/instances/select/      entity_group_id/wrong_entity_name │ entity_group_id == true && entity_name != true │
  # │ /manage/instances/select/wrong_entity_group_id/wrong_entity_name │ entity_group_id != true && entity_name != true │
  # └──────────────────────────────────────────────────────────────────┴────────────────────────────────────────────────┘
    if (isset($groups[$entity_group_id])                                                               == false) url::go(page::get_current()->args_get('base').'/'.array_keys($groups)[0].'/'.array_keys($entities_by_groups[array_keys($groups)[0]])[0]);
    if (isset($groups[$entity_group_id]) && isset($entities_by_groups[$entity_group_id][$entity_name]) == false) url::go(page::get_current()->args_get('base').'/'.   $entity_group_id   .'/'.array_keys($entities_by_groups[   $entity_group_id   ])[0]);
  # make tabs
    foreach ($entities_by_groups as $c_grp_id => $c_entities) {
      tabs_item::insert($groups[$c_grp_id],
            'manage_instances_'.$c_grp_id, null,
            'manage_instances', $c_grp_id);
      foreach ($c_entities as $c_name =>  $c_entity) {
        tabs_item::insert($c_entity->title_plural,
            'manage_instances_'.$c_grp_id.'_'.$c_name,
            'manage_instances_'.$c_grp_id,
            'manage_instances', $c_grp_id.'/'.$c_name);
      }
    }
  }

}}