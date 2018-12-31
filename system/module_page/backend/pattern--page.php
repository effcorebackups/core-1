<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class page extends node implements has_external_cache {

  public $title;
  public $https;
  public $display;
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

  function render() {
    $result = '';
    $user_agent = core::server_user_agent_info_get();
    header('Content-language: '.language::current_code_get());
    header('Content-Type: text/html; charset='.$this->charset);
    if ($user_agent->name == 'msie') {
      header('X-UA-Compatible: IE=10');
    }

  # show important messages
    if (false && !empty($this->https) && url::current_get()->protocol_get() != 'https') {
      message::insert('This page should be use HTTPS protocol!', 'warning');
    }
    if ($user_agent->name == 'msie' &&
        $user_agent->name_version < 9) {
      message::insert(translation::get(
        'Internet Explorer below version %%_version no longer supported!', ['version' => 9]), 'warning'
      );
    }

  # render page
    $contents = new node();
    foreach ($this->children as $c_rowid => $c_part) {
      if (!$contents->child_select(            $c_part->region))
           $contents->child_insert(new node(), $c_part->region);
      $c_region = $contents->child_select($c_part->region);
      $c_part_markup = $c_part->markup_get($this);
      if ($c_part_markup) {
        $c_region->child_insert($c_part_markup, $c_rowid);
        if ($c_part->type == 'link') {
          $this->used_dpaths[] = $c_part->source;
        }
      }
    }

    $frontend = $this->frontend_markup_get();
    $template = template::make_new('page');
    $template->arg_set('lang_code', language::current_code_get());
    $template->arg_set('text_direction', $this->text_direction);
    $template->arg_set('charset',        $this->charset);
    $template->arg_set('head_title',     $this->title);
    $template->arg_set('head_icons',     $frontend->icons);
    $template->arg_set('head_styles',    $frontend->styles);
    $template->arg_set('head_scripts',   $frontend->scripts);
    foreach ($contents->children_select() as $c_region => $c_parts) {
      $template->arg_set($c_region, $c_parts);
    }
    if ($user_agent->name) $template->data->children['html']->attribute_insert('data-uagent', strtolower($user_agent->name.'-'.$user_agent->name_version));
    if ($user_agent->core) $template->data->children['html']->attribute_insert('data-uacore', strtolower($user_agent->core.'-'.$user_agent->core_version));
    event::start('on_page_before_render', null, [$this, $template]);

    $template->args['content'] = new text($template->args['content']->render());
    $template->arg_set('messages', message::markup_get());
    $result = $template->render();

    timer::tap('total');
    if (storage::get('files')->select('settings')['page']->console_display == 'yes') {
      console::information_insert('Total generation time',  locale::msecond_format(timer::period_get('total', 0, 1)));
      console::information_insert('Memory for php (bytes)', locale::number_format(memory_get_usage(true)));
      console::information_insert('Current language',       language::current_code_get());
      console::information_insert('User roles',             implode(', ', user::current_get()->roles));
      $result = str_replace('</body>', console::markup_get()->render().'</body>', $result);
    }
    return $result;
  }

  function frontend_markup_get() {
    $result = new \stdClass;
    $result->icons   = new node();
    $result->styles  = new node();
    $result->scripts = new node();
    foreach (static::frontend_all_get() as $c_row_id => $c_item) {
      if (is_array(static::is_displayed_by_used_dpaths($c_item->display, $this->used_dpaths)) ||
          is_array(static::is_displayed_by_current_url($c_item->display))) {

      # ─────────────────────────────────────────────────────────────────────
      # collect favicons
      # ─────────────────────────────────────────────────────────────────────
        if (isset($c_item->favicons)) {
          foreach ($c_item->favicons as $c_icon) {
            $c_url = new url($c_icon->file[0] == '/' ? $c_icon->file : '/'.module::get($c_item->module_id)->path.$c_icon->file);
            $result->icons->child_insert(new markup_simple('link', [
              'href' => $c_url->relative_get()
            ] + ($c_icon->attributes ?? []), $c_icon->weight ?? 0));
          }
        }

      # ─────────────────────────────────────────────────────────────────────
      # collect styles
      # ─────────────────────────────────────────────────────────────────────
        if (isset($c_item->styles)) {
          foreach ($c_item->styles as $c_style) {
            $c_url = new url($c_style->file[0] == '/' ? $c_style->file : '/'.module::get($c_item->module_id)->path.$c_style->file);
            $result->styles->child_insert(new markup_simple('link', [
              'href' => $c_url->relative_get()
            ] + ($c_style->attributes ?? []), $c_style->weight ?? 0));
          }
        }

      # ─────────────────────────────────────────────────────────────────────
      # collect scripts
      # ─────────────────────────────────────────────────────────────────────
        if (isset($c_item->scripts)) {
          foreach ($c_item->scripts as $c_script) {
            $c_url = new url($c_script->file[0] == '/' ? $c_script->file : '/'.module::get($c_item->module_id)->path.$c_script->file);
            $result->scripts->child_insert(new markup('script', [
              'src' => $c_url->relative_get()
            ] + ($c_script->attributes ?? []), [], $c_script->weight ?? 0));
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
    return ['display' => 'display', 'access' => 'access'];
  }

  static function cache_cleaning() {
    static::$cache          = null;
    static::$cache_frontend = null;
  }

  static function init() {
    foreach (storage::get('files')->select('pages') as $c_module_id => $c_pages) {
      foreach ($c_pages as $c_row_id => $c_page) {
        if (isset(static::$cache[$c_row_id])) console::log_about_duplicate_insert('page', $c_row_id);
        static::$cache[$c_row_id] = $c_page;
        static::$cache[$c_row_id]->module_id = $c_module_id;
      }
    }
    foreach (storage::get('files')->select('frontend') as $c_module_id => $c_frontends) {
      foreach ($c_frontends as $c_row_id => $c_frontend) {
        if (isset(static::$cache_frontend[$c_row_id])) console::log_about_duplicate_insert('frontend', $c_row_id);
        static::$cache_frontend[$c_row_id] = $c_frontend;
        static::$cache_frontend[$c_row_id]->module_id = $c_module_id;
      }
    }
  }

  static function current_get() {
    return static::$current;
  }

  static function get($row_id, $load = true) {
    if (static::$cache == null) static::init();
    if (static::$cache[$row_id] instanceof external_cache && $load)
        static::$cache[$row_id] = static::$cache[$row_id]->external_cache_load();
    return static::$cache[$row_id];
  }

  static function all_get($load = true) {
    if (static::$cache == null) static::init();
    if ($load)
      foreach (static::$cache as &$c_item)
        if ($c_item instanceof external_cache)
            $c_item = $c_item->external_cache_load();
    return static::$cache;
  }

  static function frontend_get($row_id) {
    if    (static::$cache_frontend == null) static::init();
    return static::$cache_frontend[$row_id];
  }

  static function frontend_all_get() {
    if    (static::$cache_frontend == null) static::init();
    return static::$cache_frontend;
  }

  static function is_displayed_by_used_dpaths($display, $used_dpaths) {
    $args = [];
    if (($display->check == 'block' &&
         $display->where == 'dpath' && preg_match(
         $display->match.'m', implode(nl, $used_dpaths), $args))) {
      return array_filter($args, 'is_string', ARRAY_FILTER_USE_KEY);
    }
  }

  static function is_displayed_by_current_url($display) {
    $args = [];
    if (($display->check == 'url' && $display->where == 'protocol' && preg_match($display->match, url::current_get()->protocol_get(), $args)) ||
        ($display->check == 'url' && $display->where == 'domain'   && preg_match($display->match, url::current_get()->domain_get(),   $args)) ||
        ($display->check == 'url' && $display->where == 'path'     && preg_match($display->match, url::current_get()->path_get(),     $args)) ||
        ($display->check == 'url' && $display->where == 'query'    && preg_match($display->match, url::current_get()->query_get(),    $args)) ||
        ($display->check == 'url' && $display->where == 'anchor'   && preg_match($display->match, url::current_get()->anchor_get(),   $args)) ||
        ($display->check == 'url' && $display->where == 'type'     && preg_match($display->match, url::current_get()->type_get(),     $args)) ||
        ($display->check == 'url' && $display->where == 'full'     && preg_match($display->match, url::current_get()->full_get(),     $args)) ) {
      return array_filter($args, 'is_string', ARRAY_FILTER_USE_KEY);
    }
  }

  static function find_and_render() {
    foreach (static::all_get(false) as $c_page) {
      $c_args = static::is_displayed_by_current_url($c_page->display);
      if (is_array($c_args)) {
        if ($c_page->access === null || access::check($c_page->access)) {
          if ($c_page instanceof external_cache)
              $c_page = $c_page->external_cache_load();
          static::$current = $c_page;
        # filter arguments
          foreach ($c_args as $c_key => $c_value) {
            $c_page->args_set($c_key, $c_value);
          }
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