<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class page_part_preset extends page_part {

  public $id;
  public $managing_title;
  public $in_areas;

  function markup_get($page = null) {
    return parent::markup_get($page);
  }

}}