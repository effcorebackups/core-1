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
  # /manage/instances/select → /manage/instances/select/%%_managing_group/%%_entity_name/%%_instances_group_by
  # /manage/data/insert      → /manage/data/insert/%%_entity_name/%%_category
  #                            /manage/data/select/%%_entity_name/%%_instance_id
  #                            /manage/data/update/%%_entity_name/%%_instance_id
  #                            /manage/data/delete/%%_entity_name/%%_instance_id
  # ─────────────────────────────────────────────────────────────────────────────────

  static function on_tab_build_before($event, $tab) {
    $managing_group = page::get_current()->args_get('managing_group');
    $entity_name    = page::get_current()->args_get('entity_name'   );
    $entities = entity::get_all();
    $groups   = entity::groups_managing_get();
    $entities_by_groups = [];
    core::array_sort_text($groups);
    foreach ($groups as $c_group_id => $c_title) {
      foreach ($entities as $c_name => $c_entity)
        if ($c_entity->managing_is_on && $c_group_id == $c_entity->group_managing_get_id())
          $entities_by_groups[$c_group_id][$c_name] = $c_entity;
      if (isset($entities_by_groups[$c_group_id]))
        core::array_sort_by_text_property($entities_by_groups[$c_group_id]);
      else {                        unset($entities_by_groups[$c_group_id]);
                                    unset($groups            [$c_group_id]);
      }
    }

  # ┌─────────────────────────────────────────────────────────────────┬────────────────────────────────────────────────┐
  # │ /manage/instances/select                                        │ managing_group != true && entity_name != true │
  # │ /manage/instances/select/      managing_group                   │ managing_group == true && entity_name != true │
  # │ /manage/instances/select/      managing_group/      entity_name │ managing_group == true && entity_name == true │
  # │ /manage/instances/select/wrong_managing_group                   │ managing_group != true && entity_name != true │
  # │ /manage/instances/select/wrong_managing_group/      entity_name │ managing_group != true && entity_name == true │
  # │ /manage/instances/select/      managing_group/wrong_entity_name │ managing_group == true && entity_name != true │
  # │ /manage/instances/select/wrong_managing_group/wrong_entity_name │ managing_group != true && entity_name != true │
  # └─────────────────────────────────────────────────────────────────┴────────────────────────────────────────────────┘
    if (isset($groups[$managing_group])                                                              == false) url::go(page::get_current()->args_get('base').'/'.array_keys($groups)[0].'/'.array_keys($entities_by_groups[array_keys($groups)[0]])[0]);
    if (isset($groups[$managing_group]) && isset($entities_by_groups[$managing_group][$entity_name]) == false) url::go(page::get_current()->args_get('base').'/'.   $managing_group    .'/'.array_keys($entities_by_groups[   $managing_group    ])[0]);

  # make tabs
    foreach ($entities_by_groups as $c_group_id => $c_entities) {
      tabs_item::insert($groups[$c_group_id],
            'data_'.$c_group_id, null,
            'data', $c_group_id);
      foreach ($c_entities as $c_name =>  $c_entity) {
        tabs_item::insert($c_entity->title_plural,
            'data_'.$c_group_id.'_'.$c_name,
            'data_'.$c_group_id,
            'data', $c_group_id.'/'.$c_name
        );
      }
    }
  }

}}