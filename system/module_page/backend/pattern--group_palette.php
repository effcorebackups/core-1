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
    $c_new_color_group = null;
    $c_old_color_group = null;
    foreach (color::all_get() as $c_color) {
      $c_attributes = [
        'value' => $c_color->id,
        'title' => translation::get('Color ID = %%_id (value = %%_value)', ['id' => $c_color->id, 'value' => $c_color->value]),
        'style' => ['background: '.$c_color->value]
      ];
          $c_new_color_group  = $c_color->group ?? null;
      if ($c_new_color_group != $c_old_color_group) $this->child_insert(hr);
          $c_old_color_group  = $c_new_color_group;
      $this->field_insert(null, $c_attributes, $c_color->id);
    }
  }

  function render_self() {
    if ($this->title) {
      return $this->render_opener().(new markup($this->title_tag_name, ['for' => 'f_opener_'.$this->name_first_get()], [$this->title]))->render();
    }
  }

  function render_opener() {
    $color_id    = $this->value_get();
    $color_value = color::get($color_id ?: 'white')->value;
    return (new markup_simple('input', [
      'type' => 'checkbox',
      'data-opener-type' => 'palette',
      'title' => translation::get('Select color'),
      'id' => 'f_opener_'.$this->name_first_get(),
      'value' => $color_value,
      'style' => ['background: '.$color_value],
      'checked' => true
    ]))->render();
  }

}}