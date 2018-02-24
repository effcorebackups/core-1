<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effectivecore {
          class markup extends \effectivecore\node {

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
    return (new template($this->template, [
      'tag_name'   => $this->tag_name,
      'attributes' => factory::data_to_attr($this->attribute_select()),
      'content'    => $this->render_children($this->children)
    ]))->render();
  }

}}