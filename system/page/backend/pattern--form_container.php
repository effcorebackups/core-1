<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class form_container extends \effcore\markup {

  public $tag_name = 'x-container';
  public $template = 'form_container';

  public $title = null;
  public $title_tag_name = 'x-title';
  public $title_position = 'top';
  public $description = null;
  public $description_tag_name = 'x-description';

  function __construct($tag_name = null, $title = null, $description = null, $attributes = [], $children = [], $weight = 0) {
    if ($tag_name)    $this->tag_name    = $tag_name;
    if ($title)       $this->title       = $title;
    if ($description) $this->description = $description;
    parent::__construct($tag_name, $attributes, $children, $weight);
  }

  function render() {
    $is_bottom_title = !empty($this->title_position) &&
                              $this->title_position == 'bottom';
    return (new template($this->template, [
      'tag_name'    => $this->tag_name,
      'attributes'  => factory::data_to_attr($this->attribute_select()),
      'content'     => $this->render_children($this->children),
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
      ))->render();
    }
  }

}}