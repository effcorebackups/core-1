<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\entity;
          use \effcore\language;
          use \effcore\page;
          use \effcore\text_multiline;
          use \effcore\translation;
          abstract class events_form_instance_insert {

  static function on_init($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
    # field 'lang_code'
      if ($entity->name == 'page' && !empty($form->_instance)) {
        $items['#lang_code']->value_set(
          language::code_get_current()
        );
      }
    }
  }

  static function on_validate($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
    # field 'id'
      if ($entity->name == 'page' && !empty($form->_instance)) {
        if ($items['#id']->value_get()) {
          if (page::get($items['#id']->value_get())) {
            $items['#id']->error_set(new text_multiline([
              'Field "%%_title" contains the previously used value!',
              'Only unique value is allowed.'], ['title' => translation::get($items['#id']->title)]
            ));
          }
        }
      }
    # field 'url'
      if ($entity->name == 'page' && !empty($form->_instance)) {
        if ($items['#url']->value_get()) {
          if (page::get_by_url($items['#url']->value_get(), false)) {
            $items['#url']->error_set(new text_multiline([
              'Field "%%_title" contains the previously used value!',
              'Only unique value is allowed.'], ['title' => translation::get($items['#url']->title)]
            ));
          }
        }
      }
    }
  }

}}