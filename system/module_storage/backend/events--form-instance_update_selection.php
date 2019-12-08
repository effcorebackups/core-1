<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\entity;
          use \effcore\field_number;
          use \effcore\fieldset;
          use \effcore\page;
          use \effcore\widget_selection_fields;
          abstract class events_form_instance_update_selection {

  static function on_init($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      if ($entity->name == 'selection' && !empty($form->_instance)) {
        $fieldset_fields           = new fieldset('Fields');
        $fieldset_query_params     = new fieldset('Query parameters');
        $fieldset_decorator_params = new fieldset('Decorator parameters');
        $fieldset_conditions       = new fieldset('Conditions');
        $fieldset_sequence         = new fieldset('Sequence');
      # init pool of fields
        if ($form->validation_cache_get('fields') === null)
            $form->validation_cache_set('fields', $form->_instance->fields ?: []);
      # insert widget 'Fields'
        $widget_fields = new widget_selection_fields($form->validation_cache_get('fields'));
        $widget_fields->build();
        $fieldset_fields->child_insert($widget_fields, 'widget_fields');
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
        $form->child_select('fields')->child_insert($fieldset_fields,           'fields'          );
        $form->child_select('fields')->child_insert($fieldset_query_params,     'query_params'    );
        $form->child_select('fields')->child_insert($fieldset_decorator_params, 'decorator_params');
      }
    }
  }

  static function on_submit($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      if ($entity->name == 'selection' && !empty($form->_instance)) {
        if ($form->clicked_button->value_get() == 'update')
          $form->_instance->fields = $form->validation_cache_get('fields') ?: null;
        else {
          static::on_init(null, $form, $items);
        }
      }
    }
  }

}}