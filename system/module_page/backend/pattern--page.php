<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class page
          implements has_external_cache {

  public $title;
  public $https;
  public $display;
  public $access;
  public $content = [];
  protected $args = [];
  protected $used_dpaths = [];

  function args_set($key, $value) {$this->args[$key] = $value;}
  function args_get($id = null) {
    return $id ? $this->args[$id] :
                 $this->args;
  }

  function render() {

  # check https (@todo: enable this message)
    if (false && !empty($this->https) && url::get_current()->get_protocol() != 'https') {
      message::insert('This page should be use HTTPS protocol!', 'warning');
    }

  # collect dpaths which used in page blocks
    foreach ($this->content as $c_block) {
      if ($c_block->type == 'link') {
        $this->used_dpaths[] = $c_block->dpath;
      }
    }

  # collect page blocks
    $contents = new node();
    foreach ($this->content as $c_block) {
      if (!isset($c_block->display) ||
          (isset($c_block->display) &&
                 $c_block->display->where == 'page_args' && preg_match(
                 $c_block->display->match, $this->args_get(
                 $c_block->display->check)))) {
        $c_region = isset($c_block->region) ?
                          $c_block->region : 'content';
        if (!$contents->child_select($c_region))
             $contents->child_insert(new node(), $c_region);
        $c_block_markup = null;
        switch ($c_block->type) {
          case 'code': $c_block_markup = call_user_func_array($c_block->handler, ['page' => $this] + $this->args_get()); break;
          case 'link': $c_block_markup = storage::get('files')->select($c_block->dpath, true);                           break;
          case 'text': $c_block_markup = new text($c_block->content);                                                    break;
          default    : $c_block_markup = $c_block;
        }
        if ($c_block_markup) {
          $contents->child_select($c_region)->child_insert($c_block_markup);
        }
      }
    }

  # render
    $frontend = $this->get_frontend();
    $template = new template('page');
    foreach ($contents->children_select() as $c_region => $c_blocks) {
      $template->set_arg($c_region, $c_blocks->render());
    }

    timer::tap('total');
    $this->set_page_information();
    $template->set_arg('attributes', core::data_to_attr(['lang' => language::get_current()]));
    $template->set_arg('meta',         $frontend->meta->render());
    $template->set_arg('head_styles',  $frontend->styles->render());
    $template->set_arg('head_scripts', $frontend->scripts->render());
    $template->set_arg('head_title', token::replace(translation::get($this->title)));
    $template->set_arg('console', console::render()); # @todo: only for admins
    $template->set_arg('messages', message::render_all());
    return $template->render();
  }

  function set_page_information() {
    console::add_information('Total generation time', locale::format_msecond(timer::get_period('total', 0, 1)));
    console::add_information('Memory for php (bytes)', locale::format_number(memory_get_usage(true), 0, null, ' '));
    console::add_information('User roles', implode(', ', user::get_current()->roles));
    console::add_information('Session expiration date', locale::format_timestamp(session::id_decode_expire(session::id_get())));
    console::add_information('Current language', language::get_current());
  }

  function get_frontend() {
    $return = new \stdClass;
    $return->meta    = new node();
    $return->styles  = new node();
    $return->scripts = new node();
    $return->meta->child_insert(new markup_simple('meta', ['charset' => 'utf-8']));
    foreach (static::get_frontend_all() as $c_row_id => $c_item) {
      if (is_array(static::is_displayed_by_used_dpaths($c_item->display, $this->used_dpaths)) ||
          is_array(static::is_displayed_by_current_url($c_item->display))) {

      # collect favicons
      # ─────────────────────────────────────────────────────────────────────
        if (isset($c_item->favicons)) {
          foreach ($c_item->favicons as $c_icon) {
            $c_url = new url($c_icon->file[0] == '/' ? $c_icon->file : '/'.module::get($c_item->module_id)->get_path().$c_icon->file);
            $return->meta->child_insert(new markup_simple('link', [
              'rel'   => $c_icon->rel,
              'type'  => $c_icon->type,
              'sizes' => $c_icon->sizes,
              'href'  => $c_url->get_relative()
            ]));
          }
        }

      # collect styles
      # ─────────────────────────────────────────────────────────────────────
        if (isset($c_item->styles)) {
          foreach ($c_item->styles as $c_style) {
            $c_url = new url($c_style->file[0] == '/' ? $c_style->file : '/'.module::get($c_item->module_id)->get_path().$c_style->file);
            $return->styles->child_insert(new markup_simple('link', [
              'rel'   => 'stylesheet',
              'media' => $c_style->media,
              'href'  => $c_url->get_relative()
            ]));
          }
        }

      # collect scripts
      # ─────────────────────────────────────────────────────────────────────
        if (isset($c_item->scripts)) {
          foreach ($c_item->scripts as $c_script) {
            $c_url = new url($c_script->file[0] == '/' ? $c_script->file : '/'.module::get($c_item->module_id)->get_path().$c_script->file);
            $return->scripts->child_insert(new markup('script', [
              'src' => $c_url->get_relative()
            ]));
          }
        }

      }
    }
    return $return;
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;
  static protected $cache_frontend;
  static protected $current;

  static function get_not_external_properties() {
    return ['display' => 'display', 'access' => 'access'];
  }

  static function init() {
    foreach (storage::get('files')->select('pages') as $c_module_id => $c_pages) {
      foreach ($c_pages as $c_row_id => $c_page) {
        if (isset(static::$cache[$c_row_id])) console::add_log_about_duplicate('page', $c_row_id);
        static::$cache[$c_row_id] = $c_page;
        static::$cache[$c_row_id]->module_id = $c_module_id;
      }
    }
    foreach (storage::get('files')->select('frontend') as $c_module_id => $c_frontends) {
      foreach ($c_frontends as $c_row_id => $c_frontend) {
        if (isset(static::$cache_frontend[$c_row_id])) console::add_log_about_duplicate('frontend', $c_row_id);
        static::$cache_frontend[$c_row_id] = $c_frontend;
        static::$cache_frontend[$c_row_id]->module_id = $c_module_id;
      }
    }
  }

  static function get_current() {
    return static::$current;
  }

  static function get_all() {
    if   (!static::$cache) static::init();
    return static::$cache;
  }

  static function get_frontend_all() {
    if   (!static::$cache_frontend) static::init();
    return static::$cache_frontend;
  }

  static function is_displayed_by_used_dpaths($display, $used_dpaths) {
    $args = [];
    if (($display->check === 'dpath' &&
         $display->where === 'block' && preg_match(
         $display->match.'m', implode(nl, $used_dpaths), $args))) {
      return array_filter($args, 'is_string', ARRAY_FILTER_USE_KEY);
    }
  }

  static function is_displayed_by_current_url($display) {
    $args = [];
    if (($display->check === 'protocol' && $display->where === 'url' && preg_match($display->match, url::get_current()->get_protocol(), $args)) ||
        ($display->check === 'domain'   && $display->where === 'url' && preg_match($display->match, url::get_current()->get_domain(),   $args)) ||
        ($display->check === 'path'     && $display->where === 'url' && preg_match($display->match, url::get_current()->get_path(),     $args)) ||
        ($display->check === 'query'    && $display->where === 'url' && preg_match($display->match, url::get_current()->get_query(),    $args)) ||
        ($display->check === 'anchor'   && $display->where === 'url' && preg_match($display->match, url::get_current()->get_anchor(),   $args)) ||
        ($display->check === 'type'     && $display->where === 'url' && preg_match($display->match, url::get_current()->get_type(),     $args)) ||
        ($display->check === 'full'     && $display->where === 'url' && preg_match($display->match, url::get_current()->get_full(),     $args)) ) {
      return array_filter($args, 'is_string', ARRAY_FILTER_USE_KEY);
    }
  }

  static function find_and_render() {
    foreach (static::get_all() as $c_page) {
      $c_args = static::is_displayed_by_current_url($c_page->display);
      if (is_array($c_args)) {
        if (!isset($c_page->access) ||
            (isset($c_page->access) && access::check($c_page->access))) {
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
          core::send_header_and_exit('access_denided');
        }
      }
    }
  # no matches case
    core::send_header_and_exit('not_found');
  }

}}