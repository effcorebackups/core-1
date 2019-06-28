<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class group_page_part_manage extends fieldset {

  public $tag_name = 'x-page_part-manage';
  public $content_tag_name = null;
  public $id_preset;

  function build() {
    if (!$this->is_builded) {
      parent::build();
      $preset = page_part_preset::select($this->id_preset);
      $this->child_insert(new markup('x-title', [], $preset->managing_title), 'title');
      $this->child_insert(new markup('x-id',    [], $preset->id            ), 'id'   );
      $this->is_builded = true;
    }
  }

}}