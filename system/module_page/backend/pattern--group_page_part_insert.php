<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
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
        $options[$c_group_id]->values[$c_preset->id] = new text_multiline([
          'title' => $c_preset->managing_title, 'id' => '('.$c_preset->id.')'], [], ' '
        );
      }
      $select = new field_select('Insert part');
      $select->values = $options;
      $select->build();
      $select->name_set('part_insert_to_'.$this->id_area);
      $select->required_set(false);
      $button = new button('', ['data-style' => 'narrow-insert']);
      $button->build();
      $button->value_set('button_part_insert_to_'.$this->id_area);
      $this->child_insert($select, 'select');
      $this->child_insert($button, 'button');
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
      $id_preset = $select->value_get();
      $preset = page_part_preset::select($id_preset);
      $parts = $form->validation_cache_get('parts');
      if ($preset->origin == 'dynamic')
           $parts[$group->id_area][$preset->id] = new page_part_preset_link($preset->id, $preset->extra);
      else $parts[$group->id_area][$preset->id] = new page_part_preset_link($preset->id                );
      $form->validation_cache_is_persistent = true;
      $form->validation_cache_set('parts', $parts);
      message::insert(new text('Part of the page with id = "%%_id_page_part" was inserted to the area with id = "%%_id_area".', ['id_page_part' => $preset->id, 'id_area' => $group->id_area]));
      message::insert(new text('Click the button "%%_name" to save your changes!', ['name' => translation::get('update')]), 'warning');
      return true;
    }
  }

}}