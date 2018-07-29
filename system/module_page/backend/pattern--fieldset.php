<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class fieldset extends container {

  public $tag_name = 'fieldset';
  public $title_tag_name = 'legend';

  function __construct($title = null, $description = null, $attributes = [], $children = [], $weight = 0) {
    if ($title)       $this->title       = $title;
    if ($description) $this->description = $description;
    parent::__construct($this->tag_name, $this->title, $description, $attributes, $children, $weight);
  }

}}