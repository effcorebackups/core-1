<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\entity;
          use \effcore\field_number;
          use \effcore\fieldset;
          use \effcore\text;
          abstract class events_form_instance_update__selection {

  static function on_init($event, $form, $items) {
    if ($form->has_error_on_init === false) {
      $entity = entity::get($form->entity_name);
      if ($entity) {
        if ($entity->name === 'selection' && !empty($form->_instance)) {
          $fieldset_settings_query     = new fieldset('Query settings');
          $fieldset_settings_decorator = new fieldset('Decorator settings');
          $fieldset_settings_pager     = new fieldset('Pager settings');
          $fieldset_conditions         = new fieldset('Conditions');
          $fieldset_sequence           = new fieldset('Sequence');
        # insert field 'Limit'
          $field_limit = new field_number('Limit');
          $field_limit->build();
          $field_limit->name_set('limit');
          $field_limit->value_set(1);
          $field_limit->min_set(1    );
          $field_limit->max_set(10000);
        # fill the form
          $fieldset_settings_query     ->child_insert($fieldset_conditions,         'conditions'        );
          $fieldset_settings_query     ->child_insert($fieldset_sequence,           'sequence'          );
          $fieldset_settings_query     ->child_insert($field_limit,                 'limit'             );
          $form->child_select('fields')->child_insert($fieldset_settings_query,     'settings_query'    );
          $form->child_select('fields')->child_insert($fieldset_settings_decorator, 'settings_decorator');
          $form->child_select('fields')->child_insert($fieldset_settings_pager,     'settings_pager'    );
        }
      }
    }
  }

  static function on_validate($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'update':
          if ($entity->name === 'selection') {
            if (count($items['*fields']->value_get_complex()) < 1) {
              $form->error_set('Group "%%_title" should contain a minimum %%_number item%%_plural{number|s}!', ['title' => (new text($items['*fields']->title))->render(), 'number' => 1]);
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
          if ($entity->name === 'selection' && !empty($form->_instance)) {
          }
          break;
      }
    }
  }

}}