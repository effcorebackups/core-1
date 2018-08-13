<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class markup extends node {

  public $tag_name = 'div';
  public $template = 'markup_html';

  function __construct($tag_name = null, $attributes = [], $children = [], $weight = 0) {
    if ($tag_name) $this->tag_name = $tag_name;
    parent::__construct($attributes, $children, $weight);
  }

  function child_insert($child, $id = null) {
    if (is_string($child) || is_numeric($child)) return parent::child_insert(new text($child), $id);
    else                                         return parent::child_insert($child, $id);
  }

  function render() {
    if ($this->tag_name == 'a'          &&
        $this->attribute_select('href') && url::is_active(
        $this->attribute_select('href')))
        $this->attribute_insert('class', ['active' => 'active']);
    return (new template($this->template, [
      'tag_name'   => $this->tag_name,
      'attributes' => core::data_to_attr($this->attributes_select()),
      'content'    => $this->render_children($this->children_select())
    ]))->render();
  }

  function render_description() {
    if ($this->description) {
      return (
        new markup($this->description_tag_name, [],
          new markup('p', [], $this->description)
        )
      )->render();
    }
  }

}}