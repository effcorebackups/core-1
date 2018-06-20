<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class tabs_item extends tree_item {

  public $parent_is_tab;
  public $action_name;
  public $action_default_name;
  public $title = '';
  public $template = 'tabs_item';
  public $template_children = 'tabs_item_children';

  function render_self() {
    $href         = page::current_get()->args_get('base').'/'.($this->action_name);
    $href_default = page::current_get()->args_get('base').'/'.($this->action_default_name ?: $this->action_name);
    $this->attribute_insert('href', $href_default);
    if (url::is_active_trail($href)) {
      $this->attribute_insert('class', ['active' => 'active']);
    }
    return parent::render_self();
  }

}}