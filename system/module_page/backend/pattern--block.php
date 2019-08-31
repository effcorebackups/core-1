<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class block extends markup {

  public $tag_name = 'x-block';
  public $template = 'block';
# ─────────────────────────────────────────────────────────────────────
  public $title;
  public $title_tag_name = 'x-block-title';
  public $content_tag_name = 'x-block-content';

  function __construct($title = null, $attributes = [], $children = [], $weight = 0) {
    if ($title) $this->title = $title;
    parent::__construct(null, $attributes, $children, $weight);
  }

  function render() {
    return (template::make_new($this->template, [
      'tag_name'   => $this->tag_name,
      'attributes' => $this->render_attributes(),
      'self'       => $this->render_self(),
      'children'   => $this->content_tag_name ? (new markup($this->content_tag_name, [],
                      $this->render_children($this->children_select(true)) ))->render() :
                      $this->render_children($this->children_select(true))
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