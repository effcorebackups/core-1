<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class selection extends markup implements has_external_cache {

  public $tag_name = 'x-selection';
  public $template = 'container';
  public $title_tag_name = 'h2';
  public $view_type = 'table'; # table | ul | dl | tree
  public $id;
  public $title;
  public $fields = [];
  public $query_params = [];
  public $decorator_params = [];
  public $limit = 50;
  public $is_paged = false;
  public $pager_name = 'page';
  public $pager_id = 0;

  function __construct($title = '', $view_type = null, $weight = 0) {
    if ($title    ) $this->title     = $title;
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

  # sort fields
    foreach ($this->fields as $c_row_id => $c_field)
      if (!property_exists($c_field, 'weight'))
        $c_field->weight = 0;
    core::array_sort_by_weight($this->fields, 3);

  # analyze fields
    foreach ($this->fields as $c_row_id => $c_field) {
      if ($c_field->type == 'field' ||
          $c_field->type == 'join_field') {
        $c_entity = entity::get($c_field->entity_name, false);
        $used_entities[$c_entity->name]         = $c_entity->name;
        $used_storages[$c_entity->storage_name] = $c_entity->storage_name;
      }
    }

  # ─────────────────────────────────────────────────────────────────────
  # prepare the query and request data from the storage
  # ─────────────────────────────────────────────────────────────────────
    if (count($used_storages) == 1) {
      $main_entity = entity::get(reset($used_entities));
      $this->attribute_insert('data-main-entity', $main_entity->name);
      $id_keys = $main_entity->real_id_get();

    # prepare query params
      foreach ($this->fields as $c_row_id => $c_field) {
        if ($c_field->type == 'join_field') {
          $this->query_params['join_fields'][$c_row_id.'_!f'] = '~'.$c_field->entity_name.'.'.$c_field->entity_field_name;
        }
      }
      foreach ($this->join ?? [] as $c_id => $c_join) {
        $this->query_params['join'][$c_id] = [
          'type'      => 'LEFT OUTER JOIN',
          'target_!t' => '~'.$c_join->   entity_name,                                   'on'       => 'ON',
          'left_!f'   => '~'.$c_join->   entity_name.'.'.$c_join->   entity_field_name, 'operator' => '=',
          'right_!f'  => '~'.$c_join->on_entity_name.'.'.$c_join->on_entity_field_name
        ];
      }
      $this->query_params['limit'] = $this->limit;

    # prepare pager
      if ($this->is_paged) {
        $instances_count = $main_entity->instances_select_count($this->query_params);
        if ($instances_count > 0) {
          $page_max_number = ceil($instances_count / $this->limit);
          $pager = new pager(1, $page_max_number,
            $this->pager_name,
            $this->pager_id, [], -20);
          if ($pager->error_code_get() && $pager->error_code_get() == pager::ERR_CODE_CUR_GT_MAX) {url::go($pager->last_page_url_get()->tiny_get());}
          if ($pager->error_code_get() && $pager->error_code_get() != pager::ERR_CODE_CUR_GT_MAX) {
            core::send_header_and_exit('page_not_found');
          } else {
            $this->query_params['offset'] = ($pager->cur - 1) * $this->limit;
            $this->child_insert(
              $pager, 'pager'
            );
          }
        }
      }

    # select instances
      $instances = $main_entity->instances_select(
        $this->query_params
      );

    } elseif (count($used_storages) == 0) {
      message::insert(new text(
        'No fields for select from storage! Selection id = "%%_id"', ['id' => $this->id]), 'error'
      );
      return new node();
    } elseif (count($used_storages) >= 2) {
      message::insert(new text(
        'Distributed queries not supported! Selection id = "%%_id"', ['id' => $this->id]), 'warning'
      );
      return new node();
    }

  # ─────────────────────────────────────────────────────────────────────
  # wrap the result in the decorator
  # ─────────────────────────────────────────────────────────────────────
    $result = null;
    if (isset($instances) &&
        count($instances)) {

      $decorator = new decorator($this->view_type);
      foreach ($this->decorator_params as $c_key => $c_value) {
        $decorator->{$c_key} = $c_value;
      }

      foreach ($instances as $c_instance) {
        $c_row = [];
        foreach ($this->fields as $c_row_id => $c_field) {
          switch ($c_field->type) {
            case 'field':
            case 'join_field':
              $c_entity = entity::get($c_field->entity_name, false);
              $c_title      = $c_entity->fields[$c_field->entity_field_name]->title;
              $c_value_type = $c_entity->fields[$c_field->entity_field_name]->type;
              $c_value      = $c_instance->    {$c_field->entity_field_name};
              if ($c_value_type == 'real'    ) $c_value = locale::format_number  ($c_value, 10);
              if ($c_value_type == 'integer' ) $c_value = locale::format_number  ($c_value);
              if ($c_value_type == 'date'    ) $c_value = locale::format_date    ($c_value);
              if ($c_value_type == 'time'    ) $c_value = locale::format_time    ($c_value);
              if ($c_value_type == 'datetime') $c_value = locale::format_datetime($c_value);
              if ($c_value_type == 'boolean' ) $c_value = $c_value ? 'Yes' : 'No';
              $c_row[$c_row_id] = [
                'title' => $c_title,
                'value' => $c_value
              ];
              break;
            case 'checkbox':
              $c_id_values = array_intersect_key($c_instance->values, $id_keys);
              $c_form_field = new field_checkbox();
              $c_form_field->build();
              $c_form_field->name_set('is_checked[]');
              $c_form_field->value_set(implode('+', $c_id_values));
              $c_row[$c_row_id] = [
                'title' => $c_field->title ?? '',
                'value' => $id_keys ? $c_form_field : ''
              ];
              break;
            case 'actions':
              $c_row[$c_row_id] = [
                'title' => $c_field->title ?? '',
                'value' => $id_keys ? $this->action_list_get($main_entity, $c_instance, $id_keys) : ''
              ];
              break;
            case 'markup':
              $c_row[$c_row_id] = [
                'title' => $c_field->title,
                'value' => $c_field->markup
              ];
              break;
            case 'code':
              $c_row[$c_row_id] = [
                'title' => $c_field->title,
                'value' => $c_field->code->call($this, $c_instance)
              ];
              break;
          }
        }
        $decorator->data[] = $c_row;
      }

      $this->child_insert(
        $decorator->build(), 'result'
      );

    } else {
      $this->child_insert(
        new markup('x-no-result', [], 'no items'), 'no_result'
      );
    }

    event::start('on_selection_after_build', $this->id, [&$this]);
    return $this;
  }

  # ─────────────────────────────────────────────────────────────────────
  # custom fields
  # ─────────────────────────────────────────────────────────────────────

  function field_insert_entity($row_id = null, $entity_name, $entity_field_name, $weight = 0) {
    $field = new \stdClass;
    $field->type = 'field';
    $field->entity_name = $entity_name;
    $field->entity_field_name = $entity_field_name;
    $field->weight = $weight;
    $this->fields[$row_id ?: $entity_name.'.'.$entity_field_name] = $field;
  }

  function field_insert_checkbox($row_id = null, $title = '', $weight = 0) {
    $field = new \stdClass;
    $field->type = 'checkbox';
    $field->title = $title;
    $field->weight = $weight;
    $this->fields[$row_id ?: 'checkbox'] = $field;
  }

  function field_insert_action($row_id = null, $title = '', $weight = 0) {
    $field = new \stdClass;
    $field->type = 'actions';
    $field->title = $title;
    $field->weight = $weight;
    $this->fields[$row_id ?: 'actions'] = $field;
  }

  function field_insert_markup($row_id = null, $title = '', $markup, $weight = 0) {
    $field = new \stdClass;
    $field->type = 'markup';
    $field->title = $title;
    $field->markup = $markup;
    $field->weight = $weight;
    $this->fields[$row_id ?: 'markup'] = $field;
  }

  function field_insert_code($row_id = null, $title = '', $code, $weight = 0) {
    $field = new \stdClass;
    $field->type = 'code';
    $field->title = $title;
    $field->code = $code;
    $field->weight = $weight;
    $this->fields[$row_id ?: 'code'] = $field;
  }

  function action_list_get($entity, $instance, $id_keys) {
    $id_values = array_intersect_key($instance->values, $id_keys);
    $action_list = new actions_list();
                                    $action_list->action_add('/manage/instance/select/'.$entity->name.'/'.join('+', $id_values).'?'.url::back_part_make(), 'select');
                                    $action_list->action_add('/manage/instance/update/'.$entity->name.'/'.join('+', $id_values).'?'.url::back_part_make(), 'update');
    if (empty($instance->is_embed)) $action_list->action_add('/manage/instance/delete/'.$entity->name.'/'.join('+', $id_values).'?'.url::back_part_make(), 'delete');
    return $action_list;
  }

  # ─────────────────────────────────────────────────────────────────────
  # render
  # ─────────────────────────────────────────────────────────────────────

  function render_self() {
    return $this->title ? (
      new markup($this->title_tag_name, [], $this->title
    ))->render() : '';
  }

  function render() {
    $this->build();
    if ($this->template) {
      return (template::make_new($this->template, [
        'tag_name'   => $this->tag_name,
        'attributes' => $this->render_attributes(),
        'self_t'     => $this->render_self(),
        'children'   => $this->render_children($this->children_select())
      ]))->render();
    } else {
      return $this->render_self().
             $this->render_children($this->children);
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function get_not_external_properties() {
    return ['id' => 'id'];
  }

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    foreach (storage::get('files')->select('selections') as $c_module_id => $c_selections) {
      foreach ($c_selections as $c_row_id => $c_selection) {
        if (isset(static::$cache[$c_selection->id])) console::log_insert_about_duplicate('selection', $c_selection->id, $c_module_id);
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