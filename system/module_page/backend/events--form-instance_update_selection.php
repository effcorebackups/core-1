<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\core;
          use \effcore\entity;
          use \effcore\field_number;
          use \effcore\fieldset;
          use \effcore\group_selection_field_insert;
          use \effcore\page;
          abstract class events_form_instance_update_selection {

  static function on_init($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      if ($entity->name == 'selection' && !empty($form->_instance)) {
        $fields           = new fieldset('Fields');
        $query_params     = new fieldset('Query parameters');
        $decorator_params = new fieldset('Decorator parameters');
        $conditions       = new fieldset('Conditions');
        $order            = new fieldset('Order');

        $field_insert = new group_selection_field_insert;
        $field_insert->build();
        $fields->child_insert($field_insert, 'field_insert');

        $limit = new field_number('Limit');
        $limit->build();
        $limit->name_set('limit');
        $limit->value_set(1);
        $limit->min_set(1);
        $limit->max_set(10000);

        $query_params                ->child_insert($conditions,       'conditions'      );
        $query_params                ->child_insert($order,            'order'           );
        $query_params                ->child_insert($limit,            'limit'           );
        $form->child_select('fields')->child_insert($fields,           'fields'          );
        $form->child_select('fields')->child_insert($query_params,     'query_params'    );
        $form->child_select('fields')->child_insert($decorator_params, 'decorator_params');
      }
    }
  }

  static function on_submit($event, $form, $items) {
  }

}}