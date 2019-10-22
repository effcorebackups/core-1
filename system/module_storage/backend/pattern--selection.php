<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class selection extends markup implements has_external_cache {

  public $tag_name = 'x-selection';
  public $template = 'container';
  public $title_tag_name = 'h2';
  public $id;
  public $title;
  public $fields = [];
  public $query_params = [];
  public $decorator_params = [];
  public $pager_is_on = false;
  public $pager_name = 'page';
  public $pager_id = 0;
  public $origin = 'nosql'; # nosql | sql

  function __construct($title = '', $weight = 0) {
    if ($title) $this->title = $title;
    parent::__construct(null, [], [], $weight);
  }

  function build() {
    if (!$this->is_builded) {

      $this->children_delete();
      $this->attribute_insert('data-id',        $this->id          );
      event::start('on_selection_build_before', $this->id, [&$this]);

      $used_entities = [];
      $used_storages = [];

    # sort fields
      foreach ($this->fields ?? [] as $c_row_id => $c_field)
        if (!property_exists($c_field, 'weight'))
          $c_field->weight = 0;
      if (!empty($this->fields))
        core::array_sort_by_weight($this->fields, 3);

    # analyze fields
      foreach ($this->fields ?? [] as $c_row_id => $c_field) {
        if ($c_field->type == 'field' ||
            $c_field->type == 'join_field') {
          $c_entity = entity::get($c_field->entity_name, false);
          $used_entities[$c_entity->name        ] = $c_entity->name;
          $used_storages[$c_entity->storage_name] = $c_entity->storage_name;
        }
      }

    # ─────────────────────────────────────────────────────────────────────
    # prepare the query and request data from the storage
    # ─────────────────────────────────────────────────────────────────────
      if (count($used_storages) == 1) {
        $main_entity = entity::get(reset($used_entities));
        $this->_main_entity = $main_entity->name;
        $this->attribute_insert('data-main-entity', $main_entity->name);

      # prepare query params
        foreach ($this->fields as $c_row_id => $c_field) {
          if ($c_field->type == 'join_field') {
            $this->query_params['join_fields'][$c_row_id.'_!f'] = '~'.$c_field->entity_name.'.'.$c_field->entity_field_name;
          }
        }
        foreach ($this->join ?? [] as $c_id => $c_join) {
          $this->query_params['join'][$c_id] = [
              'type'    => 'LEFT OUTER JOIN',
            'target_!t' => '~'.$c_join->   entity_name,                                   'on'       => 'ON',
              'left_!f' => '~'.$c_join->   entity_name.'.'.$c_join->   entity_field_name, 'operator' => '=',
             'right_!f' => '~'.$c_join->on_entity_name.'.'.$c_join->on_entity_field_name
          ];
        }
        if (empty($this->query_params['limit']))
                  $this->query_params['limit'] = 50;

      # prepare pager
        if ($this->pager_is_on) {
          $instances_count = $main_entity->instances_select_count($this->query_params);
          $page_max_number = ceil($instances_count / $this->query_params['limit']);
          if ($page_max_number > 1) {
            $pager = new pager(1, $page_max_number, $this->pager_name, $this->pager_id, [], -20);
            if ($pager->error_code_get() != pager::ERR_CODE_OK && $pager->error_code_get() != pager::ERR_CODE_CUR_GT_MAX) core::send_header_and_exit('page_not_found');
            if ($pager->error_code_get() != pager::ERR_CODE_OK && $pager->error_code_get() == pager::ERR_CODE_CUR_GT_MAX) url::go($pager->last_page_url_get()->tiny_get());
            $this->query_params['offset'] = ($pager->cur - 1) * $this->query_params['limit'];
            $this->child_insert(
              $pager, 'pager'
            );
          }
        }

      # select instances
        $this->_instances = $main_entity->instances_select(
          $this->query_params
        );

      } elseif (count($used_storages) == 0) {
        message::insert(new text(
          'No fields for select from storage! Selection id = "%%_id".', ['id' => $this->id]), 'error'
        );
        return new node();
      } elseif (count($used_storages) >= 2) {
        message::insert(new text(
          'Distributed queries not supported! Selection id = "%%_id".', ['id' => $this->id]), 'warning'
        );
        return new node();
      }

    # ─────────────────────────────────────────────────────────────────────
    # wrap the result in the decorator
    # ─────────────────────────────────────────────────────────────────────
      $result = null;
      if (isset($this->_instances) &&
          count($this->_instances)) {

        $decorator = new decorator;
        $decorator->id = $this->id;
        $decorator->_main_entity = $main_entity->name;
        $decorator->attribute_insert('data-main-entity', $main_entity->name);
        foreach ($this->decorator_params ?? [] as $c_key => $c_value)
          $decorator->                           {$c_key} = $c_value;

        foreach ($this->_instances as $c_instance) {
          $c_row = [];
          foreach ($this->fields as $c_row_id => $c_field) {
          # prepare value to use in decorator
            switch ($c_field->type) {
              case 'field':
              case 'join_field':
                $c_entity     = entity::get($c_field->entity_name, false);
                $c_title      = $c_entity->fields[$c_field->entity_field_name]->title;
                $c_value_type = $c_entity->fields[$c_field->entity_field_name]->type;
                $c_value      = $c_instance->    {$c_field->entity_field_name};
                if ($c_value !== null && $c_value_type == 'real'    ) $c_value = locale::format_number  ($c_value, 10);
                if ($c_value !== null && $c_value_type == 'integer' ) $c_value = locale::format_number  ($c_value    );
                if ($c_value !== null && $c_value_type == 'date'    ) $c_value = locale::format_date    ($c_value    );
                if ($c_value !== null && $c_value_type == 'time'    ) $c_value = locale::format_time    ($c_value    );
                if ($c_value !== null && $c_value_type == 'datetime') $c_value = locale::format_datetime($c_value    );
                if ($c_value !== null && $c_value_type == 'boolean' ) $c_value =                         $c_value ? 'Yes' : 'No';
                $c_row[$c_row_id] = [
                  'title' => $c_title,
                  'value' => $c_value
                ];
                break;
              case 'checkbox':
                $c_form_field = new field_checkbox();
                $c_form_field->build();
                $c_form_field->name_set('is_checked[]');
                $c_form_field->value_set(implode('+', $c_instance->values_id_get()));
                $c_row[$c_row_id] = [
                  'title' => $c_field->title ?? '',
                  'value' => $c_form_field
                ];
                break;
              case 'actions':
                $c_row[$c_row_id] = [
                  'title' => $c_field->title ?? '',
                  'value' => $this->actions_list_get($c_instance, $c_field->allowed)
                ];
                break;
              case 'markup':
                $c_row[$c_row_id] = [
                  'title' => $c_field->title,
                  'value' => $c_field->markup
                ];
                break;
              case 'handler':
                $c_row[$c_row_id] = [
                  'title' => $c_field->title,
                  'value' => call_user_func($c_field->handler, $c_row, $c_instance)
                ];
                break;
              case 'code':
                $c_row[$c_row_id] = [
                  'title' => $c_field->title,
                  'value' => $c_field->code->call($this, $c_row, $c_instance)
                ];
                break;
            }
          # translate or replace tokens in value, if required
            if (is_string($c_row[$c_row_id]['value']) &&
                   strlen($c_row[$c_row_id]['value'])) {
              $c_row[$c_row_id]['value'] = new text($c_row[$c_row_id]['value']);
              $c_row[$c_row_id]['value']->is_apply_translation = !empty($c_field->is_apply_translation) ? true : false;
              $c_row[$c_row_id]['value']->is_apply_tokens      = !empty($c_field->is_apply_tokens     ) ? true : false;
            }
          }
          $decorator->data[] = $c_row;
        }

        $this->child_insert($decorator, 'result');
        $decorator->build();

      } else {
        $this->child_insert(
          new markup('x-no-result', [], 'no items'), 'no_result'
        );
      }

      event::start('on_selection_build_after', $this->id, [&$this]);
      $this->is_builded = true;
      return $this;

    }
  }

  # ─────────────────────────────────────────────────────────────────────
  # custom fields
  # ─────────────────────────────────────────────────────────────────────

  function field_insert_entity($row_id = null, $entity_name, $entity_field_name, $params = []) {
    $row_id = $row_id ?: $entity_name.'.'.$entity_field_name;
    $this->fields[$row_id] = new \stdClass;
    $this->fields[$row_id]->type = 'field';
    $this->fields[$row_id]->entity_name = $entity_name;
    $this->fields[$row_id]->entity_field_name = $entity_field_name;
    foreach ($params as $c_key => $c_value) {
      $this->fields[$row_id]->{$c_key} = $c_value;
    }
  }

  function field_insert_checkbox($row_id = null, $title = '', $params = []) {
    $row_id = $row_id ?: 'checkbox';
    $this->fields[$row_id] = new \stdClass;
    $this->fields[$row_id]->type = 'checkbox';
    $this->fields[$row_id]->title = $title;
    foreach ($params as $c_key => $c_value) {
      $this->fields[$row_id]->{$c_key} = $c_value;
    }
  }

  function field_insert_markup($row_id = null, $title = '', $markup, $params = []) {
    $row_id = $row_id ?: 'markup';
    $this->fields[$row_id] = new \stdClass;
    $this->fields[$row_id]->type = 'markup';
    $this->fields[$row_id]->title = $title;
    $this->fields[$row_id]->markup = $markup;
    foreach ($params as $c_key => $c_value) {
      $this->fields[$row_id]->{$c_key} = $c_value;
    }
  }

  function field_insert_code($row_id = null, $title = '', $code, $params = []) {
    $row_id = $row_id ?: 'code';
    $this->fields[$row_id] = new \stdClass;
    $this->fields[$row_id]->type = 'code';
    $this->fields[$row_id]->title = $title;
    $this->fields[$row_id]->code = $code;
    foreach ($params as $c_key => $c_value) {
      $this->fields[$row_id]->{$c_key} = $c_value;
    }
  }

  function field_insert_handler($row_id = null, $title = '', $handler, $params = []) {
    $row_id = $row_id ?: 'handler';
    $this->fields[$row_id] = new \stdClass;
    $this->fields[$row_id]->type = 'handler';
    $this->fields[$row_id]->title = $title;
    $this->fields[$row_id]->handler = $handler;
    foreach ($params as $c_key => $c_value) {
      $this->fields[$row_id]->{$c_key} = $c_value;
    }
  }

  function field_insert_action($row_id = null, $title = '', $allowed = ['select', 'update', 'delete'], $params = []) {
    $row_id = $row_id ?: 'actions';
    $this->fields[$row_id] = new \stdClass;
    $this->fields[$row_id]->type = 'actions';
    $this->fields[$row_id]->title = $title;
    $this->fields[$row_id]->allowed = $allowed;
    foreach ($params as $c_key => $c_value) {
      $this->fields[$row_id]->{$c_key} = $c_value;
    }
  }

  function actions_list_get($instance, $allowed = ['select', 'update', 'delete']) {
    $actions_list = new actions_list();
    foreach ($allowed as $c_action_name) {
      if ($c_action_name == 'select'                              ) $actions_list->action_insert('/manage/data/'.$instance->entity_get()->group_managing_get_id().'/'.$instance->entity_get()->name.'/'.join('+', $instance->values_id_get()).       '?'.url::back_part_make(), 'select');
      if ($c_action_name == 'update'                              ) $actions_list->action_insert('/manage/data/'.$instance->entity_get()->group_managing_get_id().'/'.$instance->entity_get()->name.'/'.join('+', $instance->values_id_get()).'/update?'.url::back_part_make(), 'update');
      if ($c_action_name == 'delete' && empty($instance->is_embed)) $actions_list->action_insert('/manage/data/'.$instance->entity_get()->group_managing_get_id().'/'.$instance->entity_get()->name.'/'.join('+', $instance->values_id_get()).'/delete?'.url::back_part_make(), 'delete');
    }
    return $actions_list;
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
        'children'   => $this->render_children($this->children_select(true))
      ]))->render();
    } else {
      return $this->render_self().
             $this->render_children($this->children_select(true));
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;
  static protected $is_init_nosql = false;
  static protected $is_init___sql = false;

  static function not_external_properties_get() {
    return [
      'id'    => 'id',
      'title' => 'title'
    ];
  }

  static function cache_cleaning() {
    static::$cache         = null;
    static::$is_init_nosql = false;
    static::$is_init___sql = false;
  }

  static function init() {
    if (!static::$is_init_nosql) {
         static::$is_init_nosql = true;
      foreach (storage::get('files')->select('selections') as $c_module_id => $c_selections) {
        foreach ($c_selections as $c_row_id => $c_selection) {
          if (isset(static::$cache[$c_selection->id])) console::log_insert_about_duplicate('selection', $c_selection->id, $c_module_id);
          static::$cache[$c_selection->id] = $c_selection;
          static::$cache[$c_selection->id]->module_id = $c_module_id;
          static::$cache[$c_selection->id]->origin = 'nosql';
        }
      }
    }
  }

  static function init_sql($id = null) {
    if ($id && isset(static::$cache[$id])) return;
    if ($id != null) {
      $instance = (new instance('selection', [
        'id' => $id
      ]))->select();
      if ($instance) {
        $selection = new static;
        foreach ($instance->values_get() as $c_key => $c_value)
          $selection->                     {$c_key} = $c_value;
        static::$cache[$selection->id] = $selection;
        static::$cache[$selection->id]->module_id = 'storage';
        static::$cache[$selection->id]->origin = 'sql';
      }
    }
    if (!static::$is_init___sql && $id == null) {
         static::$is_init___sql = true;
      foreach (entity::get('selection')->instances_select() as $c_instance) {
        $c_selection = new static;
        foreach ($c_instance->values_get() as $c_key => $c_value)
          $c_selection->                     {$c_key} = $c_value;
        static::$cache[$c_selection->id] = $c_selection;
        static::$cache[$c_selection->id]->module_id = 'storage';
        static::$cache[$c_selection->id]->origin = 'sql';
      }
    }
  }

  static function get($id, $load = true) {
    static::init();
    if (isset(static::$cache[$id]) == false) static::init_sql($id);
    if (isset(static::$cache[$id]) == false) return;
    if (static::$cache[$id] instanceof external_cache && $load)
        static::$cache[$id] =
        static::$cache[$id]->external_cache_load();
    return static::$cache[$id] ?? null;
  }

  static function get_all($origin = null, $load = true) {
    if ($origin == 'nosql') {static::init    ();                    }
    if ($origin ==   'sql') {static::init_sql();                    }
    if ($origin ==    null) {static::init    (); static::init_sql();}
    if ($load && ($origin == 'nosql' || $origin == null))
      foreach (static::$cache as &$c_item)
        if ($c_item instanceof external_cache)
            $c_item =
            $c_item->external_cache_load();
    $result = static::$cache ?? [];
    if ($origin)
      foreach ($result as $c_id => $c_item)
        if ($c_item->origin != $origin)
          unset($result[$c_id]);
    return $result;
  }

}}