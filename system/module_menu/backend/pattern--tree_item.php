<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class tree_item extends node {

  public $template = 'tree_item';
  public $template_children = 'tree_item_children';
  public $element_attributes = ['role' => 'treeitem'];
  public $id;
  public $id_parent;
  public $id_tree;
  public $title = '';
  public $url;
  public $url_shadow;
  public $extra;
  public $access;
  public $is_nosql = true;

  function __construct($title = '', $id = null, $id_parent = null, $id_tree = null, $url = null, $access = null, $attributes = [], $element_attributes = [], $weight = 0) {
    if ($title             ) $this->title              = $title;
    if ($id                ) $this->id                 = $id;
    if ($id_parent         ) $this->id_parent          = $id_parent;
    if ($id_tree           ) $this->id_tree            = $id_tree;
    if ($url               ) $this->url                = $url;
    if ($access            ) $this->access             = $access;
    if ($element_attributes) $this->element_attributes = $element_attributes;
    parent::__construct($attributes, [], $weight);
  }

  function build() {
    $this->attribute_insert('data-id', $this->id);
    foreach (static::select_all() as $c_item) {
      if ($c_item->id_parent == $this->id) {
        $this->child_insert($c_item, $c_item->id);
        $c_item->build();
      }
    }
  }

  function render() {
    $managed_mode = tree::select($this->id_tree)->managed_mode;
    if ($this->access === null || access::check($this->access)) {
      $rendered_self     = $managed_mode ? $this->render_self_managed() : $this->render_self();
      $rendered_children = $managed_mode || $this->children_select_count() ? (template::make_new($this->template_children, [
        'children' => $this->render_children($this->children_select())]
      ))->render() : '';
      if ($managed_mode) {
        $rendered_self     =                    (new markup('x-drop_area',  ['data-type' => 'in'    ], ''))->render().$rendered_self;
        $rendered_self     =                    (new markup('x-drop_area',  ['data-type' => 'before'], ''))->render().$rendered_self;
        $rendered_children = $rendered_children.(new markup('x-drop_area',  ['data-type' => 'after' ], ''))->render();}
      return (template::make_new($this->template, [
        'attributes' => $this->render_attributes(),
        'self'       => $rendered_self,
        'children'   => $rendered_children
      ]))->render();
    }
  }

  function render_self() {
    $href        = token::replace($this->url       );
    $href_shadow = token::replace($this->url_shadow);
    if ($href        && url::is_active      ($href, 'path')) {$this->attribute_insert('aria-selected',       'true', 'element_attributes');}
    if ($href        && url::is_active_trail($href        )) {$this->attribute_insert('data-selected-trail', 'true', 'element_attributes');}
    if ($href_shadow && url::is_active_trail($href_shadow )) {$this->attribute_insert('data-selected-trail', 'true', 'element_attributes');}
    if ($href) $this->attribute_insert('href', $href, 'element_attributes');
    if ($href) $this->attribute_insert('title', new text('Click to open the menu item: %%_title', ['title' => translation::get($this->title)]), 'element_attributes');
    return (new markup('a', $this->attributes_select('element_attributes'),
      new text($this->title, [], true, true)
    ))->render();
  }

  function render_self_managed() {
    return (new markup('x-item', $this->attributes_select('element_attributes'), [
      new markup('x-item-title', [], $this->title),
      new markup('x-item-extra', [], $this->extra),
      new markup('x-item-url',   [], $this->url ? str_replace('/', (new markup('em', [], '/'))->render(), $this->url) : 'no url')
    ]))->render();
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    foreach (storage::get('files')->select('tree_items') as $c_module_id => $c_tree_items) {
      foreach ($c_tree_items as $c_row_id => $c_tree_item) {
        if (isset(static::$cache_tree_items[$c_tree_item->id])) console::log_insert_about_duplicate('tree_item', $c_tree_item->id, $c_module_id);
        static::$cache[$c_tree_item->id] = $c_tree_item;
        static::$cache[$c_tree_item->id]->module_id = $c_module_id;
      }
    }
  # load from storage
    foreach (entity::get('tree_item')->instances_select() as $c_instance) {
      $c_tree_item = new static(
        $c_instance->title,
        $c_instance->id,
        $c_instance->id_parent,
        $c_instance->id_tree,
        $c_instance->url, unserialize($c_instance->access), [], [],
        $c_instance->weight);
      static::$cache[$c_tree_item->id] = $c_tree_item;
      static::$cache[$c_tree_item->id]->module_id = 'menu';
      static::$cache[$c_tree_item->id]->is_nosql = false;
    }
  }

  static function select_all() {
    if    (static::$cache == null) static::init();
    return static::$cache ?? [];
  }

  static function select($id) {
    if    (static::$cache == null) static::init();
    return static::$cache[$id] ?? null;
  }

  static function insert($title, $id, $id_parent, $id_tree, $url = null, $access = null, $attributes = [], $element_attributes = [], $weight = 0, $module_id = null) {
    $new_item = new static($title, $id, $id_parent, $id_tree, $url, $access, $attributes, $element_attributes, $weight);
    if    (static::$cache == null) static::init();
           static::$cache[$id] = $new_item;
           static::$cache[$id]->module_id = $module_id;
           static::$cache[$id]->is_nosql = false;
    return static::$cache[$id];
  }

  static function delete($id) {
    if   (static::$cache == null) static::init();
    unset(static::$cache[$id]);
  }

}}