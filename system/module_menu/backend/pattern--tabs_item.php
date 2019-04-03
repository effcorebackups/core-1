<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class tabs_item extends node {

  public $template = 'tabs_item';
  public $template_children = 'tabs_item_children';
  public $attributes = [];
  public $link_attributes = ['role' => 'tab'];
  public $id;
  public $id_parent;
  public $title = '';
  public $action_name;
  public $action_name_default;
  public $hidden = false;
  public $access;

  function __construct($title = '', $id = null, $id_parent = null, $action_name = null, $action_name_default = null, $attributes = [], $link_attributes = [], $hidden = false, $weight = 0) {
    if ($id)                  $this->id                  = $id;
    if ($id_parent)           $this->id_parent           = $id_parent;
    if ($title)               $this->title               = $title;
    if ($action_name)         $this->action_name         = $action_name;
    if ($action_name_default) $this->action_name_default = $action_name_default;
    if ($link_attributes)     $this->link_attributes     = $link_attributes;
    if ($hidden)              $this->hidden              = $hidden;
    parent::__construct($attributes, [], $weight);
  }

  function build() {
    foreach (tabs::items_select() as $c_item) {
      if ($c_item->id_parent == $this->id) {
        $this->child_insert($c_item, $c_item->id);
        $c_item->build();
      }
    }
  }

  function render() {
    if (empty($this->hidden)) {
      if ($this->access === null || access::check($this->access)) {
        $rendered_children = $this->children_count() ? (template::make_new($this->template_children, [
          'children' => $this->render_children($this->children_select())
        ]))->render() : '';
        return (template::make_new($this->template, [
          'attributes' => $this->render_attributes(),
          'self'       => $this->render_self(),
          'children'   => $rendered_children
        ]))->render();
      }
    }
  }

  function render_self() {
    $href         = rtrim(page::current_get()->args_get('base').'/'.($this->action_name         ?: $this->action_name), '/');
    $href_default = rtrim(page::current_get()->args_get('base').'/'.($this->action_name_default ?: $this->action_name), '/');
    if ($href && url::is_active      ($href, 'path')) {$this->attribute_insert('aria-selected', 'true', 'link_attributes');}
    if ($href && url::is_active_trail($href))         {$this->attribute_insert('aria-selected', 'true', 'link_attributes'); $this->attribute_insert('class', ['active-trail' => 'active-trail'], 'link_attributes');}
    if ($href_default) $this->attribute_insert('href', $href_default, 'link_attributes');
    $this->attribute_insert('title', translation::get('Click to open the tab: %%_title', ['title' => translation::get($this->title)]), 'link_attributes');
    return (new markup('a', $this->attributes_select('link_attributes'),
      new text($this->title, [], true, true)
    ))->render();
  }

}}