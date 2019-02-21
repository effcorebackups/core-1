<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class selection extends markup implements has_external_cache {

  public $tag_name = 'x-selection';
  public $view_type = 'table'; # table | ul | dl
  public $id;
  public $title;
  public $fields = [];
  public $query_params = [];
  public $decorator_params = [];
  public $limit = 50;
  public $is_paged = true;

  function __construct($title = '', $view_type = null, $weight = 0) {
    if ($title)     $this->title     = $title;
    if ($view_type) $this->view_type = $view_type;
    parent::__construct(null, [], [], $weight);
  }

  function build() {
    $this->children_delete_all();
    $this->attribute_insert('data-view-type', $this->view_type);
    $this->attribute_insert('data-id', $this->id);
    event::start('on_selection_before_build', $this->id, [&$this]);

    $used_entities = [];
    $used_storages = [];

  # analyze fields
    foreach ($this->fields as $c_field) {
      if ($c_field->type == 'field' ||
          $c_field->type == 'join_field') {
        $c_entity = entity::get($c_field->entity_name, false);
        $used_entities[$c_entity->name]         = $c_entity->name;
        $used_storages[$c_entity->storage_name] = $c_entity->storage_name;
      }
    }

  # prepare the query and request data from the storage
    if (count($used_storages) == 1) {
      $main_entity = entity::get(reset($used_entities));
      $this->attribute_insert('data-main-entity', $main_entity->name);
      $id_keys = $main_entity->real_id_get();
    # prepare join_fields
      foreach ($this->fields as $c_id => $c_field) {
        if ($c_field->type == 'join_field') {
          $this->query_params['join_fields'][$c_id.'_!f'] = '~'.$c_field->entity_name.'.'.$c_field->field_name;
        }
      }
      foreach ($this->join ?? [] as $c_id => $c_join) {
        $this->query_params['join'][$c_id] = [
          'type'      => 'LEFT OUTER JOIN',
          'target_!t' => '~'.$c_join->entity_name,                            'on' => 'ON',
          'left_!f'   => '~'.$c_join->entity_name   .'.'.$c_join->field_name, 'operator' => '=',
          'right_!f'  => '~'.$c_join->on_entity_name.'.'.$c_join->on_field_name
        ];
      }
      $this->query_params['limit'] = $this->limit;
      $instances = $main_entity->instances_select(
        $this->query_params
      );
    } else {
      message::insert(
        translation::get('Distributed queries not supported! Selection id: %%_id', ['id' => $this->id]), 'warning'
      );
      return new node();
    }

  # wrap the result in the decorator
    $result = null;
    if (isset($instances) &&
        count($instances)) {

      $decorator = new decorator($this->view_type);
      foreach ($this->decorator_params as $c_key => $c_value) {
        $decorator->{$c_key} = $c_value;
      }

      foreach ($instances as $c_instance) {
        $c_row = [];
        foreach ($this->fields as $c_rowid => $c_field) {
          switch ($c_field->type) {
            case 'field':
            case 'join_field':
              $c_entity = entity::get($c_field->entity_name, false);
              $c_title      = $c_entity->fields[$c_field->field_name]->title;
              $c_value_type = $c_entity->fields[$c_field->field_name]->type;
              $c_value      = $c_instance->    {$c_field->field_name};
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
                'value' => $id_keys ? $this->action_list_get($main_entity, $c_instance, $id_keys) : ''
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

      $this->child_insert(
        $decorator->build(), 'result'
      );
      if ($this->is_paged) {
        $pager = new pager();
        if ($pager->has_error) {
          core::send_header_and_exit('page_not_found');
        } else {
          $this->child_insert(
            $pager, 'pager'
          );
        }
      }
    } else {
      $this->child_insert(
        new markup('x-no-result', [], 'no items'), 'no_result'
      );
    }

    event::start('on_selection_after_build', $this->id, [&$this]);
    return $this;
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

  function field_entity_insert($row_id = null, $entity_name, $field_name) {
    $this->fields[$row_id ?: $entity_name.'.'.$field_name] = (object)[
      'type'        => 'field',
      'entity_name' => $entity_name,
      'field_name'  => $field_name
    ];
  }

  function field_action_insert($row_id = null, $title = '') {
    $this->fields[$row_id ?: 'actions'] = (object)[
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
    $this->build();
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