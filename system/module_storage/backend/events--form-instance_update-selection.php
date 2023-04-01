<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\core;
          use \effcore\entity;
          use \effcore\field_number;
          use \effcore\fieldset;
          use \effcore\form_part;
          use \effcore\text;
          abstract class events_form_instance_update__selection {

  static function on_build($event, $form) {
    if ($form->has_error_on_build === false &&
        $form->has_no_fields      === false) {
      $entity = entity::get($form->entity_name);
      if ($entity->name === 'selection') {
        $form->child_select('fields')->child_select('fields')            ->main_entity_name = $form->_instance->main_entity_name;
        $form->child_select('fields')->child_select('decorator_settings')->main_entity_name = $form->_instance->main_entity_name;
        $form->child_select('fields')->child_insert(form_part::get('form_instance_update__selection_query_settings'), 'query_settings');
        $form->child_select('fields')->child_insert(form_part::get('form_instance_update__selection_pager_settings'), 'pager_settings');
      }
    }
  }

  static function on_init($event, $form, $items) {
    if ($form->has_error_on_build === false &&
        $form->has_no_fields      === false) {
      $entity = entity::get($form->entity_name);
      if ($entity->name === 'selection') {
        $query_settings = $form->_instance->query_settings;
        $data           = $form->_instance->data;
        if (isset($query_settings['conditions'])) $items['#conditions_data']->value_data_set($query_settings['conditions'], 'conditions');
        if (isset($query_settings['order'     ])) $items['#order_data'     ]->value_data_set($query_settings['order'], 'order');
        if (isset($query_settings['limit'     ])) $items['#limit'          ]->     value_set($query_settings['limit']);
        if (isset($data['pager_is_enabled'])) $items['#pager_is_enabled']->checked_set($data['pager_is_enabled']);
        if (isset($data['pager_name'      ])) $items['#pager_name'      ]->  value_set($data['pager_name']);
        if (isset($data['pager_id'        ])) $items['#pager_id'        ]->  value_set($data['pager_id']);
      }
    }
  }

  static function on_validate($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    switch ($form->clicked_button->value_get()) {
      case 'update':
        if ($entity->name === 'selection') {
          if (count($items['*fields']->value_get()) < 1) {
            $form->error_set('Group "%%_title" should contain a minimum %%_number item%%_plural{number|s}!', ['title' => (new text($items['*fields']->title))->render(), 'number' => 1]);
          }
        }
        break;
    }
  }

  static function on_submit($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    switch ($form->clicked_button->value_get()) {
      case 'update':
        if ($entity->name === 'selection') {
          $conditions = $items['#conditions_data']->value_data_get()->conditions ?? null;
          $order      = $items['#order_data'     ]->value_data_get()->order      ?? null;
          $query_settings['conditions'] = is_array($conditions) ? $conditions : [];
          $query_settings['order'     ] = is_array($order     ) ? $order      : [];
          $query_settings['limit'     ] = $items['#limit']->value_get();
          $data = $form->_instance->data;
          $data['pager_is_enabled'] = $items['#pager_is_enabled']->checked_get();
          $data['pager_name'      ] = $items['#pager_name'      ]->  value_get();
          $data['pager_id'        ] = $items['#pager_id'        ]->  value_get();
          $form->_instance->query_settings = $query_settings;
          $form->_instance->data           = $data;
        }
        break;
    }
  }

}}
