<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Selection extends Markup implements has_Data_cache {

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

    function __construct($title = null, $weight = +0) {
        if ($title) $this->title = $title;
        parent::__construct(null, [], [], $weight);
    }

    function build() {
        if (!$this->is_builded) {

            $this->children_delete();
            $this->attribute_insert('data-id'         , $this->id              , 'attributes', true);
            $this->attribute_insert('data-entity_name', $this->main_entity_name, 'attributes', true);
            Event::start('on_selection_build_before'  , $this->id, ['selection' => &$this]);

            $this->_entities = [
                '_main' => Entity::get($this->main_entity_name)
            ];

            # if no main entity
            if ($this->_entities['_main'] === null) {
                $this->has_error_on_build = true;
                $this->child_insert(
                    new Markup('x-no-items', ['data-style' => 'table'], new Text_multiline(
                        ['Entity "%%_name" is not available.', 'Selection ID = "%%_id".'], ['name' => $this->main_entity_name, 'id' => $this->id]
                    )), 'message_error'
                );
            }

            # if no fields
            if ($this->has_error_on_build === false) {
                if (!$this->fields) {
                    $this->has_error_on_build = true;
                    $this->child_insert(
                        new Markup('x-no-items', ['data-style' => 'table'], new Text_multiline(
                            ['No fields.', 'Selection ID = "%%_id".'], ['id' => $this->id]
                        )), 'message_error'
                    );
                }
            }

            # prepare main entity fields
            if ($this->has_error_on_build === false) {
                $this->_entities[$this->main_entity_name] = &$this->_entities['_main'];
                if (!empty($this->fields['main']) && is_array($this->fields['main'])) {
                    foreach ($this->fields['main'] as $c_field) {
                        $this->query_settings['fields'][$c_field->entity_field_name.'_!f'] =
                        '~'.$this->main_entity_name.'.'.$c_field->entity_field_name;
                    }
                }
            }

            # prepare join-fields
            if ($this->has_error_on_build === false) {
                if (!empty($this->fields['join']) && is_array($this->fields['join'])) {
                    foreach ($this->fields['join'] as $c_join) {
                        $this->_entities[$c_join->entity_name] = Entity::get($c_join->entity_name);
                        if ($this->_entities[$c_join->entity_name]) {
                            $this->query_settings['join'][$c_join->entity_name] = [
                                  'type'    => strtoupper($c_join->type),
                                'target_!t' => '~'.$c_join->   entity_name                                  , 'on'       => 'ON',
                                  'left_!f' => '~'.$c_join->   entity_name.'.'.$c_join->   entity_field_name, 'operator' => '=',
                                 'right_!f' => '~'.$c_join->on_entity_name.'.'.$c_join->on_entity_field_name ];
                            foreach ($c_join->fields as $c_field) {
                                $this->query_settings['join_fields'][$c_join->entity_name.'.'.$c_field->entity_field_name] = [
                                    $c_field->entity_field_name.'_!f' => '~'.$c_join->entity_name.'.'.$c_field->entity_field_name, 'alias_begin' => 'as',
                                                              'alias' => '`'.$c_join->entity_name.'.'.$c_field->entity_field_name.'`'
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
                    $pager = new Pager(1, $page_max_number, $this->pager_name, $this->pager_id, [], -200);
                    $pager_error_code = $pager->error_code_get();
                    if ($pager_error_code === Pager::ERR_CODE_CUR_GT_MAX) Url::go($pager->url_page_max_get()->relative_get());
                    if ($pager_error_code !== Pager::ERR_CODE_OK) Response::send_header_and_exit('page_not_found', null, new Text_multiline(['wrong pager value', 'go to <a href="/">front page</a>'], [], BR.BR));
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
                    $decorator = new Decorator;
                    $decorator->id = $this->id;
                    $decorator->_selection = $this;
                    $decorator->attribute_insert('data-entity_name', $this->main_entity_name);
                    foreach ($this->decorator_settings ?? [] as $c_key => $c_value)
                        $decorator->                           {$c_key} = $c_value;
                    # processing of each instance (row)
                    foreach ($this->_instances as $c_instance) {
                        $c_row = [];
                        $c_instance_id = implode('+', $c_instance->values_id_get());

                        ############################
                        ### fields (main entity) ###
                        ############################

                        if (!empty($this->fields['main']) && is_array($this->fields['main'])) {
                            foreach ($this->fields['main'] as $c_cell_id => $c_field) {
                                $c_def_title                = $this->_entities['_main']->fields[$c_field->entity_field_name]->title                 ?? null;
                                $c_def_converters_on_render = $this->_entities['_main']->fields[$c_field->entity_field_name]->converters->on_render ?? null;
                                $c_value_type               = $this->_entities['_main']->fields[$c_field->entity_field_name]->type;
                                $c_value                    = $c_instance->                    {$c_field->entity_field_name};
                                Token::insert('selection__'.$this->main_entity_name.'__'.$c_field->entity_field_name.           '__current__context', 'text', $c_value, null, 'storage');
                                Token::insert('selection__'.$this->main_entity_name.'__'.$c_field->entity_field_name.'__'.$c_instance_id.'__context', 'text', $c_value, null, 'storage');
                                $c_row[$c_cell_id] = [
                                    'attributes'           => ['data-entity-field' => $c_field->entity_field_name],
                                    'title'                => isset($c_field->title) ? $c_field->title : $c_def_title,
                                    'value'                => $c_value,
                                    'type'                 => $c_value_type,
                                    'converters_on_render' => $c_field->converters->on_render ?? $c_def_converters_on_render,
                                    'format'               => $c_field->format ?? null,
                                    'is_apply_translation' => $c_field->is_apply_translation ?? false,
                                    'is_apply_tokens'      => $c_field->is_apply_tokens ?? false,
                                    'is_not_visible'       => $c_field->is_not_visible ?? false,
                                    'weight'               => $c_field->weight ?? 0
                                ];
                            }
                        }

                        #####################
                        ### fields 'join' ###
                        #####################

                        if (!empty($this->fields['join']) && is_array($this->fields['join'])) {
                            foreach ($this->fields['join'] as $c_join_row_id => $c_join) {
                                if (isset($this->_entities[$c_join->entity_name])) {
                                    foreach ($c_join->fields as $c_cell_id => $c_field) {
                                        $c_def_title                = $this->_entities[$c_join->entity_name]->fields[$c_field->entity_field_name]->title                 ?? null;
                                        $c_def_converters_on_render = $this->_entities[$c_join->entity_name]->fields[$c_field->entity_field_name]->converters->on_render ?? null;
                                        $c_value_type               = $this->_entities[$c_join->entity_name]->fields[$c_field->entity_field_name]->type;
                                        $c_value                    = $c_instance->   {$c_join->entity_name   .'.'.  $c_field->entity_field_name};
                                        Token::insert('selection__'.$c_join->entity_name.'__'.$c_field->entity_field_name.           '__current__context', 'text', $c_value, null, 'storage');
                                        Token::insert('selection__'.$c_join->entity_name.'__'.$c_field->entity_field_name.'__'.$c_instance_id.'__context', 'text', $c_value, null, 'storage');
                                        $c_row[$c_join_row_id.'_'.$c_cell_id] = [
                                            'attributes'           => ['data-entity-field' => $c_join->entity_name.'.'.$c_field->entity_field_name],
                                            'title'                => isset($c_field->title) ? $c_field->title : $c_def_title,
                                            'value'                => $c_value,
                                            'type'                 => $c_value_type,
                                            'converters_on_render' => $c_field->converters->on_render ?? $c_def_converters_on_render,
                                            'format'               => $c_field->format ?? null,
                                            'is_apply_translation' => $c_field->is_apply_translation ?? false,
                                            'is_apply_tokens'      => $c_field->is_apply_tokens ?? false,
                                            'is_not_visible'       => $c_field->is_not_visible ?? false,
                                            'weight'               => $c_field->weight ?? 0
                                        ];
                                    }
                                }
                            }
                        }

                        #####################
                        ### fields 'text' ###
                        #####################

                        if (!empty($this->fields['texts']) && is_array($this->fields['texts'])) {
                            foreach ($this->fields['texts'] as $c_cell_id => $c_text) {
                                $c_row[$c_cell_id] = [
                                    'type'                 => 'custom:text',
                                    'title'                => isset($c_text->title) ? $c_text->title : null,
                                    'value'                => $c_text->text,
                                    'format'               => $c_text->format ?? null,
                                    'is_apply_translation' => $c_text->is_apply_translation ?? false,
                                    'is_apply_tokens'      => $c_text->is_apply_tokens ?? false,
                                    'is_not_visible'       => $c_text->is_not_visible ?? false,
                                    'weight'               => $c_text->weight ?? 0
                                ];
                            }
                        }

                        #######################
                        ### fields 'markup' ###
                        #######################

                        if (!empty($this->fields['markup']) && is_array($this->fields['markup'])) {
                            foreach ($this->fields['markup'] as $c_cell_id => $c_markup) {
                                $c_row[$c_cell_id] = [
                                    'type'                 => 'custom:markup',
                                    'title'                => isset($c_markup->title) ? $c_markup->title : null,
                                    'value'                => $c_markup->markup->render(), # note: "render" is for markup containing tokens
                                    'format'               => $c_markup->format ?? null,
                                    'is_apply_translation' => $c_markup->is_apply_translation ?? false,
                                    'is_apply_tokens'      => $c_markup->is_apply_tokens ?? false,
                                    'is_not_visible'       => $c_markup->is_not_visible ?? false,
                                    'weight'               => $c_markup->weight ?? 0
                                ];
                            }
                        }

                        ########################
                        ### fields 'handler' ###
                        ########################

                        if (!empty($this->fields['handlers']) && is_array($this->fields['handlers'])) {
                            foreach ($this->fields['handlers'] as $c_cell_id => $c_handler) {
                                $c_row[$c_cell_id] = [
                                    'type'                 => 'custom:handler',
                                    'attributes'           => ['data-handler' => Core::handler_get_method($c_handler->handler)],
                                    'title'                => isset($c_handler->title) ? $c_handler->title : null,
                                    'value'                => Core::is_handler($c_handler->handler) && Core::handler_exists($c_handler->handler) ? call_user_func($c_handler->handler, $c_cell_id, $c_row, $c_instance, $c_handler) : 'LOST HANDLER',
                                    'format'               => $c_handler->format ?? null,
                                    'is_apply_translation' => $c_handler->is_apply_translation ?? false,
                                    'is_apply_tokens'      => $c_handler->is_apply_tokens ?? false,
                                    'is_not_visible'       => $c_handler->is_not_visible ?? false,
                                    'weight'               => $c_handler->weight ?? 0
                                ];
                            }
                        }

                        #####################
                        ### fields 'code' ###
                        #####################

                        if (!empty($this->fields['code']) && is_array($this->fields['code'])) {
                            foreach ($this->fields['code'] as $c_cell_id => $c_code) {
                                $c_row[$c_cell_id] = [
                                    'type'                 => 'custom:code',
                                    'title'                => isset($c_code->title) ? $c_code->title : null,
                                    'value'                => call_user_func($c_code->closure, $c_cell_id, $c_row, $c_instance, $c_code),
                                    'format'               => $c_code->format ?? null,
                                    'is_apply_translation' => $c_code->is_apply_translation ?? false,
                                    'is_apply_tokens'      => $c_code->is_apply_tokens ?? false,
                                    'is_not_visible'       => $c_code->is_not_visible ?? false,
                                    'weight'               => $c_code->weight ?? 0
                                ];
                            }
                        }

                        ##################################
                        ### append $c_row to decorator ###
                        ##################################

                        if (count($c_row)) {
                            Core::array_sort_by_number($c_row);
                            $decorator->data[] = $c_row; # null | markup | text
                        } else {
                            $decorator->data[] = [[
                                'value'  => new Text('No fields.'),
                                'title'  => null,
                                'weight' => 0
                            ]];
                        }
                    }
                    $this->child_insert($decorator, 'result');
                    $decorator->build();
                } else {
                    $this->child_insert(
                        new Markup('x-no-items', ['data-style' => 'table'], 'No items.'), 'message_no_items'
                    );
                }
            }

            Event::start('on_selection_build_after', $this->id, ['selection' => &$this]);
            $this->is_builded = true;
            return $this;
        }
    }

    # ─────────────────────────────────────────────────────────────────────
    # render
    # ─────────────────────────────────────────────────────────────────────

    function render_self() {
        return $this->title ? (
            new Markup($this->title_tag_name, $this->title_attributes, $this->title
        ))->render() : '';
    }

    function render() {
        $this->build();
        if ($this->template) {
            return (Template::make_new(Template::pick_name($this->template), [
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

    protected static $cache;
    protected static $cache_handlers;
    protected static $is_init_nosql = false;
    protected static $is_init___sql = false;

    static function not_external_properties_get() {
        return [
            'id'    => 'id',
            'title' => 'title'
        ];
    }

    static function cache_cleaning() {
        static::$cache          = null;
        static::$cache_handlers = null;
        static::$is_init_nosql  = false;
        static::$is_init___sql  = false;
    }

    static function init() {
        if (!static::$is_init_nosql) {
             static::$is_init_nosql = true;
            foreach (Storage::get('data')->select_array('selections') as $c_module_id => $c_selections) {
                foreach ($c_selections as $c_row_id => $c_selection) {
                    if (isset(static::$cache[$c_selection->id])) Console::report_about_duplicate('selections', $c_selection->id, $c_module_id, static::$cache[$c_selection->id]);
                              static::$cache[$c_selection->id] = $c_selection;
                              static::$cache[$c_selection->id]->module_id = $c_module_id;
                              static::$cache[$c_selection->id]->origin = 'nosql';
                }
            }
            foreach (Storage::get('data')->select_array('selection_handlers') as $c_module_id => $c_handlers) {
                foreach ($c_handlers as $c_row_id => $c_handler) {
                    if (isset(static::$cache_handlers[$c_row_id])) Console::report_about_duplicate('selection_handlers', $c_row_id, $c_module_id, static::$cache_handlers[$c_row_id]);
                              static::$cache_handlers[$c_row_id] = $c_handler;
                              static::$cache_handlers[$c_row_id]->module_id = $c_module_id;
                }
            }
        }
    }

    static function init_sql($id = null) {
        if ($id && isset(static::$cache[$id])) return;
        if ($id !== null) {
            $instance = (new Instance('selection', [
                'id' => $id
            ]))->select();
            if ($instance) {
                $selection = new static;
                $selection->origin = 'sql';
                foreach ($instance->values_get() as $c_key => $c_value)
                    if ($c_key === 'attributes') $selection->{$c_key} = (Widget_Attributes::value_to_attributes($c_value) ?? []) + $selection->{$c_key};
                    else                         $selection->{$c_key} =                                         $c_value;
                foreach ($selection->data ?? [] as $c_key => $c_value)
                         $selection->             {$c_key} = $c_value;
                static::$cache[$selection->id] = $selection;
            }
        }
        if ($id === null && !static::$is_init___sql) {
            static::$is_init___sql = true;
            foreach (Entity::get('selection')->instances_select() as $c_instance) {
                $c_selection = new static;
                $c_selection->origin = 'sql';
                foreach ($c_instance->values_get() as $c_key => $c_value)
                    if ($c_key === 'attributes') $c_selection->{$c_key} = (Widget_Attributes::value_to_attributes($c_value) ?? []) + $c_selection->{$c_key};
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
        if (static::$cache[$id] instanceof External_cache && $load)
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
                if (static::$cache[$c_id] instanceof External_cache)
                    static::$cache[$c_id] =
                    static::$cache[$c_id]->load_from_nosql_storage();
        $result = static::$cache ?? [];
        if ($origin)
            foreach ($result as $c_id => $c_item)
                if ($c_item->origin !== $origin)
                    unset($result[$c_id]);
        return $result;
    }

    static function get_handlers($filter = null) {
        static::init();
        $result = [];
        foreach (static::$cache_handlers as $c_row_id => $c_handler)
            if ($filter === null ||
                $filter === $c_handler->entity_name)
                $result[$c_row_id] = $c_handler;
        return $result;
    }

    static function get_handler($row_id) {
        static::init();
        return static::$cache_handlers[$row_id] ?? null;
    }

}
