<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class page extends node implements has_external_cache {

  public $id;
  public $title;
  public $https;
  public $url;
  public $access;
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
    foreach ($this->parts as $c_row_id => $c_part) {
      if (!$this->child_select(            $c_part->region))
           $this->child_insert(new node(), $c_part->region);
      $c_region = $this->child_select($c_part->region);
      $c_part_markup = $c_part->markup_get($this);
      if ($c_part_markup) {
        $c_region->child_insert($c_part_markup, $c_row_id);
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
    $frontend = $this->frontend_markup_get();
    $template = template::make_new('page');
    $html = $template->target_get('html');
    $html->attribute_insert('lang', language::code_get_current());
    $html->attribute_insert('dir', $this->text_direction);
    $html->attribute_insert('data-css-path', core::sanitize_id(trim(url::get_current()->path_get(), '/')));
    $head_title_text = $template->target_get('head_title_text', true);
    $head_title_text->text = $this->title;
    $template->arg_set('charset',        $this    ->charset);
    $template->arg_set('head_icons',     $frontend->icons  );
    $template->arg_set('head_styles',    $frontend->styles );
    $template->arg_set('head_scripts',   $frontend->scripts);
    foreach ($this->children_select() as $c_region => $c_parts) {
      $template->arg_set($c_region, $c_parts);
    }
    if ($user_agent->name) $template->data->children['html']->attribute_insert('data-uagent', strtolower($user_agent->name.'-'.$user_agent->name_version));
    if ($user_agent->core) $template->data->children['html']->attribute_insert('data-uacore', strtolower($user_agent->core.'-'.$user_agent->core_version));
    $template->args['content'] = new text($template->args['content']->render());
    $template->arg_set('messages', message::markup_get());
    return $template->render();
  }

  function frontend_markup_get() {
    $result = new \stdClass;
    $result->icons   = new node();
    $result->styles  = new node();
    $result->scripts = new node();
    foreach (static::frontend_get_all() as $c_row_id => $c_items) {
      if (is_array(static::is_visible_by_display_and_dpaths($c_items->display, $this->used_dpaths)) ||
          is_array(static::is_visible_by_display           ($c_items->display                    ))) {

      # ─────────────────────────────────────────────────────────────────────
      # collect favicons
      # ─────────────────────────────────────────────────────────────────────
        if (isset($c_items->favicons)) {
          foreach ($c_items->favicons as $c_item) {
            $c_module_id = $c_item->module_id ?? $c_items->module_id;
            $c_url = new url($c_item->file[0] == '/' ? $c_item->file : '/'.module::get($c_module_id)->path.$c_item->file);
            $result->icons->child_insert(new markup_simple('link', [
              'href' => $c_url->tiny_get()
            ] + ($c_item->attributes ?? []), $c_item->weight ?? 0));
          }
        }

      # ─────────────────────────────────────────────────────────────────────
      # collect styles
      # ─────────────────────────────────────────────────────────────────────
        if (isset($c_items->styles)) {
          foreach ($c_items->styles as $c_item) {
            $c_module_id = $c_item->module_id ?? $c_items->module_id;
            $c_url = new url($c_item->file[0] == '/' ? $c_item->file : '/'.module::get($c_module_id)->path.$c_item->file);
            $result->styles->child_insert(new markup_simple('link', [
              'href' => $c_url->tiny_get()
            ] + ($c_item->attributes ?? []), $c_item->weight ?? 0));
          }
        }

      # ─────────────────────────────────────────────────────────────────────
      # collect scripts
      # ─────────────────────────────────────────────────────────────────────
        if (isset($c_items->scripts)) {
          foreach ($c_items->scripts as $c_item) {
            $c_module_id = $c_item->module_id ?? $c_items->module_id;
            $c_url = new url($c_item->file[0] == '/' ? $c_item->file : '/'.module::get($c_module_id)->path.$c_item->file);
            $result->scripts->child_insert(new markup('script', [
              'src' => $c_url->tiny_get()
            ] + ($c_item->attributes ?? []), [], $c_item->weight ?? 0));
          }
        }

      }
    }
    return $result;
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;
  static protected $cache_frontend;
  static protected $current;

  static function not_external_properties_get() {
    return ['id' => 'id', 'url' => 'url', 'access' => 'access'];
  }

  static function cache_cleaning() {
    static::$cache          = null;
    static::$cache_frontend = null;
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

  static function init_frontend() {
    if (static::$cache_frontend == null) {
      foreach (storage::get('files')->select('frontend') as $c_module_id => $c_frontends) {
        foreach ($c_frontends as $c_row_id => $c_frontend) {
          if (isset(static::$cache_frontend[$c_row_id])) console::log_insert_about_duplicate('frontend', $c_row_id, $c_module_id);
          static::$cache_frontend[$c_row_id] = $c_frontend;
          static::$cache_frontend[$c_row_id]->module_id = $c_module_id;
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

  static function frontend_get($row_id) {
    static::init_frontend();
    return static::$cache_frontend[$row_id];
  }

  static function frontend_get_all() {
    static::init_frontend();
    return static::$cache_frontend;
  }

  static function is_visible_by_url($url) {
    $args = [];
    if (preg_match($url, url::get_current()->path_get(), $args)) {
      return array_filter($args, 'is_string', ARRAY_FILTER_USE_KEY);
    }
  }

  static function is_visible_by_display($display) {
    $args = [];
    if (($display->check == 'url' && $display->where == 'protocol' && preg_match($display->match, url::get_current()->protocol_get(), $args)) ||
        ($display->check == 'url' && $display->where == 'domain'   && preg_match($display->match, url::get_current()->domain_get  (), $args)) ||
        ($display->check == 'url' && $display->where == 'path'     && preg_match($display->match, url::get_current()->path_get    (), $args)) ||
        ($display->check == 'url' && $display->where == 'query'    && preg_match($display->match, url::get_current()->query_get   (), $args)) ||
        ($display->check == 'url' && $display->where == 'anchor'   && preg_match($display->match, url::get_current()->anchor_get  (), $args)) ||
        ($display->check == 'url' && $display->where == 'type'     && preg_match($display->match, url::get_current()->type_get    (), $args)) ||
        ($display->check == 'url' && $display->where == 'full'     && preg_match($display->match, url::get_current()->full_get    (), $args)) ) {
      return array_filter($args, 'is_string', ARRAY_FILTER_USE_KEY);
    }
  }

  static function is_visible_by_display_and_dpaths($display, $used_dpaths) {
    $args = [];
    if (($display->check == 'block' &&
         $display->where == 'dpath' && preg_match(
         $display->match.'m', implode(nl, $used_dpaths), $args))) {
      return array_filter($args, 'is_string', ARRAY_FILTER_USE_KEY);
    }
  }

  static function find_and_render() {
    foreach (static::get_all(false) as $c_page) {
      $c_args = static::is_visible_by_url($c_page->url);
      if (is_array($c_args)) {
        if ($c_page->access === null || access::check($c_page->access)) {
          if ($c_page instanceof external_cache)
              $c_page = $c_page->external_cache_load();
          static::$current = $c_page;
        # filter arguments
          foreach ($c_args as $c_key => $c_value)
            $c_page->args_set($c_key,   $c_value);
        # render page
          return $c_page->render();
        } else {
          core::send_header_and_exit('access_forbidden');
        }
      }
    }
  # no matches case
    core::send_header_and_exit('page_not_found');
  }

}}