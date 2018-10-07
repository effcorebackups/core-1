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
  public $access;

  function __construct($title = '', $id = null, $id_parent = null, $url = null, $attributes = [], $weight = 0) {
    if ($id)        $this->id        = $id;
    if ($id_parent) $this->id_parent = $id_parent;
    if ($title)     $this->title     = $title;
    if ($url)       $this->url       = $url;
    parent::__construct($attributes, [], $weight);
  }

  function render() {
    if ($this->access === null || access::check($this->access)) {
      $rendered_children = $this->children_count() ? (new template($this->template_children, [
        'children' => $this->render_children($this->children_select())]
      ))->render() : '';
      return (new template($this->template, [
        'self'     => $this->render_self(),
        'children' => $rendered_children
      ]))->render();
    }
  }

  function render_self() {
    if ($this->url) {
      if (url::is_active      ($this->url)) $this->attribute_insert('class', ['active'       => 'active']);
      if (url::is_active_trail($this->url)) $this->attribute_insert('class', ['active-trail' => 'active-trail']);
      $this->attribute_insert('href', token::replace($this->url));
    }
    return (new markup('a', $this->attributes_select(),
      token::replace(translation::get($this->title))
    ))->render();
  }

}}