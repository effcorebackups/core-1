<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class tabs_item extends \effcore\tree_item {

  public $id;
  public $id_parent;
  public $parent_is_tab;
  public $action_name;
  public $title = '';
  public $template = 'tabs_item';
  public $template_children = 'tabs_item_children';

  function render_self() {
    $page = page::get_current();
    $this->attribute_insert('href', $page->args_get('base').'/'.$this->action_name);
    return parent::render_self();
  }

}}