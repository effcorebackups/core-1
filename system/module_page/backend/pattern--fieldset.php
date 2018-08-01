<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class fieldset extends markup {

  public $tag_name = 'fieldset';
  public $template = 'fieldset';
# ─────────────────────────────────────────────────────────────────────
  public $title;
  public $title_tag_name = 'legend';
  public $content_wrapper_tag_name = 'x-content';
  public $description;
  public $description_tag_name = 'x-description';
  public $state = ''; # opened|closed

  function __construct($title = null, $description = null, $attributes = [], $children = [], $weight = 0) {
    if ($title)       $this->title       = $title;
    if ($description) $this->description = $description;
    parent::__construct(null, $attributes, $children, $weight);
  }

  function render() {
    $content = $this->content_wrapper_tag_name ? (new markup($this->content_wrapper_tag_name, [],
      $this->render_children($this->children_select())))->render() : 
      $this->render_children($this->children_select());
    return (new template($this->template, [
      'tag_name'    => $this->tag_name,
      'attributes'  => core::data_to_attr($this->attributes_select()),
      'title'       => $this->render_self(),
      'description' => $this->render_description(),
      'content'     => $content
    ]))->render();
  }

  function render_self() {
    if ($this->title) {
      $opener = $this->state == 'opened' ? (new markup_simple('input', ['type' => 'checkbox', 'data-opener-type' => 'fieldset', 'checked' => 'checked']))->render() : (
                $this->state == 'closed' ? (new markup_simple('input', ['type' => 'checkbox', 'data-opener-type' => 'fieldset'                        ]))->render() : '');
      return $opener.(new markup($this->title_tag_name, [], [
        $this->title
      ]))->render();
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