<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class tabs_item extends node {

  public $id;
  public $id_parent;
  public $title = '';
  public $action_name;
  public $action_default_name;
  public $template = 'tabs_item';
  public $template_children = 'tabs_item_children';

  function __construct($title = '', $id = null, $id_parent = null, $action_name = null, $action_default_name = null, $attributes = [], $weight = 0) {
    if ($id)                  $this->id                  = $id;
    if ($id_parent)           $this->id_parent           = $id_parent;
    if ($title)               $this->title               = $title;
    if ($action_name)         $this->action_name         = $action_name;
    if ($action_default_name) $this->action_default_name = $action_default_name;
    parent::__construct($attributes, [], $weight);
  }

  function render() {
    if (!isset($this->access) ||
        (isset($this->access) && access::check($this->access))) {
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
    $href         = page::current_get()->args_get('base').'/'.($this->action_name);
    $href_default = page::current_get()->args_get('base').'/'.($this->action_default_name ?: $this->action_name);
    $this->attribute_insert('href', $href_default);
    if (url::is_active_trail($href)) {
      $this->attribute_insert('class', ['active' => 'active']);
    }
    return (new markup('a', $this->attributes_select(),
      token::replace(translation::get($this->title))
    ))->render();
  }

}}