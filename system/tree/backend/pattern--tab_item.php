<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class tab_item extends \effcore\tree_item {

  public $id;
  public $id_parent;
  public $parent_is_tab;
  public $title = '';
  public $template = 'tab_item';
  public $template_children = 'tab_item_children';

}}