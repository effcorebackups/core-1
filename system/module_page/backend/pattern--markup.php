<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
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
    if ($this->template) {
      return (template::make_new($this->template, [
        'tag_name'   => $this->tag_name,
        'attributes' => $this->render_attributes(),
        'self'       => $this->render_self(), # note: not used in the self template
        'children'   => $this->render_children($this->children_select(true))
      ]))->render();
    } else {
      return $this->render_self().
             $this->render_children($this->children_select(true));
    }
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