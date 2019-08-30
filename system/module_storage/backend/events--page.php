<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\entity;
          use \effcore\page;
          use \effcore\url;
          abstract class events_page {

  static function on_breadcrumbs_build_before($event, $breadcrumbs) {
    $managing_group_id = page::get_current()->args_get('managing_group_id');
    $entity_name       = page::get_current()->args_get('entity_name'      );
    $back_return_0     = page::get_current()->args_get('back_return_0'    );
    $back_return_n     = page::get_current()->args_get('back_return_n'    );
    if (page::get_current()->id == 'instance_select' ||
        page::get_current()->id == 'instance_insert' ||
        page::get_current()->id == 'instance_update' ||
        page::get_current()->id == 'instance_delete') {
      $breadcrumbs->is_remove_last_link = false;
      $groups = entity::groups_managing_get();
      $entity = entity::get($entity_name);
      $breadcrumbs->link_insert('entity_group', $groups[$managing_group_id],                                                              '/manage/data/'.$managing_group_id                    );
      $breadcrumbs->link_insert('entity',       $entity->title,              $back_return_0 ?: (url::back_url_get() ?: ($back_return_n ?: '/manage/data/'.$managing_group_id.'/'.$entity->name)));
    }
  }

}}