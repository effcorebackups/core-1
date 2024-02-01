<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Page extends Node implements has_Data_cache {

    public $template = 'page';
    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
    public $id;
    public $id_layout = 'simple';
    public $title;
    public $url;
    public $lang_code;
    public $text_direction = 'ltr';
    public $charset = 'utf-8';
    public $is_https;
    public $is_use_global_meta = 1;
    public $is_embedded = 1;
    public $meta;
    public $access;
    public $blocks;
    public $data;
    public $origin = 'nosql'; # nosql | sql
    public $module_id;
    public $_markup;
    public $_layout;
    public $_areas_pointers = [];
    protected $args              = [];
    protected $used_blocks_dpath = [];
    protected $used_blocks_cssid = [];

    function args_set($name, $value) {$this->args[$name] = $value;}
    function args_get($id = null) {
        return $id ? ($this->args[$id] ?? null) :
                      $this->args;
    }

    function build() {
        if (!$this->is_builded) {
            Event::start('on_page_build_before', $this->id, ['page' => &$this]);
            $this->_layout = Core::deep_clone(Layout::select($this->id_layout));
            if ($this->_layout) {
                # prepare each area
                foreach ($this->_layout->children_select_recursive() as $c_area) {
                    if ($c_area instanceof Area ||
                        $c_area instanceof Area_group) {
                        $c_area->states_set(
                            $c_area->id &&
                            isset($this->_layout->states[$c_area->id]) ?
                                  $this->_layout->states[$c_area->id] : []);
                        $c_area->build();
                    }
                }
                # prepare each block
                foreach ($this->_layout->children_select_recursive() as $c_area) {
                    if ($c_area instanceof Area) {
                        if (isset($c_area->id) && isset($this->blocks[$c_area->id])) {
                            $this->_areas_pointers[$c_area->id] = $c_area;
                            $c_blocks = $this->blocks[$c_area->id];
                            Core::array_sort_by_number($c_blocks);
                            foreach ($c_blocks as $c_row_id => $c_block) {
                                if ($c_blocks[$c_row_id] instanceof Block_preset_link) $c_blocks[$c_row_id] = $c_block->block_make();
                                if ($c_blocks[$c_row_id] instanceof Block) {
                                    $c_blocks[$c_row_id]->build($this);
                                    if ($c_blocks[$c_row_id]->children_select_count()) {
                                        $c_area->child_insert($c_blocks[$c_row_id], $c_row_id);
                                        if (isset($c_blocks[$c_row_id]->attributes['data-id']))
                                            $this->used_blocks_cssid[$c_blocks[$c_row_id]->attributes['data-id']] =
                                                                     $c_blocks[$c_row_id]->attributes['data-id'];
                                        if ($c_blocks[$c_row_id]->type === 'link' ||
                                            $c_blocks[$c_row_id]->type === 'copy') {
                                            $this->used_blocks_dpath[] = $c_blocks[$c_row_id]->source;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $this->_markup = $this->_layout;
            } else {
                $this->_markup = new Text(
                    'LOST LAYOUT: %%_id', ['id' => $this->id_layout ?: 'n/a']
                );
            }
            Event::start('on_page_build_after', $this->id, ['page' => &$this]);
            $this->is_builded = true;
        }
    }

    function render() {
        $this->build();
        $settings = Module::settings_get('page');
        header('content-language: '.Language::code_get_current());
        header('content-type: text/html; charset='.$this->charset);

        # show important messages
        if ($settings->show_warning_if_not_https && !empty($this->is_https) && Url::get_current()->protocol !== 'https') {
            Message::insert(
                'This page should be use HTTPS protocol!', 'warning'
            );
        }

        # page palette is dark or light
        $is_dark_palette = Color_profile::get_current()->is_dark ?? true;

        # global styles
        $file_global_cssd = new File(Dynamic::DIR_FILES.'global.cssd');
        if ($file_global_cssd->is_exists()) {
            Frontend::insert('page_all__global__page', null, 'styles', [
                'path' => '/dynamic/files/global.cssd',
                'attributes' => [
                    'rel'   => 'stylesheet',
                    'media' => 'all'],
                'weight' => -600], 'page_style', 'page');
        }

        # render page
        $template = Template::make_new(Template::pick_name($this->template));

        if ($this->_areas_pointers) {
            Core::array_sort_by_number($this->_areas_pointers, 'render_weight');
            foreach ($this->_areas_pointers as $c_area_id => $c_area) {
                $this->_areas_pointers[$c_area_id]->children_update(
                    [new Text_simple( (new Node([], $c_area->children_select(true)))->render() )]
                );
            }
        }

        $frontend = Frontend::markup_get($this->used_blocks_dpath, $this->used_blocks_cssid);
        $template->arg_set('lang'   , $this->lang_code ?: Language::code_get_current());
        $template->arg_set('dir'    , $this->text_direction);
        $template->arg_set('icons'  , $frontend->icons  ->render());
        $template->arg_set('styles' , $frontend->styles ->render());
        $template->arg_set('scripts', $frontend->scripts->render());

        $html_attributes = [];
        $html_attributes['data-user-has-avatar'] = isset(User::get_current()->avatar_path) ? true : null;
        $html_attributes['data-page-palette-is-dark'] = $is_dark_palette ? true : null; # note: refreshed after page reload
        $html_attributes['data-css-path'] = Security::sanitize_id(Url::UTF8_encode(trim(Url::get_current()->path, '/')));
        $template->arg_set('html_custom_attributes',
            Core::data_to_attributes($html_attributes)
        );

        $template->arg_set('title',
            (new Text($this->title, [], true, true))->render()
        );

        $body_attributes = [];
        $body_attributes['data-layout-id'] = $this->id_layout;
        if (Request::value_get('manage_layout', 0, '_GET') === 'true')
            if (Access::check((object)['roles' => ['registered' => 'registered']]))
                $body_attributes['data-is-managed-layout'] = true;
        $template->arg_set('body_custom_attributes',
            Core::data_to_attributes($body_attributes)
        );

        $template->arg_set('body',
            $this->_markup->render()
        );

        $meta_charset  = (new Markup_simple('meta', ['charset' => $this->charset]))->render();
        $meta_viewport = (new Markup_simple('meta', ['name' => 'viewport', 'content' => $settings->page_meta_viewport]))->render();
        $template->arg_set('meta', $meta_charset.$meta_viewport);

        $file_meta = new File(Dynamic::DIR_FILES.'meta.html');
        if ($this->is_use_global_meta && $file_meta->is_exists()) {
            $template->arg_set('meta_custom_global',
                (new Text($file_meta->load(), [], false, $settings->apply_tokens_for_meta))->render()
            );
        }
        if ($this->meta) {
            $template->arg_set('meta_custom', $this->meta);
        }

        Event::start('on_page_render_before', $this->id, ['page' => &$this, 'template' => &$template]);
        return $template->render();
    }

    ###########################
    ### static declarations ###
    ###########################

    protected static $current;
    protected static $cache;
    protected static $is_init_nosql = false;
    protected static $is_init___sql = false;

    static function not_external_properties_get() {
        return [
            'id'     => 'id',
            'url'    => 'url',
            'access' => 'access',
            'origin' => 'origin'
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
            foreach (Storage::get('data')->select_array('pages') as $c_module_id => $c_pages) {
                foreach ($c_pages as $c_id => $c_page) {
                    if (isset(static::$cache[$c_id])) Console::report_about_duplicate('pages', $c_id, $c_module_id, static::$cache[$c_id]);
                              static::$cache[$c_id] = $c_page;
                              static::$cache[$c_id]->module_id = $c_module_id;
                }
            }
        }
    }

    static function init_sql() {
        if (!static::$is_init___sql) {
             static::$is_init___sql = true;
            foreach (Entity::get('page')->instances_select() as $c_instance) {
                $c_page = new static;
                foreach ($c_instance->values_get() as $c_key => $c_value)
                    $c_page->                        {$c_key} = $c_value;
                static::$cache[$c_page->id] = $c_page;
            }
        }
    }

    static function init_sql_by_id($id) {
        static::init();
        $instance = (new Instance('page', [
            'id' => $id
        ]))->select();
        if ($instance) {
            $page = new static;
            foreach ($instance->values_get() as $c_key => $c_value)
                $page->                        {$c_key} = $c_value;
                   static::$cache[$page->id] = $page;
            return static::$cache[$page->id];
        }
    }

    static function init_sql_by_url($url) {
        static::init();
        $instance = (new Instance('page', [
            'url' => $url
        ]))->select();
        if ($instance) {
            $page = new static;
            foreach ($instance->values_get() as $c_key => $c_value)
                $page->                        {$c_key} = $c_value;
                   static::$cache[$page->id] = $page;
            return static::$cache[$page->id];
        }
    }

    static function init_current() {
        $path_current = Url::get_current()->path;
        $page = static::get_by_url($path_current, true);
        if ($page) {
            if (Access::check($page->access)) {
                if ($page->_match_args === [])
                    $page->_match_args['base'] = $path_current;
                foreach ($page->_match_args as $c_key => $c_value)
                    $page->args_set           ($c_key,   $c_value);
                       static::$current = $page;
                return static::$current;
            } else Response::send_header_and_exit('page_access_forbidden');
        }     else Response::send_header_and_exit('page_not_found');
    }

    static function get_current() {
        return static::$current;
    }

    static function get_by_id($id, $load = false) {
        static::init();
        if (isset(static::$cache[$id]) === false) static::init_sql_by_id($id);
        if (isset(static::$cache[$id]) === false) return;
        if (static::$cache[$id] instanceof External_cache && $load) static::$cache[$id] = static::$cache[$id]->load_from_nosql_storage();
        if (static::$cache[$id] instanceof Page_hybrid    && $load) static::$cache[$id] = static::$cache[$id]->load_from___sql_storage();
        return static::$cache[$id];
    }

    static function get_by_url($url, $load = false) {
        static::init();
        $result = null;
        $result_args = [];
        foreach (static::$cache as $c_item) {
            if ($c_item->url[0] !== '%' &&            $c_item->url === $url               ) {$result = $c_item; break;}
            if ($c_item->url[0] === '%' && preg_match($c_item->url,    $url, $result_args)) {$result = $c_item; break;} }
        if ($result === null) $result = static::init_sql_by_url($url);
        if ($result === null) return;
        if ($result instanceof External_cache && $load) $result = $result->load_from_nosql_storage();
        if ($result instanceof Page_hybrid    && $load) $result = $result->load_from___sql_storage();
        $result->_match_args = array_filter($result_args, 'is_string', ARRAY_FILTER_USE_KEY);
        return $result;
    }

    static function get_all($origin = null, $load = false) {
        if ($origin === 'nosql' ) {static::init();                    }
        if ($origin === 'hybrid') {static::init();                    }
        if ($origin === 'sql'   ) {                static::init_sql();}
        if ($origin ===  null   ) {static::init(); static::init_sql();}
        if ($load) foreach (static::$cache as $c_id => $c_item) {
            if (static::$cache[$c_id] instanceof External_cache) static::$cache[$c_id] = static::$cache[$c_id]->load_from_nosql_storage();
            if (static::$cache[$c_id] instanceof Page_hybrid   ) static::$cache[$c_id] = static::$cache[$c_id]->load_from___sql_storage(); }
        $result = static::$cache ?? [];
        if ($origin)
            foreach ($result as $c_id => $c_item)
                if ($c_item->origin !== $origin)
                    unset($result[$c_id]);
        return $result;
    }

    static function changes_store($values = []) {
        $result = true;
        if (array_key_exists('apply_tokens_for_meta', $values)) {
            if ($values['apply_tokens_for_meta'] !== null) $result&= Storage::get('data')->changes_register  ('page', 'update', 'settings/page/apply_tokens_for_meta', $values['apply_tokens_for_meta'], false);
            if ($values['apply_tokens_for_meta'] === null) $result&= Storage::get('data')->changes_unregister('page', 'update', 'settings/page/apply_tokens_for_meta', null                            , false);
        }
        if (array_key_exists('apply_tokens_for_robots', $values)) {
            if ($values['apply_tokens_for_robots'] !== null) $result&= Storage::get('data')->changes_register  ('page', 'update', 'settings/page/apply_tokens_for_robots', $values['apply_tokens_for_robots'], false);
            if ($values['apply_tokens_for_robots'] === null) $result&= Storage::get('data')->changes_unregister('page', 'update', 'settings/page/apply_tokens_for_robots', null                              , false);
        }
        if (array_key_exists('apply_tokens_for_sitemap', $values)) {
            if ($values['apply_tokens_for_sitemap'] !== null) $result&= Storage::get('data')->changes_register  ('page', 'update', 'settings/page/apply_tokens_for_sitemap', $values['apply_tokens_for_sitemap'], false);
            if ($values['apply_tokens_for_sitemap'] === null) $result&= Storage::get('data')->changes_unregister('page', 'update', 'settings/page/apply_tokens_for_sitemap', null                               , false);
        }
        if (array_key_exists('page_width_min',     $values) ||
            array_key_exists('page_width_mobile',  $values) ||
            array_key_exists('page_width_max',     $values) ||
            array_key_exists('page_meta_viewport', $values)) {
            if ($values['page_width_min'    ] !== null) $result&= Storage::get('data')->changes_register  ('page', 'update', 'settings/page/page_width_min'    , $values['page_width_min'    ], false);
            if ($values['page_width_mobile' ] !== null) $result&= Storage::get('data')->changes_register  ('page', 'update', 'settings/page/page_width_mobile' , $values['page_width_mobile' ], false);
            if ($values['page_width_max'    ] !== null) $result&= Storage::get('data')->changes_register  ('page', 'update', 'settings/page/page_width_max'    , $values['page_width_max'    ], false);
            if ($values['page_meta_viewport'] !== null) $result&= Storage::get('data')->changes_register  ('page', 'update', 'settings/page/page_meta_viewport', $values['page_meta_viewport'], false);
            if ($values['page_width_min'    ] === null) $result&= Storage::get('data')->changes_unregister('page', 'update', 'settings/page/page_width_min'    , null                         , false);
            if ($values['page_width_mobile' ] === null) $result&= Storage::get('data')->changes_unregister('page', 'update', 'settings/page/page_width_mobile' , null                         , false);
            if ($values['page_width_max'    ] === null) $result&= Storage::get('data')->changes_unregister('page', 'update', 'settings/page/page_width_max'    , null                         , false);
            if ($values['page_meta_viewport'] === null) $result&= Storage::get('data')->changes_unregister('page', 'update', 'settings/page/page_meta_viewport', null                         , false);
        }
        $result&= Storage_Data::cache_update();
        return $result;
    }

}
