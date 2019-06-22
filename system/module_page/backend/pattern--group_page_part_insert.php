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
      $c_select_preset->name_set('page_part_for_'.$this->in_area);
      $c_select_preset->required_set(false);
      $c_select_preset->option_insert('- no -', 'not_selected');
      foreach ($presets as $c_preset)
        $c_select_preset->option_insert(
          $c_preset->managing_title,
          $c_preset->id);
      $c_button_add = new button;
      $c_button_add->title = '';
      $c_button_add->build();
      $c_button_add->value_set('button_add_for_'.$this->in_area);
      $this->child_insert($c_select_preset, 'select');
      $this->child_insert($c_button_add,    'button');
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
      return $select->value_get();
    }
  }

}}