<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\entity;
          use \effcore\page;
          use \effcore\url;
          abstract class events_form_instance_select {

  static function on_submit($event, $form, $items) {
    $back_return = page::get_current()->args_get('back_return');
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    switch ($form->clicked_button->value_get()) {
      case 'return':
        if ($entity->name == 'tree_item' && !empty($form->_instance)) {
          url::go(url::back_url_get() ?: ($back_delete ?: '/manage/data/select_multiple/'.$entity->group_managing_get_id().'/'.$entity->name.'/'.$form->_instance->id_tree));
          break;
        }
    }
  }

}}