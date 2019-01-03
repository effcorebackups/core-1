<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class tree_item extends node {

  public $template = 'tree_item';
  public $template_children = 'tree_item_children';
  public $id;
  public $id_parent;
  public $title = '';
  public $url;
  public $shadow_url;
  public $access;

  function __construct($title = '', $id = null, $id_parent = null, $url = null, $attributes = [], $weight = 0) {
    if ($id)        $this->id        = $id;
    if ($id_parent) $this->id_parent = $id_parent;
    if ($title)     $this->title     = $title;
    if ($url)       $this->url       = $url;
    parent::__construct($attributes, [], $weight);
  }

  function build() {
    foreach (tree::items_select() as $c_item) {
      if ($c_item->id_parent == $this->id) {
        $this->child_insert($c_item, $c_item->id);
        $c_item->build();
      }
    }
  }

  function render() {
    if ($this->access === null || access::check($this->access)) {
      $rendered_children = $this->children_count() ? (template::make_new($this->template_children, [
        'children' => $this->render_children($this->children_select())]
      ))->render() : '';
      return (template::make_new($this->template, [
        'self'     => $this->render_self(),
        'children' => $rendered_children
      ]))->render();
    }
  }

  function render_self() {
    $href        = token::replace($this->url);
    $href_shadow = token::replace($this->shadow_url); 
    if ($href        && url::is_active      ($href, 'path')) $this->attribute_insert('class', ['active'       => 'active'      ]);
    if ($href        && url::is_active_trail($href        )) $this->attribute_insert('class', ['active-trail' => 'active-trail']);
    if ($href_shadow && url::is_active_trail($href_shadow )) $this->attribute_insert('class', ['active-trail' => 'active-trail']);
    if ($href) $this->attribute_insert('href', $href);
    return (new markup('a', $this->attributes_select(),
      token::replace(translation::get($this->title))
    ))->render();
  }

}}