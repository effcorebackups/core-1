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
            $previous_group_name  =  $c_color->group;
        if (!$this->child_select($c_color->id)) {
          $c_color_id        = $c_color->id;
          $c_color_value_hex = $c_color->value_hex ?: '#ffffff';
          $c_color_value     = $c_color->value_hex ?: 'transparent';
          $c_element_attributes = [
            'value' => $c_color_id,
            'title' => (new text('color ID = "%%_id" and value = "%%_value"', ['id' => $c_color_id, 'value' => $c_color_value]))->render(),
            'style' => ['background-color: '.$c_color_value_hex]];
          $c_field                     = new $this->field_class;
          $c_field->tag_name           = $this->field_tag_name;
          $c_field->title_tag_name     = $this->field_title_tag_name;
          $c_field->title_position     = $this->field_title_position;
          $c_field->title              = null;
          $c_field->description        = null;
          $c_field->element_attributes = $c_element_attributes + $this->attributes_select('element_attributes') + $c_field->attributes_select('element_attributes');
          $c_field->weight             = 0;
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
    $color_id        = $this->value_get() ?: 'white';
    $color_value_hex = color::get($color_id)->value_hex ?: '#ffffff';
    return (new markup_simple('input', [
      'type'             => 'checkbox',
      'role'             => 'button',
      'data-opener-type' => 'palette',
      'id'               => 'f_opener_'.$this->name_get_complex(),
      'title'            => new text('press to show or hide available colors'),
      'checked'          => $this->has_error_in_container() ? false : true,
      'value'            => $color_id,
      'style'            => ['background: '.$color_value_hex]
    ]))->render();
  }

}}