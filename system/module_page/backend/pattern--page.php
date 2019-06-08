<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class page extends node implements has_external_cache {

  public $id;
  public $id_layout = 'simple';
  public $title;
  public $https;
  public $url;
  public $access;
  public $parts;
  public $charset = 'utf-8';
  public $lang_code = 'en';
  public $text_direction = 'ltr';
  protected $args = [];
  protected $used_dpaths = [];

  function args_set($key, $value) {$this->args[$key] = $value;}
  function args_get($id = null) {
    return $id ? ($this->args[$id] ?? null) :
                  $this->args;
  }

  function build() {
    event::start('on_page_before_build', $this->id, [&$this]);
    core::array_sort_by_weight($this->parts);
    foreach ($this->parts as $c_row_id => $c_part) {
      if (!$this->child_select(            $c_part->id_area))
           $this->child_insert(new node(), $c_part->id_area);
      $c_area = $this->child_select       ($c_part->id_area);
      $c_part_markup = $c_part->markup_get($this);
      if ($c_part_markup) {
        $c_area->child_insert($c_part_markup, $c_row_id);
        if ($c_part->type == 'link') {
          $this->used_dpaths[] = $c_part->source;}}}
    event::start('on_page_after_build', $this->id, [&$this]);
  }

  function render() {
    $this->build();
    $settings = module::settings_get('page');
    $user_agent = core::server_get_user_agent_info();
    header('Content-language: '.language::code_get_current());
    header('Content-Type: text/html; charset='.$this->charset);
    if ($user_agent->name == 'msie' &&
        $user_agent->name_version == 11) {
      header('X-UA-Compatible: IE=10');
    }

  # show important messages
    if ($settings->show_warning_if_not_https && !empty($this->https) && url::get_current()->protocol_get() != 'https') {
      message::insert(
        'This page should be use HTTPS protocol!', 'warning'
      );
    }
    if ($user_agent->name == 'msie' &&
        $user_agent->name_version < 9) {
      message::insert(new text(
        'Internet Explorer below version %%_version no longer supported!', ['version' => 9]), 'warning'
      );
    }

  # render page
    event::start('on_page_before_render', $this->id, [&$this, &$template]);
    $frontend = frontend::markup_get($this->used_dpaths);
    $template = template::make_new('page');
    $layout = layout::select($this->id_layout);

    $html = $template->target_get('html');
    $html->attribute_insert('lang', language::code_get_current());
    $html->attribute_insert('dir', $this->text_direction);
    $html->attribute_insert('data-css-path', core::sanitize_id(trim(url::get_current()->path_get(), '/')));
    $head_title_text = $template->target_get('head_title_text', true);
    $head_title_text->text = $this->title;
    $template->arg_set('charset',      $this    ->charset);
    $template->arg_set('head_icons',   $frontend->icons  );
    $template->arg_set('head_styles',  $frontend->styles );
    $template->arg_set('head_scripts', $frontend->scripts);
    if ($user_agent->name) $html->attribute_insert('data-uagent', strtolower($user_agent->name.'-'.$user_agent->name_version));
    if ($user_agent->core) $html->attribute_insert('data-uacore', strtolower($user_agent->core.'-'.$user_agent->core_version));

    foreach ($this->children_select() as $c_id_area => $c_parts) {
      $template->arg_set($c_id_area, $c_parts);
    }
    $template->args['content'] = new text($template->args['content']->render());
    $template->arg_set('messages', message::markup_get());
    return $template->render();
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;
  static protected $current;

  static function not_external_properties_get() {
    return ['id' => 'id', 'url' => 'url', 'access' => 'access'];
  }

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    if (static::$cache == null) {
      foreach (storage::get('files')->select('pages') as $c_module_id => $c_pages) {
        foreach ($c_pages as $c_id => $c_page) {
          if (isset(static::$cache[$c_id])) console::log_insert_about_duplicate('page', $c_id, $c_module_id);
          static::$cache[$c_id] = $c_page;
          static::$cache[$c_id]->module_id = $c_module_id;
        }
      }
    }
  }

  static function get_current() {
    return static::$current;
  }

  static function get($id, $load = true) {
    static::init();
    if (isset(static::$cache[$id]) == false) return;
    if (static::$cache[$id] instanceof external_cache && $load)
        static::$cache[$id] = static::$cache[$id]->external_cache_load();
    return static::$cache[$id];
  }

  static function get_all($load = true) {
    static::init();
    if ($load)
      foreach (static::$cache as &$c_item)
        if ($c_item instanceof external_cache)
            $c_item = $c_item->external_cache_load();
    return static::$cache;
  }

  static function find_and_render() {
    $is_match = false;
    $path_current = url::get_current()->path_get();
    $path_args = [];
    foreach (static::get_all(false) as $c_page) {
      if ($c_page->url[0] != '%' && $path_current == $c_page->url                            ) {$is_match = true; $path_args = ['base' => $path_current];                                  }
      if ($c_page->url[0] == '%' &&       preg_match($c_page->url, $path_current, $path_args)) {$is_match = true; $path_args = array_filter($path_args, 'is_string', ARRAY_FILTER_USE_KEY);}
      if ($is_match) {
        if ($c_page->access == null || access::check($c_page->access)) {
          if ($c_page instanceof external_cache)
              $c_page = $c_page->external_cache_load();
          foreach ($path_args as $c_key => $c_value)
            $c_page->args_set   ($c_key,   $c_value);
          static::$current = $c_page;
          return static::$current->render();
        } else core::send_header_and_exit('access_forbidden');
      }
    }
  # no matches case
    core::send_header_and_exit('page_not_found');
  }

}}