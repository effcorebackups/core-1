<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
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
    else                                         return parent::child_insert(         $child,  $id);
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

  static function description_prepare($data) {
    $result = [];
    if (gettype($data) === 'NULL'   ) $result = [                                                             ];
    if (gettype($data) === 'string' ) $result = ['default' => new markup('p', ['data-id' => 'default'], $data)];
    if (gettype($data) === 'integer') $result = ['default' => new markup('p', ['data-id' => 'default'], $data)];
    if (gettype($data) === 'double' ) $result = ['default' => new markup('p', ['data-id' => 'default'], $data)];
    if (gettype($data) === 'object' ) $result = ['default' => new markup('p', ['data-id' => 'default'], $data)]; # ready for: object:text, object:text_multiline, object->render()
    if (gettype($data) === 'array'  ) $result = $data;
    return $result;
  }

}}