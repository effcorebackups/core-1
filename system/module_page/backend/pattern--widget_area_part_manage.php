<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_area_part_manage extends container {

  public $tag_name = 'x-widget';
  public $attributes = ['data-type' => 'area_part-manage', 'data-rearrangeable' => 'true', 'data-fields-is-inline-full' => 'true'];
  public $on_click_delete_handler;
  public $id_area;
  public $id_preset;

  function __construct($id_area, $id_preset, $attributes = [], $weight = 0) {
    $this->id_area   = $id_area;
    $this->id_preset = $id_preset;
    parent::__construct(null, null, null, $attributes, [], $weight);
  }

  function build() {
    if (!$this->is_builded) {
      $preset = page_part_preset::select($this->id_preset);
      $field_weight = new field_weight();
      $field_weight->description_state = 'hidden';
      $field_weight->build();
      $field_weight->name_set('weight_'.$this->id_preset.'_'.$this->id_area);
      $field_weight->required_set(false);
      $field_weight->value_set($this->weight);
      $button_delete = new button('', ['data-style' => 'narrow-delete', 'title' => new text('Delete')]);
      $button_delete->build();
      $button_delete->value_set('button_delete_'.$this->id_preset.'_'.$this->id_area);
      $this->child_insert($field_weight,  'field_weight' );
      $this->child_insert($button_delete, 'button_delete');
      $this->child_insert(new markup('x-info', [], [
        'title' => new markup('x-title', [], $preset ? [$preset->managing_group, ': ', $preset->managing_title] : 'LOST PART'),
        'id'    => new markup('x-id',    [], new text_simple($this->id_preset)) ]), 'info');
      $this->is_builded = true;
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static function on_submit(&$group, $form, $npath) {
    $button_delete = $group->child_select('button_delete');
    if ($button_delete->is_clicked()) {
      if ($group->on_click_delete_handler) {
        return call_user_func($group->on_click_delete_handler, $group, $form, $npath);
      }
    }
  }

}}