<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class page
          implements has_different_cache {

  public $title;
  public $https;
  public $display;
  public $access;
  public $args = [];
  public $constants = [];
  public $content = [];

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

  # render frontend items: icons, styles, scripts
    $used_dpaths = [];
    foreach ($this->content as $c_block) {
      if ($c_block->type == 'link') {
        $used_dpaths[] = $c_block->dpath;
      }
    }
    $frontend = $this->get_frontend($used_dpaths);

  # collect page content
    $contents = [];
    foreach ($this->content as $c_block) {
      $c_region = isset($c_block->region) ?
                        $c_block->region : 'content';
      if (!isset($c_block->display) ||
          (isset($c_block->display) &&
                 $c_block->display->where === 'page_args' && preg_match(
                 $c_block->display->match, $this->args_get(
                 $c_block->display->check)))) {
        switch ($c_block->type) {
          case 'code': $contents[$c_region][] = call_user_func_array($c_block->handler, ['page' => $this] + $this->args_get()); break;
          case 'link': $contents[$c_region][] = storage::get('files')->select($c_block->dpath, true); break;
          case 'text': $contents[$c_region][] = new text($c_block->content); break;
          default: $contents[$c_region][] = $c_block;
        }
      }
    }

  # render each block
    $template = new template('page');
    foreach ($contents as $c_region_name => $c_blocks) {
      $rendered_c_region = '';
      foreach ($c_blocks as $c_block) {
        $rendered_c_region.= method_exists($c_block, 'render') ?
                                           $c_block->render() :
                                           $c_block;
      }
      $template->set_var($c_region_name,
        $rendered_c_region
      );
    }
    timer::tap('total');
    console::add_information('Total generation time', locale::format_msecond(timer::get_period('total', 0, 1)));
    console::add_information('Memory for php (bytes)', locale::format_number(memory_get_usage(true), 0, null, ' '));
    console::add_information('User roles', implode(', ', user::get_current()->roles));
    console::add_information('Session expiration date', locale::format_timestamp(session::id_decode_expire(session::id_get())));
    console::add_information('Current language', language::get_current());

    $template->set_var('attributes', factory::data_to_attr(['lang' => language::get_current()]));
    $template->set_var('console', console::render()); # @todo: only for admins
    $template->set_var('messages', message::render_all());
    $template->set_var('meta', $frontend->meta->render());
    $template->set_var('head_styles', $frontend->styles->render());
    $template->set_var('head_scripts', $frontend->scripts->render());
    $template->set_var('head_title', token::replace(translation::get($this->title)));

    return $template->render();
  }

  function get_frontend($used_dpaths) {
    $return = new \stdClass();
    $return->meta = new node();
    $return->styles = new node();
    $return->scripts = new node();
    $return->meta->child_insert(new markup_simple('meta', ['charset' => 'utf-8']));
    $frontend = storage::get('files')->select('frontend');
    foreach ($frontend as $c_module_id => $c_module_frontend) {
      foreach ($c_module_frontend as $c_row_id => $c_item) {
        if (($c_item->display->check === 'protocol' && $c_item->display->where === 'url'   && preg_match($c_item->display->match, url::get_current()->get_protocol())) ||
            ($c_item->display->check === 'domain'   && $c_item->display->where === 'url'   && preg_match($c_item->display->match, url::get_current()->get_domain()))   ||
            ($c_item->display->check === 'path'     && $c_item->display->where === 'url'   && preg_match($c_item->display->match, url::get_current()->get_path()))     ||
            ($c_item->display->check === 'query'    && $c_item->display->where === 'url'   && preg_match($c_item->display->match, url::get_current()->get_query()))    ||
            ($c_item->display->check === 'anchor'   && $c_item->display->where === 'url'   && preg_match($c_item->display->match, url::get_current()->get_anchor()))   ||
            ($c_item->display->check === 'type'     && $c_item->display->where === 'url'   && preg_match($c_item->display->match, url::get_current()->get_type()))     ||
            ($c_item->display->check === 'full'     && $c_item->display->where === 'url'   && preg_match($c_item->display->match, url::get_current()->get_full()))     ||
            ($c_item->display->check === 'dpath'    && $c_item->display->where === 'block' && preg_match($c_item->display->match.'m', implode(nl, $used_dpaths)))) {

        # render meta
          if (isset($c_item->favicons)) {
            foreach ($c_item->favicons as $c_icon) {
              $c_url = new url($c_icon->file[0] == '/' ? $c_icon->file : '/'.module::get($c_module_id)->get_path().$c_icon->file);
              $return->meta->child_insert(new markup_simple('link', [
                'rel'   => $c_icon->rel,
                'type'  => $c_icon->type,
                'href'  => $c_url->get_full(),
                'sizes' => $c_icon->sizes
              ]));
            }
          }

        # render styles
          if (isset($c_item->styles)) {
            foreach ($c_item->styles as $c_style) {
              $c_url = new url($c_style->file[0] == '/' ? $c_style->file : '/'.module::get($c_module_id)->get_path().$c_style->file);
              $return->styles->child_insert(new markup_simple('link', [
                'rel'   => 'stylesheet',
                'media' => $c_style->media,
                'href'  => $c_url->get_full()
              ]));
            }
          }

        # render scripts
          if (isset($c_item->scripts)) {
            foreach ($c_item->scripts as $c_script) {
              $c_url = new url($c_script->file[0] == '/' ? $c_script->file : '/'.module::get($c_module_id)->get_path().$c_script->file);
              $return->scripts->child_insert(new markup('script', [
                'src' => $c_url->get_full()
              ]));
            }
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
  static protected $current;

  static function get_non_different_properties() {
    return ['display' => 'display', 'access' => 'access'];
  }

  static function init() {
    foreach (storage::get('files')->select('pages') as $c_module_id => $c_pages) {
      foreach ($c_pages as $c_row_id => $c_page) {
        static::$cache[$c_row_id] = $c_page;
        static::$cache[$c_row_id]->module_id = $c_module_id;
      }
    }
  }

  static function get_all() {
    if   (!static::$cache) static::init();
    return static::$cache;
  }

  static function get_current() {
    return static::$current;
  }

  static function find_and_render() {
    foreach (static::get_all() as $c_row_id => $c_page) {
      $c_matches = [];
      if (($c_page->display->check === 'protocol' && $c_page->display->where === 'url' && preg_match($c_page->display->match, url::get_current()->get_protocol(), $c_matches)) ||
          ($c_page->display->check === 'domain'   && $c_page->display->where === 'url' && preg_match($c_page->display->match, url::get_current()->get_domain(),   $c_matches)) ||
          ($c_page->display->check === 'path'     && $c_page->display->where === 'url' && preg_match($c_page->display->match, url::get_current()->get_path(),     $c_matches)) ||
          ($c_page->display->check === 'query'    && $c_page->display->where === 'url' && preg_match($c_page->display->match, url::get_current()->get_query(),    $c_matches)) ||
          ($c_page->display->check === 'anchor'   && $c_page->display->where === 'url' && preg_match($c_page->display->match, url::get_current()->get_anchor(),   $c_matches)) ||
          ($c_page->display->check === 'type'     && $c_page->display->where === 'url' && preg_match($c_page->display->match, url::get_current()->get_type(),     $c_matches)) ||
          ($c_page->display->check === 'full'     && $c_page->display->where === 'url' && preg_match($c_page->display->match, url::get_current()->get_full(),     $c_matches)) ) {
        if (!isset($c_page->access) ||
            (isset($c_page->access) && access::check($c_page->access))) {
          if ($c_page instanceof different_cache)
              $c_page = $c_page->get_different_cache();
          static::$current = $c_page;
        # filter arguments
          $c_args = array_filter($c_matches, 'is_string', ARRAY_FILTER_USE_KEY);
          foreach ($c_args as $c_key => $c_value) {
            $c_page->args_set($c_key, $c_value);
          }
        # render page
          return $c_page->render();
        } else {
          factory::send_header_and_exit('access_denided');
        }
      }
    }
  # no matches case
    factory::send_header_and_exit('not_found');
  }

}}