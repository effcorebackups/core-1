<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class markup_simple extends node_simple {

  public $tag_name = 'input';
  public $template = 'markup_html_simple';

  function __construct($tag_name = null, $attributes = [], $weight = 0) {
    if ($tag_name) $this->tag_name = $tag_name;
    parent::__construct($attributes, $weight);
  }

  function render() {
    return (template::make_new($this->template, [
      'tag_name'   => $this->tag_name,
      'attributes' => $this->render_attributes()
    ]))->render();
  }

}}