<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\entity;
          abstract class events_form_instance_insert__selection {

  static function on_init($event, $form, $items) {
    if ($form->has_error_on_build === false &&
        $form->has_no_fields      === false) {
      $entity = entity::get($form->entity_name);
      if ($entity->name === 'selection') {
      }
    }
  }

}}