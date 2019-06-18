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
      $page_parts = page_part::select_all($this->id_area);
      $c_field_page_part = new field_select;
      $c_field_page_part->title = 'Insert part';
      $c_field_page_part->build();
      $c_field_page_part->name_set('page_part_for_'.$this->id_area);
      $c_field_page_part->required_set(false);
      $c_field_page_part->option_insert('- no -', 'not_selected');
      foreach ($page_parts as $c_row_id => $c_part)
        $c_field_page_part->option_insert($c_part->managing_title, $c_row_id);
      $c_button_add = new button;
      $c_button_add->title = '';
      $c_button_add->build();
      $c_button_add->value_set('button_add_for_'.$this->id_area);
      $this->child_insert($c_field_page_part, 'select');
      $this->child_insert($c_button_add,      'button');
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
      $id_part = $select->value_get(  );
                 $select->value_set('');
      return $id_part;
    }
  }

}}