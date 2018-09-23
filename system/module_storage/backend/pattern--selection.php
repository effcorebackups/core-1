<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class selection
          implements has_external_cache {

  public $view_type = 'table';
  public $fields;
  public $conditions;
  public $order;
  public $count;
  public $offset;

  function make_markup() {
    $markup = null;
    $used_entities = [];
    $used_storages = [];
    foreach ($this->fields as $c_field) {
      $c_entity = entity::get($c_field->entity_name, false);
      $used_entities[$c_entity->name]       = $c_entity->name;
      $used_storages[$c_entity->storage_id] = $c_entity->storage_id;
    }
  # get data from storage
    if (count($used_entities) == 1 &&
        count($used_storages) == 1) {
      $entity    = entity::get(reset($used_entities));
      $instances = entity::get(reset($used_entities))->instances_select();
    }
  # make markup
    if (!empty($entity)) {
      switch ($this->view_type) {
        case 'table':
          $thead = [];
          $tbody = [];
        # make thead
          foreach ($this->fields as $c_field) {
            $thead[] = new table_head_row_cell(['class' => [$c_field->field_name => $c_field->field_name]],
              $entity->fields[$c_field->field_name]->title
            );
          }
        # make tbody
          foreach ($instances as $c_instance) {
            $c_tbody_row = [];
            foreach ($this->fields as $c_field)
              $c_tbody_row[] = new table_body_row_cell(['class' => [$c_field->field_name => $c_field->field_name]],
                $c_instance->{$c_field->field_name}
              );
            $tbody[] = $c_tbody_row;
          }
          return new table([], $tbody, [$thead]);
      }
    }
  }

  function field_insert($entity_name, $field_name) {
    $this->fields[$entity_name.'.'.$field_name] = (object)[
      'entity_name' => $entity_name,
      'field_name'  => $field_name
    ];
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function not_external_properties_get() {
    return [];
  }

  static function init() {
    foreach (storage::get('files')->select('selections') as $c_module_id => $c_selections) {
      foreach ($c_selections as $c_row_id => $c_selection) {
        if (isset(static::$cache[$c_row_id])) console::log_about_duplicate_add('selection', $c_row_id);
        static::$cache[$c_row_id] = $c_selection;
        static::$cache[$c_row_id]->module_id = $c_module_id;
      }
    }
  }

  static function get($row_id, $load = true) {
    if (static::$cache == null) static::init();
    if (static::$cache[$row_id] instanceof external_cache && $load)
        static::$cache[$row_id] = static::$cache[$row_id]->external_cache_load();
    return static::$cache[$row_id] ?? null;
  }

}}