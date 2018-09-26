<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class manage_instances {

  static function instance_select($page) {
    $entities = entity::all_get(false);
    core::array_sort_by_property($entities, 'title');
    if (!$page->args_get('id')) url::go($page->args_get('base').'/select/'.reset($entities)->name);
    foreach ($entities as $c_entity) {
      tabs::item_insert($c_entity->title_plural, 'instance_select_'.$c_entity->name, 'instance_select', 'select/'.$c_entity->name);
    }
    $entity = entity::get($page->args_get('id'));
    $selection = new selection;
    foreach ($entity->fields as $c_name => $c_info) {
      if (!isset($c_info->hidden) ||
                !$c_info->hidden) {
        $selection->field_insert($entity->name, $c_name);
      }
    }
    $markup = $selection->build();
    return new block('', ['class' => [$entity->name => $entity->name]],
      $markup
    );
  }

  static function instance_insert($page) {
    $entities = entity::all_get(false);
    core::array_sort_by_property($entities, 'title');
    if (!$page->args_get('id')) url::go($page->args_get('base').'/insert/'.reset($entities)->name);
    foreach ($entities as $c_entity) {
      tabs::item_insert($c_entity->title, 'instance_insert_'.$c_entity->name, 'instance_insert', 'insert/'.$c_entity->name);
    }
    return new text('instance_insert is UNDER CONSTRUCTION');
  }

  static function instance_update($page) {
    return new text('instance_update is UNDER CONSTRUCTION');
  }

  static function instance_delete($page) {
    return new text('instance_delete is UNDER CONSTRUCTION');
  }

}}