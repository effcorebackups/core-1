<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class group_page_part_insert extends fieldset {

  public $tag_name = 'x-page_part-insert';
  public $content_tag_name = null;
  public $id_area;

  function build() {
    if (!$this->is_builded) {
      parent::build();
      $presets = page_part_preset::select_all($this->id_area);
      $select_preset = new field_select;
      $select_preset->title = 'Insert part';
      $select_preset->build();
      $select_preset->name_set('insert_to_'.$this->id_area);
      $select_preset->required_set(false);
      $select_preset->option_insert('- no -', 'not_selected');
      foreach ($presets as $c_preset) {
        if (!$select_preset->optgroup_select(core::sanitize_id($c_preset->managing_group)))
             $select_preset->optgroup_insert(core::sanitize_id($c_preset->managing_group), $c_preset->managing_group);
        $select_preset->option_insert(
          $c_preset->managing_title,
          $c_preset->id, [], core::sanitize_id(
          $c_preset->managing_group));}
      $button_insert = new button;
      $button_insert->title = '';
      $button_insert->build();
      $button_insert->value_set('button_insert_to_'.$this->id_area);
      $this->child_insert($select_preset, 'select');
      $this->child_insert($button_insert, 'button');
      $this->is_builded = true;
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static function validate($group, $form, $npath) {
    $select = $group->child_select('select');
    $button = $group->child_select('button');
    if ($button->is_clicked() && !$select->value_get()) {
      $select->error_set(
        'Field "%%_title" must be selected!', ['title' => translation::get($select->title)]
      );
    } else {
      return true;
    }
  }

  static function submit(&$group, $form, $npath) {
    $select = $group->child_select('select');
    $button = $group->child_select('button');
    if ($button->is_clicked() && $select->value_get()) {
      return (object)[
        'id_area'   => $group->id_area,
        'id_preset' => $select->value_get()];
    }
  }

}}