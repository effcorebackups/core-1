<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class container extends markup {

  public $tag_name = 'x-container';
  public $template = 'container';

  public $title;
  public $title_tag_name = 'x-title';
  public $title_position = 'top';
  public $description;
  public $description_tag_name = 'x-description';

  function __construct($tag_name = null, $title = null, $description = null, $attributes = [], $children = [], $weight = 0) {
    if ($title)       $this->title       = $title;
    if ($description) $this->description = $description;
    parent::__construct($tag_name, $attributes, $children, $weight);
  }

  function render() {
    $is_bottom_title = !empty($this->title_position) &&
                              $this->title_position == 'bottom';
    return (new template($this->template, [
      'tag_name'    => $this->tag_name,
      'attributes'  => factory::data_to_attr($this->attribute_select_all()),
      'content'     => $this->render_children($this->child_select_all()),
      'description' => $this->render_description(),
      'title_t'     => $is_bottom_title ? '' : $this->render_self(),
      'title_b'     => $is_bottom_title ?      $this->render_self() : ''
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