<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class page extends node implements has_external_cache {

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
            event::start('on_page_build_before', $this->id, ['page' => &$this]);
            $this->_markup = core::deep_clone(layout::select($this->id_layout));
            if ($this->_markup) {
                foreach ($this->_markup->children_select_recursive() as $c_area) {
                    if ($c_area instanceof area && isset($c_area->id)) {
                        if (isset($this->blocks[$c_area->id])) {
                            $this->_areas_pointers[$c_area->id] = $c_area;
                            $c_blocks = $this->blocks[$c_area->id];
                            core::array_sort_by_number($c_blocks);
                            foreach ($c_blocks as $c_row_id => $c_block) {
                                if ($c_blocks[$c_row_id] instanceof block_preset_link) $c_blocks[$c_row_id] = $c_block->block_make();
                                if ($c_blocks[$c_row_id] instanceof block) {
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
            } else {
                $this->_markup = new text(
                    'LOST LAYOUT: %%_id', ['id' => $this->id_layout ?: 'n/a']
                );
            }
            event::start('on_page_build_after', $this->id, ['page' => &$this]);
            $this->is_builded = true;
        }
    }

    function render() {
        $this->build();
        $settings = module::settings_get('page');
        $user_agent_info = request::user_agent_get_info();
        header('Content-language: '.language::code_get_current());
        header('Content-Type: text/html; charset='.$this->charset);
        if ($user_agent_info->name === 'msie' &&
            $user_agent_info->name_version === '11') {
            header('X-UA-Compatible: IE=10');
        }

        # show important messages
        if ($settings->show_warning_if_not_https && !empty($this->is_https) && url::get_current()->protocol !== 'https') {
            message::insert(
                'This page should be use HTTPS protocol!', 'warning'
            );
        }
        if ($user_agent_info->name === 'msie' && (int)$user_agent_info->name_version < 9) {
            message::insert(new text(
                'Internet Explorer below version %%_number no longer supported!', ['number' => 9]), 'warning'
            );
        }

        # page palette is dark or light
        $colors = color::get_all();
        $color_page = $colors[$settings->color__page_id] ?? null;
        $is_dark_palette = $color_page && $color_page->is_dark();

        # render page
        $template = template::make_new(template::pick_name($this->template));
        $html            = $template->target_get('html');
        $meta            = $template->target_get('head_meta');
        $body            = $template->target_get('body');
        $head_title_text = $template->target_get('head_title_text', true);

        if ($this->_areas_pointers) {
            core::array_sort_by_number($this->_areas_pointers, 'render_weight');
            foreach ($this->_areas_pointers as $c_area_id => $c_area) {
                $this->_areas_pointers[$c_area_id]->children_update(
                    [new text_simple( (new node([], $c_area->children_select(true)))->render() )]
                );
            }
        }

        $file_global_cssd = new file(dynamic::DIR_FILES.'global.cssd');
        if ($file_global_cssd->is_exists()) {
            frontend::insert('page_all__global__page', null, 'styles', ['path' => '/dynamic/files/global.cssd', 'attributes' => ['rel' => 'stylesheet', 'media' => 'all'], 'weight' => -600], 'page_style', 'page');
        }

        $frontend = frontend::markup_get($this->used_blocks_dpath, $this->used_blocks_cssid);
        $template->arg_set('charset',      $this    ->charset);
        $template->arg_set('head_icons',   $frontend->icons  );
        $template->arg_set('head_styles',  $frontend->styles );
        $template->arg_set('head_scripts', $frontend->scripts);

        $html->attribute_insert('lang', $this->lang_code ?: language::code_get_current());
        $html->attribute_insert('dir',  $this->text_direction);
        $html->attribute_insert('data-user-has-avatar', isset(user::get_current()->avatar_path) ? true : null);
        $html->attribute_insert('data-page-palette-is-dark', $is_dark_palette ? true : null); # note: refreshed after page reload
        $html->attribute_insert('data-css-path', core::sanitize_id(url::utf8_encode(trim(url::get_current()->path, '/'))));
        if ($user_agent_info->name) $html->attribute_insert('data-uagent', core::sanitize_id($user_agent_info->name.'-'.$user_agent_info->name_version));
        if ($user_agent_info->core) $html->attribute_insert('data-uacore', core::sanitize_id($user_agent_info->core.'-'.$user_agent_info->core_version));

        $head_title_text->text = $this->title;
        $meta->child_insert(
            new markup_simple('meta', ['name' => 'viewport', 'content' => $settings->page_meta_viewport]), 'viewport'
        );

        $body->attribute_insert('data-layout-id', $this->id_layout);
        $body->child_insert($this->_markup, 'markup');
        if (request::value_get('manage_layout', 0, '_GET') === 'true') {
            if (access::check((object)['roles' => ['registered' => 'registered']])) {
                $body->attribute_insert('data-is-managed-layout', true);
            }
        }

        $file_meta = new file(dynamic::DIR_FILES.'meta.html');
        if ($this->is_use_global_meta && $file_meta->is_exists()) {
            $template->arg_set('head_meta_custom_global',
                new text($file_meta->load(), [], false, $settings->apply_tokens_for_meta)
            );
        }
        if ($this->meta) {
            $template->arg_set('head_meta_custom',
                new text($this->meta, [], false, false)
            );
        }

        event::start('on_page_render_before', $this->id, ['page' => &$this, 'template' => &$template]);
        return $template->render();
    }

    ###########################
    ### static declarations ###
    ###########################

    static protected $current;
    static protected $cache;
    static protected $is_init_nosql = false;
    static protected $is_init___sql = false;

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
            foreach (storage::get('data')->select_array('pages') as $c_module_id => $c_pages) {
                foreach ($c_pages as $c_id => $c_page) {
                    if (isset(static::$cache[$c_id])) console::report_about_duplicate('pages', $c_id, $c_module_id, static::$cache[$c_id]);
                              static::$cache[$c_id] = $c_page;
                              static::$cache[$c_id]->module_id = $c_module_id;
                }
            }
        }
    }

    static function init_sql() {
        if (!static::$is_init___sql) {
             static::$is_init___sql = true;
            foreach (entity::get('page')->instances_select() as $c_instance) {
                $c_page = new static;
                foreach ($c_instance->values_get() as $c_key => $c_value)
                    $c_page->                        {$c_key} = $c_value;
                static::$cache[$c_page->id] = $c_page;
            }
        }
    }

    static function init_sql_by_id($id) {
        static::init();
        $instance = (new instance('page', [
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
        $instance = (new instance('page', [
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
        $path_current = url::get_current()->path;
        $page = static::get_by_url($path_current, true);
        if ($page) {
            if (access::check($page->access)) {
                if ($page->_match_args === [])
                    $page->_match_args['base'] = $path_current;
                foreach ($page->_match_args as $c_key => $c_value)
                    $page->args_set           ($c_key,   $c_value);
                       static::$current = $page;
                return static::$current;
            } else response::send_header_and_exit('access_forbidden');
        }     else response::send_header_and_exit('page_not_found');
    }

    static function get_current() {
        return static::$current;
    }

    static function get_by_id($id, $load = false) {
        static::init();
        if (isset(static::$cache[$id]) === false) static::init_sql_by_id($id);
        if (isset(static::$cache[$id]) === false) return;
        if (static::$cache[$id] instanceof external_cache && $load) static::$cache[$id] = static::$cache[$id]->load_from_nosql_storage();
        if (static::$cache[$id] instanceof page_hybrid    && $load) static::$cache[$id] = static::$cache[$id]->load_from___sql_storage();
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
        if ($result instanceof external_cache && $load) $result = $result->load_from_nosql_storage();
        if ($result instanceof page_hybrid    && $load) $result = $result->load_from___sql_storage();
        $result->_match_args = array_filter($result_args, 'is_string', ARRAY_FILTER_USE_KEY);
        return $result;
    }

    static function get_all($origin = null, $load = false) {
        if ($origin === 'nosql' ) {static::init();                    }
        if ($origin === 'hybrid') {static::init();                    }
        if ($origin === 'sql'   ) {                static::init_sql();}
        if ($origin ===  null   ) {static::init(); static::init_sql();}
        if ($load) foreach (static::$cache as $c_id => $c_item) {
            if (static::$cache[$c_id] instanceof external_cache) static::$cache[$c_id] = static::$cache[$c_id]->load_from_nosql_storage();
            if (static::$cache[$c_id] instanceof page_hybrid   ) static::$cache[$c_id] = static::$cache[$c_id]->load_from___sql_storage(); }
        $result = static::$cache ?? [];
        if ($origin)
            foreach ($result as $c_id => $c_item)
                if ($c_item->origin !== $origin)
                    unset($result[$c_id]);
        return $result;
    }

}
