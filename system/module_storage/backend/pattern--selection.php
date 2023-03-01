<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class selection extends markup implements has_external_cache {

  const default_limit = 50;

  public $tag_name = 'x-selection';
  public $attributes = ['data-selection' => true];
  public $template = 'container';
# ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
  public $title_tag_name = 'h2';
  public $title_attributes = ['data-selection-title' => true];
  public $id;
  public $title;
  public $fields = [];
  public $query_settings = [];
  public $decorator_settings = [];
  public $pager_is_enabled = false;
  public $pager_name = 'page';
  public $pager_id = 0;
  public $origin = 'nosql'; # nosql | sql
  public $_instances;

  function __construct($title = null, $weight = 0) {
    if ($title) $this->title = $title;
    parent::__construct(null, [], [], $weight);
  }

  function build() {
    if (!$this->is_builded) {

      $this->children_delete();
      $this->attribute_insert('data-id',        $this->id, 'attributes', true);
      event::start('on_selection_build_before', $this->id, ['selection' => &$this]);

      $used_entities = [];
      $used_storages = [];
      $this->_main_entity = null;

    # sort + analyze fields
      if (isset($this->fields) && is_array($this->fields)) {
        foreach ($this->fields as $c_field)
          if (!property_exists($c_field, 'weight'))
            $c_field->weight = 0;
        core::array_sort_by_weight($this->fields, 3);
        foreach ($this->fields as $c_row_id => $c_field) {
          if ($c_field->type === 'field' ||
              $c_field->type === 'join_field') {
            $c_entity = entity::get($c_field->entity_name, false);
            $used_entities[$c_entity->name        ] = $c_entity->name;
            $used_storages[$c_entity->storage_name] = $c_entity->storage_name;
            if ($this->_main_entity === null && $c_field->type === 'field') {
                $this->_main_entity = entity::get($c_field->entity_name);
            }
          }
        }
      } else {
        message::insert(new text(
          'No fields for select from storage! Selection ID = "%%_id".', ['id' => $this->id]), 'error'
        );
        return new node;
      }

    # ─────────────────────────────────────────────────────────────────────
    # prepare the query and request data from the storage
    # ─────────────────────────────────────────────────────────────────────
      if (count($used_storages) === 0) {message::insert(new text('No fields for select from storage! Selection ID = "%%_id".',     ['id' => $this->id]), 'warning'); return new node;}
      if (count($used_storages)  >  1) {message::insert(new text('Distributed queries are not supported! Selection ID = "%%_id".', ['id' => $this->id]), 'warning'); return new node;}
      if (count($used_storages) === 1) {
        $this->attribute_insert('data-entity', $this->_main_entity->name, 'attributes', true);

      # prepare join
        foreach ($this->fields as $c_row_id => $c_field) {
          if ($c_field->type === 'join_field') {
            $this->query_settings['join_fields'][$c_row_id.'_!f'] = '~'.$c_field->entity_name.'.'.$c_field->entity_field_name;
          }
        }
        if (!isset($this->query_settings['join']) &&
             isset($this->query_settings['join_script'])) {
          foreach ($this->query_settings['join_script'] as $c_id => $c_join) {
            $this->query_settings['join'][$c_id] = [
                'type'    =>     $c_join->type,
              'target_!t' => '~'.$c_join->   entity_name,                                   'on'       => 'ON',
                'left_!f' => '~'.$c_join->   entity_name.'.'.$c_join->   entity_field_name, 'operator' => '=',
               'right_!f' => '~'.$c_join->on_entity_name.'.'.$c_join->on_entity_field_name
            ];
          }
        }

      # prepare limit
        if (empty($this->query_settings['limit']))
                  $this->query_settings['limit'] = static::default_limit;

      # prepare pager
        if ($this->pager_is_enabled) {
          $instances_count = $this->_main_entity->instances_select_count($this->query_settings);
          $page_max_number = ceil($instances_count / $this->query_settings['limit']);
          if ($page_max_number < 1)
              $page_max_number = 1;
          $pager = new pager(1, $page_max_number, $this->pager_name, $this->pager_id, [], -200);
          $pager_error_code = $pager->error_code_get();
          if ($pager_error_code === pager::ERR_CODE_CUR_GT_MAX) url::go($pager->url_page_max_get()->tiny_get());
          if ($pager_error_code !== pager::ERR_CODE_OK) response::send_header_and_exit('page_not_found', null, new text_multiline(['wrong pager value', 'go to <a href="/">front page</a>'], [], br.br));
          if ($page_max_number > 1) {
            $this->query_settings['offset'] = ($pager->cur - 1) * $this->query_settings['limit'];
            $this->child_insert(
              $pager, 'pager'
            );
          }
        }

      # select instances
        $this->_instances = $this->_main_entity->instances_select(
          $this->query_settings
        );

      }

    # ─────────────────────────────────────────────────────────────────────
    # wrap the result in the decorator
    # ─────────────────────────────────────────────────────────────────────
      $result = null;
      if (count($this->_instances)) {

        $decorator = new decorator;
        $decorator->id = $this->id;
        $decorator->_selection = $this;
        $decorator->attribute_insert('data-entity', $this->_main_entity->name);
        foreach ($this->decorator_settings ?? [] as $c_key => $c_value)
          $decorator->                             {$c_key} = $c_value;

        foreach ($this->_instances as $c_instance) {
          $c_instance_id = implode('+', $c_instance->values_id_get());

        # ─────────────────────────────────────────────────────────────────────
        # make context token for each value
        # ─────────────────────────────────────────────────────────────────────
          foreach ($this->fields as $c_row_id => $c_field) {
            if ($c_field->type === 'field' ||
                $c_field->type === 'join_field') {
              token::insert('selection_'.$c_field->entity_name.'_'.$c_field->entity_field_name.               '_cur_context', 'text', $c_instance->{$c_field->entity_field_name}, null, 'storage');
              token::insert('selection_'.$c_field->entity_name.'_'.$c_field->entity_field_name.'_'.$c_instance_id.'_context', 'text', $c_instance->{$c_field->entity_field_name}, null, 'storage');
            }
          }

          $c_row = [];
          foreach ($this->fields as $c_row_id => $c_field) {

          # ─────────────────────────────────────────────────────────────────────
          # prepare each value for $c_row[$c_row_id]
          # ─────────────────────────────────────────────────────────────────────
            switch ($c_field->type) {
              case 'field':
              case 'join_field':
                $c_entity     = entity::get($c_field->entity_name, false);
                $c_title      = $c_field->title ?? $c_entity->fields[$c_field->entity_field_name]->title;
                $c_value_type =                    $c_entity->fields[$c_field->entity_field_name]->type;
                $c_value      =                    $c_instance->    {$c_field->entity_field_name};
                if ($c_value !== null && $c_value_type === 'real'    ) $c_value = locale::format_number  ($c_value, core::fpart_max_len);
                if ($c_value !== null && $c_value_type === 'integer' ) $c_value = locale::format_number  ($c_value);
                if ($c_value !== null && $c_value_type === 'date'    ) $c_value = locale::format_date    ($c_value);
                if ($c_value !== null && $c_value_type === 'datetime') $c_value = locale::format_datetime($c_value);
                if ($c_value !== null && $c_value_type === 'time'    ) $c_value = locale::format_time    ($c_value);
                if ($c_value !== null && $c_value_type === 'boolean' ) $c_value =   core::format_logic   ($c_value);
                $c_row[$c_row_id] = [
                  'attributes' => ['data-entity-field-name' => $c_field->entity_field_name],
                  'title' => $c_title,
                  'value' => $c_value
                ];
                break;
              case 'text':
                $c_row[$c_row_id] = [
                  'title' => $c_field->title ?? null,
                  'value' => $c_field->text
                ];
                break;
              case 'markup':
                $c_row[$c_row_id] = [
                  'title' => $c_field->title ?? null,
                  'value' => $c_field->markup->render() # note: "render" is for markup containing tokens
                ];
                break;
              case 'code':
                $c_row[$c_row_id] = [
                  'title' =>  $c_field->title ?? null,
                  'value' => call_user_func($c_field->closure, $c_row_id, $c_row, $c_instance, $c_field->settings ?? [])
                ];
                break;
              case 'handler':
                $c_row[$c_row_id] = [
                  'title' => $c_field->title ?? null,
                  'value' => call_user_func($c_field->handler, $c_row_id, $c_row, $c_instance, $c_field->settings ?? [])
                ];
                break;
              case 'checkbox':
                $c_form_field = new field_checkbox;
                $c_form_field->build();
                $c_form_field->name_set($c_field->settings['name'] ?? 'is_checked[]');
                $c_form_field->value_set(implode('+', $c_instance->values_id_get()));
                $c_row[$c_row_id] = [
                  'title' => $c_field->title ?? null,
                  'value' => $c_form_field
                ];
                break;
            }

          # ─────────────────────────────────────────────────────────────────────
          # prepare each value for $c_row[$c_row_id]: apply the filters
          # ─────────────────────────────────────────────────────────────────────
            $c_filters = !empty($c_field->settings['filters']) &&
                       is_array($c_field->settings['filters']) ?
                                $c_field->settings['filters'] : [];
            if (count($c_filters)) {
              krsort($c_filters, SORT_NUMERIC);
              foreach ($c_filters as $c_filter) {
                if ($c_filter === 'translate') {if (is_string($c_row[$c_row_id]['value'])) $c_row[$c_row_id]['value'] = new text($c_row[$c_row_id]['value'], false, false); if ($c_row[$c_row_id]['value'] instanceof text) $c_row[$c_row_id]['value']->is_apply_translation = true; continue;}
                if ($c_filter === 'tokenized') {if (is_string($c_row[$c_row_id]['value'])) $c_row[$c_row_id]['value'] = new text($c_row[$c_row_id]['value'], false, false); if ($c_row[$c_row_id]['value'] instanceof text) $c_row[$c_row_id]['value']->is_apply_tokens      = true; continue;}
                if (core::is_handler($c_filter) !== true &&      function_exists($c_filter)) {$c_row[$c_row_id]['value'] = call_user_func($c_filter, $c_row[$c_row_id]['value']); continue;}
                if (core::is_handler($c_filter) === true && core::handler_exists($c_filter)) {$c_row[$c_row_id]['value'] = call_user_func($c_filter, $c_row[$c_row_id]['value']); continue;}
              }
            }
            if (is_string($c_row[$c_row_id]['value']) ||
                   is_int($c_row[$c_row_id]['value'])) {
              $c_row[$c_row_id]['value'] = new text((string)$c_row[$c_row_id]['value'], false, false);
            }

          # ─────────────────────────────────────────────────────────────────────
          # delete invisible items
          # ─────────────────────────────────────────────────────────────────────
            if (isset($c_field->is_visible) && $c_field->is_visible === false) {
              unset($c_row[$c_row_id]);
            }

          }

        # append $c_row to decorator
          $decorator->data[] = $c_row; # null | markup | text
        }

        $this->child_insert($decorator, 'result');
        $decorator->build();

      } else {
        $this->child_insert(
          new markup('x-no-items', ['data-style' => 'table'], 'no items'), 'no_items'
        );
      }

      event::start('on_selection_build_after', $this->id, ['selection' => &$this]);
      $this->is_builded = true;
      return $this;

    }
  }

  # ─────────────────────────────────────────────────────────────────────
  # field insertion functionality
  # ─────────────────────────────────────────────────────────────────────

  function field_insert_entity($row_id = null, $title = null, $entity_name = '', $entity_field_name = '', $settings = [], $weight = 0) {
    $row_id = $row_id ?: $entity_name.'.'.$entity_field_name;
    $this->fields[$row_id] = new \stdClass;
    $this->fields[$row_id]->type              = 'field';
    $this->fields[$row_id]->title             = $title;
    $this->fields[$row_id]->entity_name       = $entity_name;
    $this->fields[$row_id]->entity_field_name = $entity_field_name;
    $this->fields[$row_id]->settings          = $settings;
    $this->fields[$row_id]->weight            = $weight;
  }

  function field_insert_entity_join($row_id = null, $title = null, $entity_name = '', $entity_field_name = '', $settings = [], $weight = 0) {
    $row_id = $row_id ?: $entity_name.'.'.$entity_field_name;
    $this->fields[$row_id] = new \stdClass;
    $this->fields[$row_id]->type              = 'join_field';
    $this->fields[$row_id]->title             = $title;
    $this->fields[$row_id]->entity_name       = $entity_name;
    $this->fields[$row_id]->entity_field_name = $entity_field_name;
    $this->fields[$row_id]->settings          = $settings;
    $this->fields[$row_id]->weight            = $weight;
  }

  function field_insert_text($row_id = null, $title = null, $text = '', $settings = [], $weight = 0) {
    $row_id = $row_id ?: 'text';
    $this->fields[$row_id] = new \stdClass;
    $this->fields[$row_id]->type     = 'text';
    $this->fields[$row_id]->title    = $title;
    $this->fields[$row_id]->text     = $text;
    $this->fields[$row_id]->settings = $settings;
    $this->fields[$row_id]->weight   = $weight;
  }

  function field_insert_markup($row_id = null, $title = null, $markup = null, $settings = [], $weight = 0) {
    $row_id = $row_id ?: 'markup';
    $this->fields[$row_id] = new \stdClass;
    $this->fields[$row_id]->type     = 'markup';
    $this->fields[$row_id]->title    = $title;
    $this->fields[$row_id]->markup   = $markup;
    $this->fields[$row_id]->settings = $settings;
    $this->fields[$row_id]->weight   = $weight;
  }

  function field_insert_code($row_id = null, $title = null, $closure = null, $settings = [], $weight = 0) {
    $row_id = $row_id ?: 'code';
    $this->fields[$row_id] = new \stdClass;
    $this->fields[$row_id]->type     = 'code';
    $this->fields[$row_id]->title    = $title;
    $this->fields[$row_id]->closure  = $closure;
    $this->fields[$row_id]->settings = $settings;
    $this->fields[$row_id]->weight   = $weight;
  }

  function field_insert_handler($row_id = null, $title = null, $handler = '', $settings = [], $weight = 0) {
    $row_id = $row_id ?: 'handler';
    $this->fields[$row_id] = new \stdClass;
    $this->fields[$row_id]->type     = 'handler';
    $this->fields[$row_id]->title    = $title;
    $this->fields[$row_id]->handler  = $handler;
    $this->fields[$row_id]->settings = $settings;
    $this->fields[$row_id]->weight   = $weight;
  }

  function field_insert_checkbox($row_id = null, $title = null, $settings = [], $weight = 0) {
    $row_id = $row_id ?: 'checkbox';
    $this->fields[$row_id] = new \stdClass;
    $this->fields[$row_id]->type     = 'checkbox';
    $this->fields[$row_id]->title    = $title;
    $this->fields[$row_id]->settings = $settings;
    $this->fields[$row_id]->weight   = $weight;
  }

  # ─────────────────────────────────────────────────────────────────────
  # render
  # ─────────────────────────────────────────────────────────────────────

  function render_self() {
    return $this->title ? (
      new markup($this->title_tag_name, $this->title_attributes, $this->title
    ))->render() : '';
  }

  function render() {
    $this->build();
    if ($this->template) {
      return (template::make_new(template::pick_name($this->template), [
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
      foreach (storage::get('data')->select_array('selections') as $c_module_id => $c_selections) {
        foreach ($c_selections as $c_row_id => $c_selection) {
          if (isset(static::$cache[$c_selection->id])) console::report_about_duplicate('selections', $c_selection->id, $c_module_id, static::$cache[$c_selection->id]);
                    static::$cache[$c_selection->id] = $c_selection;
                    static::$cache[$c_selection->id]->module_id = $c_module_id;
                    static::$cache[$c_selection->id]->origin = 'nosql';
        }
      }
    }
  }

  static function init_sql($id = null) {
    if ($id && isset(static::$cache[$id])) return;
    if ($id !== null) {
      $instance = (new instance('selection', [
        'id' => $id
      ]))->select();
      if ($instance) {
        $selection = new static;
        foreach ($instance->values_get() as $c_key => $c_value)
          if ($c_key === 'attributes') $selection->{$c_key} = widget_attributes::complex_value_to_attributes($c_value) ?? [];
          else                         $selection->{$c_key} =                                                $c_value;
        static::$cache[$selection->id] = $selection;
        static::$cache[$selection->id]->origin = 'sql';
      }
    }
    if ($id === null && !static::$is_init___sql) {
      static::$is_init___sql = true;
      foreach (entity::get('selection')->instances_select() as $c_instance) {
        $c_selection = new static;
        foreach ($c_instance->values_get() as $c_key => $c_value)
          if ($c_key === 'attributes') $c_selection->{$c_key} = widget_attributes::complex_value_to_attributes($c_value) ?? [];
          else                         $c_selection->{$c_key} =                                                $c_value;
        static::$cache[$c_selection->id] = $c_selection;
        static::$cache[$c_selection->id]->origin = 'sql';
      }
    }
  }

  static function get($id, $load = true) {
    static::init();
    if (isset(static::$cache[$id]) === false) static::init_sql($id);
    if (isset(static::$cache[$id]) === false) return;
    if (static::$cache[$id] instanceof external_cache && $load)
        static::$cache[$id] =
        static::$cache[$id]->load_from_nosql_storage();
    return static::$cache[$id] ?? null;
  }

  static function get_all($origin = null, $load = true) {
    if ($origin === 'nosql') {static::init();                    }
    if ($origin === 'sql'  ) {                static::init_sql();}
    if ($origin ===  null  ) {static::init(); static::init_sql();}
    if ($load && ($origin === 'nosql' || $origin === null))
      foreach (static::$cache as $c_id => $c_item)
           if (static::$cache[$c_id] instanceof external_cache)
               static::$cache[$c_id] =
               static::$cache[$c_id]->load_from_nosql_storage();
    $result = static::$cache ?? [];
    if ($origin)
      foreach ($result as $c_id => $c_item)
        if ($c_item->origin !== $origin)
          unset($result[$c_id]);
    return $result;
  }

}}