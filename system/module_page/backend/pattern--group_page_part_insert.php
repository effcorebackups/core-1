<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class group_page_part_insert extends fieldset {

  public $tag_name = 'x-page_part-insert';
  public $content_tag_name = null;
  public $in_area;

  function build() {
    if (!$this->is_builded) {
      parent::build();
      $presets = page_part_preset::select_all($this->in_area);
      $c_select_preset = new field_select;
      $c_select_preset->title = 'Insert part';
      $c_select_preset->build();
      $c_select_preset->name_set('insert_to_'.$this->in_area);
      $c_select_preset->required_set(false);
      $c_select_preset->option_insert('- no -', 'not_selected');
      foreach ($presets as $c_preset)
        $c_select_preset->option_insert(
          $c_preset->managing_title,
          $c_preset->id);
      $c_button_insert = new button;
      $c_button_insert->title = '';
      $c_button_insert->build();
      $c_button_insert->value_set('button_insert_to_'.$this->in_area);
      $this->child_insert($c_select_preset, 'select');
      $this->child_insert($c_button_insert, 'button');
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
    }
  }

  static function submit(&$group, $form, $npath) {
    $select = $group->child_select('select');
    $button = $group->child_select('button');
    if ($button->is_clicked() && $select->value_get()) {
      return                     $select->value_get();
    }
  }

}}