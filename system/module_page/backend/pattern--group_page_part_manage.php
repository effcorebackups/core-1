<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class group_page_part_manage extends fieldset {

  public $tag_name = 'x-page_part-manage';
  public $content_tag_name = null;
  public $id_area;
  public $id_preset;

  function build() {
    if (!$this->is_builded) {
      parent::build();
      $preset = page_part_preset::select($this->id_preset);
      $button_delete = new button;
      $button_delete->title = '';
      $button_delete->build();
      $button_delete->value_set('button_delete_'.$preset->id.'_in_'.$this->id_area);
      $this->child_insert($button_delete, 'button');
      $this->child_insert(new markup('x-title', [], $preset->managing_title), 'title');
      $this->child_insert(new markup('x-id',    [], $preset->id            ), 'id'   );
      $this->is_builded = true;
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static function submit(&$group, $form, $npath) {
    $button = $group->child_select('button');
    if ($button->is_clicked()) {
      return (object)[
        'id_area'   => $group->id_area,
        'id_preset' => $group->id_preset];
    }
  }

}}