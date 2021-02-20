<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class group_palette extends group_radiobuttons {

  public $title_tag_name = 'label';
  public $content_tag_name = 'x-group-content';
  public $required_any = true;
  public $attributes = [
    'data-type' => 'palette',
    'role'      => 'group'
  ];

  function build() {
    if (!$this->is_builded) {
      $previous_group_name = '';
      foreach (color::get_all() as $c_color) {
        if ($previous_group_name !== '' &&
            $previous_group_name !== $c_color->group) $this->child_insert(hr);
            $previous_group_name   = $c_color->group;
        if (!$this->child_select($c_color->id)) {
          $c_title = (new text('color ID = "%%_id" and value = "%%_value"', ['id' => $c_color->id, 'value' => $c_color->value]))->render();
          $c_info = (object)[
            'title'              => null,
            'description'        => null,
            'element_attributes' => ['value' => $c_color->id, 'title' => $c_title, 'style' => ['background-color: '.$c_color->value]],
            'weight'             => 0];
          $c_field                     = new $this->field_class;
          $c_field->tag_name           = $this->field_tag_name;
          $c_field->title_tag_name     = $this->field_title_tag_name;
          $c_field->title_position     = $this->field_title_position;
          $c_field->title              = $c_info->title;
          $c_field->description        = $c_info->description;
          $c_field->element_attributes = $c_info->element_attributes + $this->attributes_select('element_attributes') + $c_field->attributes_select('element_attributes');
          $c_field->weight             = $c_info->weight;
          $c_field->build();
          $c_field->required_set(isset($this->required[$c_color->id]));
          $c_field-> checked_set(isset($this->checked [$c_color->id]));
          $c_field->disabled_set(isset($this->disabled[$c_color->id]));
          $this->child_insert($c_field, $c_color->id);
        }
      }
      $this->is_builded = true;
    }
  }

  function render_self() {
    if ($this->title && (bool)$this->title_is_visible !== true) return $this->render_opener().(new markup($this->title_tag_name, $this->title_attributes + ['for' => 'f_opener_'.$this->name_get_complex(), 'aria-hidden' => 'true'], $this->title))->render();
    if ($this->title && (bool)$this->title_is_visible === true) return $this->render_opener().(new markup($this->title_tag_name, $this->title_attributes + ['for' => 'f_opener_'.$this->name_get_complex()                         ], $this->title))->render();
  }

  function render_opener() {
    $color_id    = $this->value_get();
    $color_value = color::get($color_id ?: 'white')->value;
    return (new markup_simple('input', [
      'type'             => 'checkbox',
      'role'             => 'button',
      'data-opener-type' => 'palette',
      'title'            => new text('press to show or hide available colors'),
      'id'               => 'f_opener_'.$this->name_get_complex(),
      'value'            => $color_value,
      'checked'          => $this->has_error_in_container() ? false : true,
      'style'            => ['background: '.$color_value]
    ]))->render();
  }

}}