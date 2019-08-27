<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\page;
          use \effcore\entity;
          abstract class events_page {

  static function on_breadcrumbs_build_before($event, $breadcrumbs) {
    $managing_group_id = page::get_current()->args_get('managing_group_id');
    $entity_name       = page::get_current()->args_get('entity_name'      );
    $category_id       = page::get_current()->args_get('category_id'      );
    if (page::get_current()->id == 'instance_insert') {
      $groups = entity::groups_managing_get();
      $entity = entity::get($entity_name);
      $breadcrumbs->link_insert('entity_group', $groups[$managing_group_id], '/manage/data/'.$managing_group_id                  );
      $breadcrumbs->link_insert('entity',       $entity->title,              '/manage/data/'.$managing_group_id.'/'.$entity->name);
    }
  }

}}