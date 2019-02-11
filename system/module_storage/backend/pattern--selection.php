<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class selection extends node implements has_external_cache {

  public $id;
  public $view_type = 'table'; # table | ul | dl
  public $title;
  public $fields = [];
  public $params = [];

  function __construct($title = '', $view_type = null, $weight = 0) {
    if ($title)     $this->title     = $title;
    if ($view_type) $this->view_type = $view_type;
    parent::__construct([], [], $weight);
  }

  function build() {
    $used_entities = [];
    $used_storages = [];

  # analyze
    foreach ($this->fields as $c_field) {
      if ($c_field->type == 'field') {
        $c_entity = entity::get($c_field->entity_name, false);
        $used_entities[$c_entity->name]         = $c_entity->name;
        $used_storages[$c_entity->storage_name] = $c_entity->storage_name;
      }
    }

  # get data from storage
    if (count($used_storages) == 1 &&
        count($used_entities) == 1) {
      $storage   = storage::get(reset($used_storages));
      $entity    =  entity::get(reset($used_entities));
      $instances =  entity::get(reset($used_entities))->instances_select([
        'join'            => $this->params['join']            ?? [],
        'pure_conditions' => $this->params['pure_conditions'] ?? [],
        'order'           => $this->params['order']           ?? [],
        'limit'           => $this->params['limit']           ?? 50,
        'offset'          => $this->params['offset']          ?? 0
      ]);
      $id_keys = $entity->real_id_get();
    }
    if (count($used_storages) == 1 &&
        count($used_entities) >= 2) {
      # @todo: make functionality (query with inner join)
    }
    if (count($used_storages) >= 2) {
      message::insert(translation::get('Distributed queries not supported! Selection id: %%_id', ['id' => $this->id]), 'warning');
      return new node();
    }

  # make result
    $result = null;
    if (!empty($entity)) {
      if ($instances) {

      // $pager = new pager();
      // if ($pager->has_error) {
      //   core::send_header_and_exit('page_not_found');
      // }

        $decorator = new decorator($this->view_type);
        foreach ($instances as $c_instance) {
          $c_row = [];
          foreach ($this->fields as $c_rowid => $c_field) {
            switch ($c_field->type) {
              case 'field':
                $c_title = $entity->fields[$c_field->field_name]->title;
                $c_value_type = $entity->fields[$c_field->field_name]->type;
                $c_value = $c_instance->{$c_field->field_name};
                if ($c_value_type == 'real')     $c_value = locale::  number_format($c_value, 10);
                if ($c_value_type == 'date')     $c_value = locale::    date_format($c_value);
                if ($c_value_type == 'time')     $c_value = locale::    time_format($c_value);
                if ($c_value_type == 'datetime') $c_value = locale::datetime_format($c_value);
                if ($c_value_type == 'boolean')  $c_value = $c_value ? 'Yes' : 'No';
                $c_row[$c_rowid] = [
                  'title' => $c_title,
                  'value' => $c_value
                ];
                break;
              case 'actions':
                $c_row[$c_rowid] = [
                  'title' => $c_field->title ?? '',
                  'value' => $id_keys ? $this->action_list_get($entity, $c_instance, $id_keys) : ''
                ];
                break;
              case 'markup':
                $c_row[$c_rowid] = [
                  'title' => $c_field->title,
                  'value' => $c_field->markup
                ];
                break;
            }
          }
          $decorator->data[] = $c_row;
        }
             $result = $decorator->build();
      } else $result = new markup('x-no-result', [], 'no items');

    # return result
      return new markup('x-selection', [
        'data-view-type' => $this->view_type,
        'data-entity'    => $entity->name], $result
      );
    }
  }

  function action_list_get($entity, $instance, $id_keys) {
    $id_values = array_intersect_key($instance->values, $id_keys);
    if (empty($instance->is_embed)) {
      $action_list = new control_actions_list();
      $action_list->title = ' ';
      $action_list->action_add(page::current_get()->args_get('base').'/select/'.$entity->name.'/'.join('+', $id_values), 'select');
      $action_list->action_add(page::current_get()->args_get('base').'/update/'.$entity->name.'/'.join('+', $id_values), 'update');
      $action_list->action_add(page::current_get()->args_get('base').'/delete/'.$entity->name.'/'.join('+', $id_values), 'delete');
      return $action_list;
    }
  }

  function field_entity_insert($entity_name, $field_name) {
    $this->fields[$entity_name.'.'.$field_name] = (object)[
      'type'        => 'field',
      'entity_name' => $entity_name,
      'field_name'  => $field_name
    ];
  }

  function field_action_insert($title = 'Actions') {
    $this->fields['actions'] = (object)[
      'type'  => 'actions',
      'title' => $title,
    ];
  }

  function field_markup_insert($row_id, $title, $markup) {
    $this->fields[$row_id] = (object)[
      'type'   => 'markup',
      'title'  => $title,
      'markup' => $markup
    ];
  }

  function render_self() {
    return $this->title ? (new markup('h2', [], $this->title))->render() : '';
  }

  function render() {
    $this->child_delete('markup');
    $this->child_insert($this->build(), 'markup');
    return parent::render();
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function not_external_properties_get() {
    return ['id' => 'id'];
  }

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    foreach (storage::get('files')->select('selections') as $c_module_id => $c_selections) {
      foreach ($c_selections as $c_row_id => $c_selection) {
        if (isset(static::$cache[$c_selection->id])) console::log_about_duplicate_insert('selection', $c_selection->id, $c_module_id);
        static::$cache[$c_selection->id] = $c_selection;
        static::$cache[$c_selection->id]->module_id = $c_module_id;
      }
    }
  }

  static function get($id, $load = true) {
    if (static::$cache == null) static::init();
    if (static::$cache[$id] instanceof external_cache && $load)
        static::$cache[$id] = static::$cache[$id]->external_cache_load();
    return static::$cache[$id] ?? null;
  }

}}