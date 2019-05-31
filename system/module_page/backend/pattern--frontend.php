<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class frontend {

  public $display;
  public $favicons = [];
  public $styles   = [];
  public $scripts  = [];

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    if (static::$cache == null) {
      foreach (storage::get('files')->select('frontend') as $c_module_id => $c_frontends) {
        foreach ($c_frontends as $c_row_id => $c_frontend) {
          if (isset(static::$cache[$c_row_id])) console::log_insert_about_duplicate('frontend', $c_row_id, $c_module_id);
          static::$cache[$c_row_id] = $c_frontend;
          static::$cache[$c_row_id]->module_id = $c_module_id;
        }
      }
    }
  }

  static function select_all() {
    static::init();
    return static::$cache;
  }

  static function select($row_id) {
    static::init();
    return static::$cache[$row_id];
  }

  static function insert($row_id, $display = null, $type = 'styles', $element) {
    static::init();
    static::$cache[$row_id] = new static;
    static::$cache[$row_id]->display = $display;
    static::$cache[$row_id]->module_id = null;
    static::$cache[$row_id]->{$type}[] = (object)$element;
  }

  static function markup_get($used_dpaths) {
    $result          = new \stdClass;
    $result->icons   = new node();
    $result->styles  = new node();
    $result->scripts = new node();
    foreach (static::select_all() as $c_row_id => $c_items) {
      if (                                                  $c_items->display == null         ||
          is_array(static::is_visible_by_display_and_dpaths($c_items->display, $used_dpaths)) ||
          is_array(static::is_visible_by_display           ($c_items->display              ))) {

      # ─────────────────────────────────────────────────────────────────────
      # collect favicons
      # ─────────────────────────────────────────────────────────────────────
        foreach ($c_items->favicons as $c_item) {
          $c_url = new url($c_item->file[0] == '/' ? $c_item->file : '/'.module::get($c_items->module_id)->path.$c_item->file);
          $result->icons->child_insert(new markup_simple('link', [
            'href' => $c_url->tiny_get()
          ] + ($c_item->attributes ?? []), $c_item->weight ?? 0));
        }

      # ─────────────────────────────────────────────────────────────────────
      # collect styles
      # ─────────────────────────────────────────────────────────────────────
        foreach ($c_items->styles as $c_item) {
          $c_url = new url($c_item->file[0] == '/' ? $c_item->file : '/'.module::get($c_items->module_id)->path.$c_item->file);
          $result->styles->child_insert(new markup_simple('link', [
            'href' => $c_url->tiny_get()
          ] + ($c_item->attributes ?? []), $c_item->weight ?? 0));
        }

      # ─────────────────────────────────────────────────────────────────────
      # collect scripts
      # ─────────────────────────────────────────────────────────────────────
        foreach ($c_items->scripts as $c_item) {
          $c_url = new url($c_item->file[0] == '/' ? $c_item->file : '/'.module::get($c_items->module_id)->path.$c_item->file);
          $result->scripts->child_insert(new markup('script', [
            'src' => $c_url->tiny_get()
          ] + ($c_item->attributes ?? []), [], $c_item->weight ?? 0));
        }

      }
    }
    return $result;
  }

  static function is_visible_by_display_and_dpaths($display, $used_dpaths) {
    $args = [];
    if (($display->check == 'block' &&
         $display->where == 'dpath' && preg_match(
         $display->match.'m', implode(nl, $used_dpaths), $args))) {
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

}}