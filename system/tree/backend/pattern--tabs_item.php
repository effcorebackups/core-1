<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class tabs_item extends \effcore\tree_item {

  public $id;
  public $id_parent;
  public $parent_is_tab;
  public $title = '';
  public $template = 'tabs_item';
  public $template_children = 'tabs_item_children';

}}