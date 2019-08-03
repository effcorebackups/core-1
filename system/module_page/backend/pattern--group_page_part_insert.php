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
      core::array_sort_by_text_property($presets, 'managing_title');
      $options = ['not_selected' => '- no -'];
      foreach ($presets as $c_preset) {
        $c_group_id = core::sanitize_id($c_preset->managing_group);
        if (!isset($options[$c_group_id])) {
                   $options[$c_group_id] = new \stdClass;
                   $options[$c_group_id]->title = $c_preset->managing_group;}
        $options[$c_group_id]->values[$c_preset->id] = $c_preset->managing_title;
      }
      $select_preset = new field_select;
      $select_preset->title = 'Insert part';
      $select_preset->values = $options;
      $select_preset->build();
      $select_preset->name_set('insert_to_'.$this->id_area);
      $select_preset->required_set(false);
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