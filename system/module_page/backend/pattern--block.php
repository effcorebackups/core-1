<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class block extends markup {

  public $tag_name = 'x-block';
  public $template = 'block';
# ─────────────────────────────────────────────────────────────────────
  public $title;
  public $title_tag_name = 'x-title';
  public $content_tag_name = 'x-content';

  function __construct($title = null, $attributes = [], $children = [], $weight = 0) {
    if ($title) $this->title = $title;
    parent::__construct(null, $attributes, $children, $weight);
  }

  function render() {
    return (new template($this->template, [
      'tag_name'   => $this->tag_name,
      'attributes' => core::data_to_attr($this->attributes_select()),
      'title'      => $this->render_self(),
      'content'    => $this->content_tag_name ? (new markup($this->content_tag_name, [],
                      $this->render_children($this->children_select()) ))->render() :
                      $this->render_children($this->children_select())
    ]))->render();
  }

  function render_self() {
    if ($this->title) {
      return (new markup($this->title_tag_name, [], [
        $this->title
      ]))->render();
    }
  }

}}