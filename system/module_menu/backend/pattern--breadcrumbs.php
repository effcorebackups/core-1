<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class breadcrumbs extends markup {

  public $tag_name = 'x-breadcrumbs';
  public $id;
  public $links = [];
  public $is_remove_last_link = true;

  function build() {
    if (!$this->is_builded) {
      event::start('on_breadcrumbs_build_before', $this->id, [&$this]);
      $this->children_delete();
      foreach ($this->links as $rowid => $c_link) {
        if ($this->is_remove_last_link && $c_link == end($this->links)) break;
        $c_link_markup = new markup('a', ['href' => $c_link->url], new text($c_link->title, [], true, true), $c_link->weight ?? 0);
        if (url::is_active      ($c_link->url)) $c_link_markup->attribute_insert('aria-selected',       'true');
        if (url::is_active_trail($c_link->url)) $c_link_markup->attribute_insert('data-selected-trail', 'true');
        $this->child_insert($c_link_markup);
      }
      event::start('on_breadcrumbs_build_after', $this->id, [&$this]);
      $this->is_builded = true;
    }
  }

  function link_select_all() {
    return $this->links;
  }

  function link_select($rowid) {
    return $this->links[$rowid];
  }

  function link_insert($rowid, $title, $url, $weight = null) {
    $this->links[$rowid] = (object)[
      'title'  => $title,
      'url'    => $url,
      'weight' => $weight === null ? - count($this->links) :
                  $weight
    ];
  }

  function link_update($rowid, $title = null, $url = null, $weight = null) {
    if ($title  !== null) $this->links[$rowid]->title  = $title;
    if ($url    !== null) $this->links[$rowid]->url    = $url;
    if ($weight !== null) $this->links[$rowid]->weight = $weight;
  }

  function link_delete($rowid) {
    unset($this->links[$rowid]);
  }

  function render() {
    $this->build();
    return parent::render();
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    if (static::$cache == null) {
      foreach (storage::get('files')->select('breadcrumbs') as $c_module_id => $c_breadcrumbs_by_module) {
        foreach ($c_breadcrumbs_by_module as $c_breadcrumbs) {
          if (isset(static::$cache[$c_breadcrumbs->id])) console::log_insert_about_duplicate('breadcrumbs', $c_breadcrumbs->id, $c_module_id);
          static::$cache[$c_breadcrumbs->id] = $c_breadcrumbs;
          static::$cache[$c_breadcrumbs->id]->module_id = $c_module_id;
          static::$cache[$c_breadcrumbs->id]->type = 'nosql';
        }
      }
    }
  }

  static function select($id) {
    static::init();
    return static::$cache[$id] ?? null;
  }

}}