<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\entity;
          use \effcore\language;
          use \effcore\markup;
          use \effcore\page;
          use \effcore\text_multiline;
          use \effcore\text;
          use \effcore\translation;
          abstract class events_form_instance_insert {

  static function on_init($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      if ($entity->name == 'page') {
      # field 'lang_code'
        $items['#lang_code']->value_set(
          language::code_get_current()
        );
      # layout and its parts
        $form->child_select('fields')->child_insert(
          new markup('x-layout-message', [], ['message' => new text(
            'Layout editing will be available after saving the page.')
          ], -20), 'layout_message'
        );
      }
    }
  }

  static function on_validate($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'insert':
        case 'insert_and_update':
        # field 'id'
          if ($entity->name == 'page') {
            if ($items['#id']->value_get()) { # check the uniqueness of SQL + NoSQL data
              if (page::get($items['#id']->value_get())) {
                $items['#id']->error_set(new text_multiline([
                  'Field "%%_title" contains the previously used value!',
                  'Only unique value is allowed.'], ['title' => translation::get($items['#id']->title)]
                ));
              }
            }
          }
        # field 'url'
          if ($entity->name == 'page') {
            if ($items['#url']->value_get()) { # check the uniqueness of SQL + NoSQL data
              if (page::get_by_url($items['#url']->value_get(), false)) {
                $items['#url']->error_set(new text_multiline([
                  'Field "%%_title" contains the previously used value!',
                  'Only unique value is allowed.'], ['title' => translation::get($items['#url']->title)]
                ));
              }
            }
          }
          break;
      }
    }
  }

}}