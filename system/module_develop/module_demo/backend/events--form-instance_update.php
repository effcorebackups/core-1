<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use \effcore\entity;
          use \effcore\page;
          use \effcore\text_multiline;
          use \effcore\translation;
          abstract class events_form_instance_update {

  static function on_validate($form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      if ($entity->name == 'demo_data_join') {
        $id_data_new = $items['#id_data']->value_get();
        $id_data_old = $items['#id_data']->value_get_initial();
        if ($id_data_new) {
          $result = $entity->instances_select(['conditions' => [
            'id_data_!f' => 'id_data', '=',
            'id_data_!v' => $id_data_new], 'limit' => 1]);
          if ($result && $id_data_new != $id_data_old) {
            $items['#id_data']->error_set(new text_multiline([
              'Field "%%_title" contains the previously used value!',
              'Only unique value is allowed.'], ['title' => translation::get($items['#id_data']->title)]
            ));
          }
        }
      }
    }
  }

}}