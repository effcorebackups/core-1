<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_area_part_insert extends container {

  public $tag_name = 'x-widget';
  public $attributes = ['data-type' => 'area_part-insert'];
  public $id_area;

  function __construct($id_area, $attributes = [], $weight = 0) {
    $this->id_area = $id_area;
    parent::__construct(null, null, null, $attributes, [], $weight);
  }

  function build() {
    if (!$this->is_builded) {
      $presets = page_part_preset::select_all($this->id_area);
      core::array_sort_by_text_property($presets, 'managing_group');
      $options = ['not_selected' => '- no -'];
      foreach ($presets as $c_preset) {
        $c_group_id = core::sanitize_id($c_preset->managing_group);
        if (!isset($options[$c_group_id])) {
                   $options[$c_group_id] = new \stdClass;
                   $options[$c_group_id]->title = $c_preset->managing_group;}
        $options[$c_group_id]->values[$c_preset->id] = translation::get($c_preset->managing_title).' ('.$c_preset->id.')';
      }
      foreach ($options as $c_group) {
        if ($c_group instanceof \stdClass) {
          core::array_sort_text($c_group->values);
        }
      }
      $select = new field_select('Insert part');
      $select->values = $options;
      $select->build();
      $select->name_set('part_insert_to_'.$this->id_area);
      $select->required_set(false);
      $button = new button('', ['data-style' => 'narrow-insert', 'title' => new text('Insert')]);
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
      $parts[$group->id_area][$preset->id] = new page_part_preset_link($preset->id);
      $form->validation_cache_is_persistent = true;
      $form->validation_cache_set('parts', $parts);
      message::insert(new text('Part of the page with id = "%%_id_page_part" was inserted to the area with id = "%%_id_area".', ['id_page_part' => $preset->id, 'id_area' => $group->id_area]));
      message::insert(new text('Click the button "%%_name" to save your changes!', ['name' => translation::get('update')]), 'warning');
      return true;
    }
  }

}}