<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class group_palette extends group_radiobuttons {

  public $title_tag_name = 'label';
  public $content_tag_name = 'x-group-content';
  public $attributes = ['data-type' => 'palette'];

  function build() {
  # parent::build() not required
    foreach (storage::get('files')->select('colors') as $c_colors) {
      foreach ($c_colors as $c_row_id => $c_color) {
        $this->field_insert(null, [
          'value' => $c_color->id,
          'title' => translation::get('Color ID = %%_id (value = %%_value)', ['id' => $c_color->id, 'value' => $c_color->value]),
          'style' => ['background: '.$c_color->value]
        ], $c_color->id);
      }
    }
  }

  function render_self() {
    if ($this->title) {
      return $this->render_opener().(new markup($this->title_tag_name, ['for' => 'f_opener_'.$this->name_first_get()], [$this->title]))->render();
    }
  }

  function render_opener() {
    $color_id    = $this->value_get();
    $color_value = $this->color_value_get($color_id);
    return (new markup_simple('input', [
      'type' => 'checkbox',
      'data-opener-type' => 'palette',
      'title' => translation::get('Select color'),
      'id' => 'f_opener_'.$this->name_first_get(),
      'style' => ['background: '.$color_value],
      'checked' => true
    ]))->render();
  }

  function color_value_get($color_id) {
    foreach (storage::get('files')->select('colors') as $c_colors) {
      foreach ($c_colors as $c_row_id => $c_color) {
        if ($c_color->id == $color_id) {
          return $c_color->value;
        }
      }
    }
  }

}}