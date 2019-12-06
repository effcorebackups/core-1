<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_page_part_manage extends container {

  public $tag_name = 'x-widget';
  public $attributes = ['data-type' => 'page_part-manage'];
  public $id_area;
  public $id_preset;

  function build() {
    if (!$this->is_builded) {
      $preset = page_part_preset::select($this->id_preset);
      $button_delete = new button('', ['data-style' => 'narrow-delete', 'title' => new text('Delete')]);
      $button_delete->build();
      $button_delete->value_set('button_delete_'.$this->id_preset.'_in_'.$this->id_area);
      $this->child_insert($button_delete, 'button_delete');
      $this->child_insert(new markup('x-title', [], $preset ? [$preset->managing_group, ': ', $preset->managing_title] : 'LOST PART'), 'title');
      $this->child_insert(new markup('x-id',    [], new text_simple($this->id_preset)     ), 'id'   );
      $this->is_builded = true;
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static function submit(&$group, $form, $npath) {
    $button_delete = $group->child_select('button_delete');
    if ($button_delete->is_clicked()) {
      $parts = $form->validation_cache_get('parts');
      unset($parts[$group->id_area][$group->id_preset]);
      if   ($parts[$group->id_area] == [])
      unset($parts[$group->id_area]);
      $form->validation_cache_is_persistent = true;
      $form->validation_cache_set('parts', $parts);
      message::insert(new text('Part of the page with id = "%%_id_page_part" was deleted from the area with id = "%%_id_area".', ['id_page_part' => $group->id_preset, 'id_area' => $group->id_area]));
      message::insert(new text('Click the button "%%_name" to save your changes!', ['name' => translation::get('update')]), 'warning');
      return true;
    }
  }

}}