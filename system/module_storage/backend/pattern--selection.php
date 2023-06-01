<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class selection extends markup implements has_external_cache {

    const DEFAULT_LIMIT = 50;

    public $tag_name = 'x-selection';
    public $attributes = ['data-selection' => true];
    public $template = 'container';
    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
    public $id;
    public $title_tag_name = 'h2';
    public $title_attributes = ['data-selection-title' => true];
    public $title;
    public $main_entity_name;
    public $has_error_on_build = false;
    public $origin = 'nosql'; # nosql | sql | dynamic
    public $_instances;
    public $fields = [];
    public $query_settings = [];
    public $decorator_settings = [];
    public $pager_is_enabled = false;
    public $pager_name = 'page';
    public $pager_id = 0;

    function __construct($title = null, $weight = 0) {
        if ($title) $this->title = $title;
        parent::__construct(null, [], [], $weight);
    }

    function build() {
        if (!$this->is_builded) {

            $this->children_delete();
            $this->attribute_insert('data-id',          $this->id,               'attributes', true);
            $this->attribute_insert('data-entity_name', $this->main_entity_name, 'attributes', true);
            event::start('on_selection_build_before', $this->id, ['selection' => &$this]);

            $this->_entities = [
                '_main' => entity::get($this->main_entity_name)
            ];

            # if no main entity
            if ($this->_entities['_main'] === null) {
                $this->has_error_on_build = true;
                $this->child_insert(
                    new markup('x-no-items', ['data-style' => 'table'], new text_multiline(
                        ['Entity "%%_name" is not available.', 'Selection ID = "%%_id".'], ['name' => $this->main_entity_name, 'id' => $this->id]
                    )), 'message_error'
                );
            }

            # prepare main entity fields
            if ($this->has_error_on_build === false) {
                $this->_entities[$this->main_entity_name] = &$this->_entities['_main'];
                if (!empty($this->fields['main']) && is_array($this->fields['main'])) {
                    foreach ($this->fields['main'] as $c_field) {
                        $this->query_settings['fields'][$c_field->entity_field_name.'_!f'] = '~'.$this->main_entity_name.'.'.$c_field->entity_field_name;
                    }
                } else { # if no fields
                    $this->has_error_on_build = true;
                    $this->child_insert(
                        new markup('x-no-items', ['data-style' => 'table'], new text_multiline(
                            ['No fields.', 'Selection ID = "%%_id".'], ['id' => $this->id]
                        )), 'message_error'
                    );
                }
            }

            # prepare join-fields
            if ($this->has_error_on_build === false) {
                if (!empty($this->fields['join']) && is_array($this->fields['join'])) {
                    foreach ($this->fields['join'] as $c_join) {
                        $this->_entities[$c_join->entity_name] = entity::get($c_join->entity_name);
                        if ($this->_entities[$c_join->entity_name]) {
                            $this->query_settings['join'][$c_join->entity_name] = [
                                  'type'    => strtoupper($c_join->type),
                                'target_!t' => '~'.$c_join->   entity_name,                                   'on'       => 'ON',
                                  'left_!f' => '~'.$c_join->   entity_name.'.'.$c_join->   entity_field_name, 'operator' => '=',
                                 'right_!f' => '~'.$c_join->on_entity_name.'.'.$c_join->on_entity_field_name ];
                            foreach ($c_join->fields as $c_field) {
                                $this->query_settings['join_fields'][$c_join->entity_name.'.'.$c_field->entity_field_name] = [
                                    $c_field->entity_field_name.'_!f' => '~'.$c_join->entity_name.'.'.$c_field->entity_field_name, 'alias_begin' => 'as',
                                                              'alias' => '"'.$c_join->entity_name.'.'.$c_field->entity_field_name.'"'
                                ];
                            }
                        }
                    }
                }
            }

            # prepare limit
            if ($this->has_error_on_build === false) {
                if (empty($this->query_settings['limit']))
                          $this->query_settings['limit'] = static::DEFAULT_LIMIT;
            }

            # prepare pager and offset
            if ($this->has_error_on_build === false) {
                if ($this->pager_is_enabled) {
                    $instances_count = $this->_entities['_main']->instances_select_count($this->query_settings);
                    $page_max_number = ceil($instances_count / $this->query_settings['limit']);
                    if ($page_max_number < 1)
                        $page_max_number = 1;
                    $pager = new pager(1, $page_max_number, $this->pager_name, $this->pager_id, [], -200);
                    $pager_error_code = $pager->error_code_get();
                    if ($pager_error_code === pager::ERR_CODE_CUR_GT_MAX) url::go($pager->url_page_max_get()->tiny_get());
                    if ($pager_error_code !== pager::ERR_CODE_OK) response::send_header_and_exit('page_not_found', null, new text_multiline(['wrong pager value', 'go to <a href="/">front page</a>'], [], BR.BR));
                    if ($page_max_number > 1) {
                        $this->query_settings['offset'] = ($pager->cur - 1) * $this->query_settings['limit'];
                        $this->child_insert(
                            $pager, 'pager'
                        );
                    }
                }
            }

            # prepare decorator
            if ($this->has_error_on_build === false) {
                $this->_instances = $this->_entities['_main']->instances_select($this->query_settings);
                if (count($this->_instances)) {
                    $decorator = new decorator;
                    $decorator->id = $this->id;
                    $decorator->_selection = $this;
                    $decorator->attribute_insert('data-entity_name', $this->main_entity_name);
                    foreach ($this->decorator_settings ?? [] as $c_key => $c_value)
                        $decorator->                           {$c_key} = $c_value;
                    # processing of each instance (row)
                    foreach ($this->_instances as $c_instance) {
                        $c_row = [];
                        $c_instance_id = implode('+', $c_instance->values_id_get());
                        # fields (main entity)
                        foreach ($this->fields['main'] as $c_cell_id => $c_field) {
                            $c_def_title               = $this->_entities['_main']->fields[$c_field->entity_field_name]->title               ?? null;
                            $c_def_converter_on_render = $this->_entities['_main']->fields[$c_field->entity_field_name]->converter_on_render ?? null;
                            $c_value_type              = $this->_entities['_main']->fields[$c_field->entity_field_name]->type;
                            $c_value                   = $c_instance->                    {$c_field->entity_field_name};
                            token::insert('selection_'.$this->main_entity_name.'_'.$c_field->entity_field_name.               '_cur_context', 'text', $c_value, null, 'storage');
                            token::insert('selection_'.$this->main_entity_name.'_'.$c_field->entity_field_name.'_'.$c_instance_id.'_context', 'text', $c_value, null, 'storage');
                            $c_is_not_formatted = isset($c_field->is_not_formatted) && $c_field->is_not_formatted === true;
                            if ($c_is_not_formatted === false && $c_value !== null && $c_value_type === 'real'    ) $c_value = locale::format_number  ($c_value, core::FPART_MAX_LEN);
                            if ($c_is_not_formatted === false && $c_value !== null && $c_value_type === 'integer' ) $c_value = locale::format_number  ($c_value);
                            if ($c_is_not_formatted === false && $c_value !== null && $c_value_type === 'date'    ) $c_value = locale::format_date    ($c_value);
                            if ($c_is_not_formatted === false && $c_value !== null && $c_value_type === 'datetime') $c_value = locale::format_datetime($c_value);
                            if ($c_is_not_formatted === false && $c_value !== null && $c_value_type === 'time'    ) $c_value = locale::format_time    ($c_value);
                            if ($c_is_not_formatted === false && $c_value !== null && $c_value_type === 'boolean' ) $c_value =   core::format_logic   ($c_value);
                            $c_row[$c_cell_id] = [
                                'attributes'           => ['data-entity-field-name' => $c_field->entity_field_name],
                                'value'                => $c_value,
                                'title'                => isset($c_field->title)               ? $c_field->title               : $c_def_title,
                                'converter_on_render'  => isset($c_field->converter_on_render) ? $c_field->converter_on_render : $c_def_converter_on_render,
                                'is_apply_translation' => isset($c_field->is_apply_translation) && $c_field->is_apply_translation,
                                'is_apply_tokens'      => isset($c_field->is_apply_tokens)      && $c_field->is_apply_tokens,
                                'is_trimmed'           => isset($c_field->is_trimmed)           && $c_field->is_trimmed,
                                'is_not_visible'       => isset($c_field->is_not_visible)       && $c_field->is_not_visible,
                                'weight'               => isset($c_field->weight) ? (int)$c_field->weight : 0
                            ];
                        }
                        # fields 'join'
                        if (!empty($this->fields['join']) && is_array($this->fields['join'])) {
                            foreach ($this->fields['join'] as $c_join_row_id => $c_join) {
                                if (isset($this->_entities[$c_join->entity_name])) {
                                    foreach ($c_join->fields as $c_cell_id => $c_field) {
                                        $c_def_title               = $this->_entities[$c_join->entity_name]->fields[$c_field->entity_field_name]->title               ?? null;
                                        $c_def_converter_on_render = $this->_entities[$c_join->entity_name]->fields[$c_field->entity_field_name]->converter_on_render ?? null;
                                        $c_value_type              = $this->_entities[$c_join->entity_name]->fields[$c_field->entity_field_name]->type;
                                        $c_value                   = $c_instance->   {$c_join->entity_name   .'.'.  $c_field->entity_field_name};
                                        token::insert('selection_'.$c_join->entity_name.'_'.$c_field->entity_field_name.               '_cur_context', 'text', $c_value, null, 'storage');
                                        token::insert('selection_'.$c_join->entity_name.'_'.$c_field->entity_field_name.'_'.$c_instance_id.'_context', 'text', $c_value, null, 'storage');
                                        $c_is_not_formatted = isset($c_field->is_not_formatted) && $c_field->is_not_formatted === true;
                                        if ($c_is_not_formatted === false && $c_value !== null && $c_value_type === 'real'    ) $c_value = locale::format_number  ($c_value, core::FPART_MAX_LEN);
                                        if ($c_is_not_formatted === false && $c_value !== null && $c_value_type === 'integer' ) $c_value = locale::format_number  ($c_value);
                                        if ($c_is_not_formatted === false && $c_value !== null && $c_value_type === 'date'    ) $c_value = locale::format_date    ($c_value);
                                        if ($c_is_not_formatted === false && $c_value !== null && $c_value_type === 'datetime') $c_value = locale::format_datetime($c_value);
                                        if ($c_is_not_formatted === false && $c_value !== null && $c_value_type === 'time'    ) $c_value = locale::format_time    ($c_value);
                                        if ($c_is_not_formatted === false && $c_value !== null && $c_value_type === 'boolean' ) $c_value =   core::format_logic   ($c_value);
                                        $c_row[$c_join_row_id.'_'.$c_cell_id] = [
                                            'attributes'           => ['data-entity-field-name' => $c_join->entity_name.'.'.$c_field->entity_field_name],
                                            'value'                => $c_value,
                                            'title'                => isset($c_field->title)               ? $c_field->title               : $c_def_title,
                                            'converter_on_render'  => isset($c_field->converter_on_render) ? $c_field->converter_on_render : $c_def_converter_on_render,
                                            'is_apply_translation' => isset($c_field->is_apply_translation) && $c_field->is_apply_translation,
                                            'is_apply_tokens'      => isset($c_field->is_apply_tokens)      && $c_field->is_apply_tokens,
                                            'is_trimmed'           => isset($c_field->is_trimmed)           && $c_field->is_trimmed,
                                            'is_not_visible'       => isset($c_field->is_not_visible)       && $c_field->is_not_visible,
                                            'weight'               => isset($c_field->weight) ? (int)$c_field->weight : 0
                                        ];
                                    }
                                }
                            }
                        }
                        # fields 'text'
                        if (!empty($this->fields['texts']) && is_array($this->fields['texts'])) {
                            foreach ($this->fields['texts'] as $c_cell_id => $c_text) {
                                $c_row[$c_cell_id] = [
                                    'value'                => $c_text->text,
                                    'title'                => isset($c_text->title) ? $c_text->title : null,
                                    'is_apply_translation' => isset($c_text->is_apply_translation) && $c_text->is_apply_translation,
                                    'is_apply_tokens'      => isset($c_text->is_apply_tokens)      && $c_text->is_apply_tokens,
                                    'is_trimmed'           => isset($c_text->is_trimmed)           && $c_text->is_trimmed,
                                    'is_not_visible'       => isset($c_text->is_not_visible)       && $c_text->is_not_visible,
                                    'weight'               => isset($c_text->weight) ? (int)$c_text->weight : 0
                                ];
                            }
                        }
                        # fields 'markup'
                        if (!empty($this->fields['markup']) && is_array($this->fields['markup'])) {
                            foreach ($this->fields['markup'] as $c_cell_id => $c_markup) {
                                $c_row[$c_cell_id] = [
                                    'value'                => $c_markup->markup->render(), # note: "render" is for markup containing tokens
                                    'title'                => isset($c_markup->title) ? $c_markup->title : null,
                                    'is_apply_translation' => isset($c_markup->is_apply_translation) && $c_markup->is_apply_translation,
                                    'is_apply_tokens'      => isset($c_markup->is_apply_tokens)      && $c_markup->is_apply_tokens,
                                    'is_trimmed'           => isset($c_markup->is_trimmed)           && $c_markup->is_trimmed,
                                    'is_not_visible'       => isset($c_markup->is_not_visible)       && $c_markup->is_not_visible,
                                    'weight'               => isset($c_markup->weight) ? (int)$c_markup->weight : 0
                                ];
                            }
                        }
                        # fields 'checkbox'
                        if (!empty($this->fields['checkboxes']) && is_array($this->fields['checkboxes'])) {
                            foreach ($this->fields['checkboxes'] as $c_cell_id => $c_checkbox) {
                                $c_form_field = new field_checkbox;
                                $c_form_field->build();
                                $c_form_field->name_set($c_checkbox->settings['name'] ?? 'is_checked[]');
                                $c_form_field->value_set($c_instance_id);
                                $c_row[$c_cell_id] = [
                                    'value'          => $c_form_field,
                                    'title'          => isset($c_checkbox->title) ? $c_checkbox->title : null,
                                    'is_not_visible' => isset($c_checkbox->is_not_visible) && $c_checkbox->is_not_visible,
                                    'weight'         => isset($c_checkbox->weight) ? (int)$c_checkbox->weight : 0
                                ];
                            }
                        }
                        # fields 'handler'
                        if (!empty($this->fields['handlers']) && is_array($this->fields['handlers'])) {
                            foreach ($this->fields['handlers'] as $c_cell_id => $c_handler) {
                                $c_row[$c_cell_id] = [
                                    'value'                => call_user_func($c_handler->handler, $c_cell_id, $c_row, $c_instance, $c_handler->settings ?? []),
                                    'title'                => isset($c_handler->title) ? $c_handler->title : null,
                                    'is_apply_translation' => isset($c_handler->is_apply_translation) && $c_handler->is_apply_translation,
                                    'is_apply_tokens'      => isset($c_handler->is_apply_tokens)      && $c_handler->is_apply_tokens,
                                    'is_trimmed'           => isset($c_handler->is_trimmed)           && $c_handler->is_trimmed,
                                    'is_not_visible'       => isset($c_handler->is_not_visible)       && $c_handler->is_not_visible,
                                    'weight'               => isset($c_handler->weight) ? (int)$c_handler->weight : 0
                                ];
                            }
                        }
                        # fields 'code'
                        if (!empty($this->fields['code']) && is_array($this->fields['code'])) {
                            foreach ($this->fields['code'] as $c_cell_id => $c_code) {
                                $c_row[$c_cell_id] = [
                                    'value'                => call_user_func($c_code->closure, $c_cell_id, $c_row, $c_instance, $c_code->settings ?? []),
                                    'title'                => isset($c_code->title) ? $c_code->title : null,
                                    'is_apply_translation' => isset($c_code->is_apply_translation) && $c_code->is_apply_translation,
                                    'is_apply_tokens'      => isset($c_code->is_apply_tokens)      && $c_code->is_apply_tokens,
                                    'is_trimmed'           => isset($c_code->is_trimmed)           && $c_code->is_trimmed,
                                    'is_not_visible'       => isset($c_code->is_not_visible)       && $c_code->is_not_visible,
                                    'weight'               => isset($c_code->weight) ? (int)$c_code->weight : 0
                                ];
                            }
                        }
                        # post-processing
                        foreach ($c_row as $c_cell_id => $c_cell) {
                            # apply markup filter
                            if (!empty($c_row[$c_cell_id]['converter_on_render'])) {
                                if (core::is_handler($c_row[$c_cell_id]['converter_on_render']) !== true &&      function_exists($c_row[$c_cell_id]['converter_on_render'])) $c_row[$c_cell_id]['value'] = call_user_func($c_row[$c_cell_id]['converter_on_render'], $c_row[$c_cell_id]['value']);
                                if (core::is_handler($c_row[$c_cell_id]['converter_on_render']) === true && core::handler_exists($c_row[$c_cell_id]['converter_on_render'])) $c_row[$c_cell_id]['value'] = call_user_func($c_row[$c_cell_id]['converter_on_render'], $c_row[$c_cell_id]['value']);
                            }
                            # convert scalar value to '\effcore\text'
                            if (is_numeric($c_row[$c_cell_id]['value']) ||
                                 is_string($c_row[$c_cell_id]['value'])) {
                                $c_row[$c_cell_id]['value'] = new text(
                                    (string)$c_row[$c_cell_id]['value'], false, false
                                );
                            }
                            # apply translation, tokens…
                            if ($c_row[$c_cell_id]['value'] instanceof text && is_string($c_row[$c_cell_id]['value']->text_select()) && !empty($c_row[$c_cell_id]['is_trimmed'])) $c_row[$c_cell_id]['value']->text_update(trim($c_row[$c_cell_id]['value']->text_select()));
                            if ($c_row[$c_cell_id]['value'] instanceof text) $c_row[$c_cell_id]['value']->is_apply_translation = !empty($c_row[$c_cell_id]['is_apply_translation']);
                            if ($c_row[$c_cell_id]['value'] instanceof text) $c_row[$c_cell_id]['value']->is_apply_tokens      = !empty($c_row[$c_cell_id]['is_apply_tokens']);
                            # removal of unnecessary parameters
                            unset($c_row[$c_cell_id]['converter_on_render']);
                            unset($c_row[$c_cell_id]['is_trimmed']);
                            unset($c_row[$c_cell_id]['is_apply_translation']);
                            unset($c_row[$c_cell_id]['is_apply_tokens']);
                        }
                        # delete invisible items
                        foreach ($c_row as $c_cell_id => $c_cell) {
                            if ($c_cell['is_not_visible'] === true)
                                unset($c_row[$c_cell_id]);
                                unset($c_row[$c_cell_id]['is_not_visible']);
                        }
                        # append $c_row to decorator
                        if (count($c_row)) {
                            core::array_sort_by_number($c_row);
                            $decorator->data[] = $c_row; # null | markup | text
                        } else {
                            $decorator->data[] = [[
                                'value'  => new text('No fields.'),
                                'title'  => null,
                                'weight' => 0
                            ]];
                        }
                    }
                    $this->child_insert($decorator, 'result');
                    $decorator->build();
                } else {
                    $this->child_insert(
                        new markup('x-no-items', ['data-style' => 'table'], 'No items.'), 'message_no_items'
                    );
                }
            }

            event::start('on_selection_build_after', $this->id, ['selection' => &$this]);
            $this->is_builded = true;
            return $this;
        }
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
                $selection->origin = 'sql';
                foreach ($instance->values_get() as $c_key => $c_value)
                    if ($c_key === 'attributes') $selection->{$c_key} = (widget_attributes::value_to_attributes($c_value) ?? []) + $selection->{$c_key};
                    else                         $selection->{$c_key} =                                         $c_value;
                foreach ($selection->data ?? [] as $c_key => $c_value)
                         $selection->             {$c_key} = $c_value;
                static::$cache[$selection->id] = $selection;
            }
        }
        if ($id === null && !static::$is_init___sql) {
            static::$is_init___sql = true;
            foreach (entity::get('selection')->instances_select() as $c_instance) {
                $c_selection = new static;
                $c_selection->origin = 'sql';
                foreach ($c_instance->values_get() as $c_key => $c_value)
                    if ($c_key === 'attributes') $c_selection->{$c_key} = (widget_attributes::value_to_attributes($c_value) ?? []) + $c_selection->{$c_key};
                    else                         $c_selection->{$c_key} =                                         $c_value;
                foreach ($c_selection->data ?? [] as $c_key => $c_value)
                         $c_selection->             {$c_key} = $c_value;
                static::$cache[$c_selection->id] = $c_selection;
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

}
