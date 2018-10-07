<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\manage_instances;
          use \effcore\message;
          use \effcore\translation;
          use \effcore\page;
          abstract class events_form_instance_delete {

  static function on_submit($form, $items) {
    $entity_name = page::current_get()->args_get('entity_name');
    $instance_id = page::current_get()->args_get('instance_id');
    switch ($form->clicked_button->value_get()) {
      case 'delete':
        $result = manage_instances::instance_delete_by_entity_name_and_instance_id(
          page::current_get(), $form, $items
        );
        if ($result) {
             message::insert(translation::get('Instance of entity "%%_entity_name" with id = "%%_instance_id" was deleted.',     ['entity_name' => $entity_name, 'instance_id' => $instance_id]));}
        else message::insert(translation::get('Instance of entity "%%_entity_name" with id = "%%_instance_id" was not deleted!', ['entity_name' => $entity_name, 'instance_id' => $instance_id]), 'error');
        break;
    }
  }

}}