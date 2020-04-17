<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\entity;
          use \effcore\field_number;
          use \effcore\fieldset;
          use \effcore\translation;
          abstract class events_form_instance_update_selection {

  static function on_init($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      if ($entity->name == 'selection' && !empty($form->_instance)) {
        $fieldset_query_params     = new fieldset('Query parameters');
        $fieldset_decorator_params = new fieldset('Decorator parameters');
        $fieldset_conditions       = new fieldset('Conditions');
        $fieldset_sequence         = new fieldset('Sequence');
      # insert field 'Limit'
        $field_limit = new field_number('Limit');
        $field_limit->build();
        $field_limit->name_set('limit');
        $field_limit->value_set(1);
        $field_limit->min_set(1    );
        $field_limit->max_set(10000);
      # fill the form
        $fieldset_query_params       ->child_insert($fieldset_conditions,       'conditions'      );
        $fieldset_query_params       ->child_insert($fieldset_sequence,         'sequence'        );
        $fieldset_query_params       ->child_insert($field_limit,               'limit'           );
        $form->child_select('fields')->child_insert($fieldset_query_params,     'query_params'    );
        $form->child_select('fields')->child_insert($fieldset_decorator_params, 'decorator_params');
      }
    }
  }

  static function on_validate($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'update':
          if ($entity->name == 'selection') {
            if (count($items['*fields']->value_get_complex()) < 1) {
              $form->error_set('Group "%%_title" should contain a minimum %%_number item%%_plural{number,s}!', ['title' => translation::get($items['*fields']->title), 'number' => 1]);
            }
          }
          break;
      }
    }
  }

  static function on_submit($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'update':
          if ($entity->name == 'selection' && !empty($form->_instance)) {
          }
          break;
      }
    }
  }

}}