<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_area_part_insert extends container {

  public $tag_name = 'x-widget';
  public $attributes = ['data-type' => 'area_part-insert'];
  public $on_click_insert_handler;
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

  static function on_validate($group, $form, $npath) {
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

  static function on_submit(&$group, $form, $npath) {
    $select = $group->child_select('select');
    $button = $group->child_select('button');
    if ($button->is_clicked() && $select->value_get()) {
      if ($group->on_click_insert_handler) {
        return call_user_func($group->on_click_insert_handler, $group, $form, $npath, $select->value_get());
      }
    }
  }

}}