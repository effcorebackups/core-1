<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class container extends markup {

  public $tag_name = 'x-container';
  public $template = 'container';
# ─────────────────────────────────────────────────────────────────────
  public $title;
  public $title_tag_name = 'x-title';
  public $title_position = 'top';
  public $description;
  public $description_tag_name = 'x-description';
  public $description_position = 'bottom';
  public $content_wrapper_tag_name;

  function __construct($tag_name = null, $title = null, $description = null, $attributes = [], $children = [], $weight = 0) {
    if ($title)       $this->title       = $title;
    if ($description) $this->description = $description;
    parent::__construct($tag_name, $attributes, $children, $weight);
  }

  function render() {
    $is_bottom_title    = !empty($this->title_position)       && $this->title_position       == 'bottom';
    $is_top_description = !empty($this->description_position) && $this->description_position == 'top';
    return (new template($this->template, [
      'tag_name'      => $this->tag_name,
      'attributes'    => core::data_to_attr($this->attributes_select()),
      'title_t'       => $is_bottom_title    ? '' : $this->render_self(),
      'title_b'       => $is_bottom_title    ?      $this->render_self()        : '',
      'description_t' => $is_top_description ?      $this->render_description() : '',
      'description_b' => $is_top_description ? '' : $this->render_description(),
      'content'       => $this->content_wrapper_tag_name ? (new markup($this->content_wrapper_tag_name, [],
                         $this->render_children($this->children_select()) ))->render() :
                         $this->render_children($this->children_select())
    ]))->render();
  }

  function render_self() {
    if ($this->title) {
      $required_mark = $this->attribute_select('required') ? $this->render_required_mark() : '';
      return (new markup($this->title_tag_name, [], [
        $this->title, $required_mark
      ]))->render();
    }
  }

  function render_required_mark() {
    return (new markup('b', ['class' => ['required' => 'required']], '*'))->render();
  }

}}