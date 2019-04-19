<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\block;
          use \effcore\core;
          use \effcore\entity;
          use \effcore\markup;
          use \effcore\selection;
          use \effcore\tabs_item;
          use \effcore\text;
          use \effcore\translation;
          use \effcore\url;
          abstract class events_page_instance_select_multiple {

  # URLs for manage:
  # ─────────────────────────────────────────────────────────────────────────────────
  # /manage/instances/select → /manage/instances/select/%%_group/%%_entity_name
  # /manage/instance /insert → /manage/instance /insert/%%_entity_name
  #                            /manage/instance /select/%%_entity_name/%%_instance_id
  #                            /manage/instance /update/%%_entity_name/%%_instance_id
  #                            /manage/instance /delete/%%_entity_name/%%_instance_id
  # ─────────────────────────────────────────────────────────────────────────────────

  static function on_page_instance_select_multiple_init($page) {
    $group_id = $page->get_args('group_id');
    $entity_name = $page->get_args('entity_name');
    $entities = entity::get_all   ();
    $groups   = entity::get_groups();
    $entities_by_groups = [];
    core::array_sort_text($groups);
    foreach ($groups as $c_id => $c_title) {
      foreach ($entities as $c_name => $c_entity)
        if ($c_id == $c_entity->get_group_id())
          $entities_by_groups[$c_id][$c_name] = $c_entity;
      core::array_sort_by_title(
        $entities_by_groups[$c_id]
      );
    }
  # ┌───────────────────────────────────────────────────────────┬─────────────────────────────────────────┐
  # │ /manage/instances/select                                  │ group_id != true && entity_name != true │
  # │ /manage/instances/select/      group_id                   │ group_id == true && entity_name != true │
  # │ /manage/instances/select/      group_id/      entity_name │ group_id == true && entity_name == true │
  # │ /manage/instances/select/wrong_group_id                   │ group_id != true && entity_name != true │
  # │ /manage/instances/select/wrong_group_id/      entity_name │ group_id != true && entity_name == true │
  # │ /manage/instances/select/      group_id/wrong_entity_name │ group_id == true && entity_name != true │
  # │ /manage/instances/select/wrong_group_id/wrong_entity_name │ group_id != true && entity_name != true │
  # └───────────────────────────────────────────────────────────┴─────────────────────────────────────────┘
    if (isset($groups[$group_id])                                                        == false) url::go($page->get_args('base').'/'.array_keys($groups)[0].'/'.array_keys($entities_by_groups[array_keys($groups)[0]])[0]);
    if (isset($groups[$group_id]) && isset($entities_by_groups[$group_id][$entity_name]) == false) url::go($page->get_args('base').'/'.           $group_id  .'/'.array_keys($entities_by_groups[           $group_id  ])[0]);
  # make tabs
    foreach ($entities_by_groups as $c_id => $c_entities) {
      tabs_item::insert($groups[$c_id],
              'instance_group_'.$c_id,
          'T:manage_instances', $c_id, null, ['class' => [
                       'group-'.$c_id =>
                       'group-'.$c_id]]);
      foreach ($c_entities as $c_name =>  $c_entity) {
        tabs_item::insert(      $c_entity->title_plural,
             'instance_select_'.$c_name,
              'instance_group_'.$c_id, $c_id.'/'.$c_name, null, ['class' => [
                      'select-'.$c_name =>
                      'select-'.$c_name]]);
      }
    }
  }

  static function on_show_block_instance_select_multiple($page) {
    $entity_name = $page->get_args('entity_name');
    $entity = entity::get($entity_name);
    $link_add_new = new markup('a', ['role' => 'button', 'href' => '/manage/instance/insert/'.$entity_name.'?'.url::back_part_make(), 'title' => new text('Add new instance of type %%_name on new page.', ['name' => translation::get($entity->title)]), 'class' => ['link-add-new-instance' => 'link-add-new-instance']], new text('add'));
    if ($entity) {
      $selection = new selection('', $entity->view_type_multiple);
      $selection->id = 'instances_manage';
      $selection->is_paged = true;
      foreach ($entity->selection_params as $c_key => $c_value) {
        $selection->{$c_key} = $c_value;
      }
      $has_visible_fields = false;
      foreach ($entity->fields as $c_name => $c_field) {
        if (!empty($c_field->field_is_visible_on_select)) {
          $has_visible_fields = true;
          $selection->field_insert_entity(null, $entity->name, $c_name);
        }
      }
      if (!$has_visible_fields) {
        return new block('', ['class' => [$entity->name => $entity->name]], [
          $link_add_new,
          new markup('x-no-result', [], 'no visible fields')
        ]);
      } else {
        $selection->field_insert_checkbox(null, '', 80);
        $selection->field_insert_action();
        return new block('', ['class' => [$entity->name => $entity->name]], [
          $link_add_new,
          $selection
        ]);
      }
    }
  }

}}