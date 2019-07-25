<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\entity;
          use \effcore\language;
          use \effcore\page;
          abstract class events_form_instance_insert {

  static function on_init($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      if ($entity->name == 'page' && !empty($form->_instance)) {
        $items['#lang_code']->value_set(
          language::code_get_current()
        );
      }
    }
  }

}}