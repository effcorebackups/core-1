<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_pictures extends widget_fields {

  public $title = 'Pictures';
  public $item_title = 'Picture';
  public $attributes = ['data-type' => 'fields-pictures'];
  public $name_complex = 'widget_pictures';

  # ─────────────────────────────────────────────────────────────────────

  function widget_insert_get() {
    $widget = new markup('x-widget', [
      'data-type' => 'insert']);
  # field for upload new picture
    $field_picture = new field_picture;
    $field_picture->build();
    $field_picture->name_set($this->name_complex.'__picture');
    $this->_fields['picture'] = $field_picture;
  # button for insertion of the new item
    $button = new button(null, ['data-style' => 'narrow-insert', 'title' => new text('insert')]);
    $button->break_on_validate = true;
    $button->build();
    $button->value_set($this->name_complex.'__insert');
    $button->_type = 'insert';
    $this->_buttons['insert'] = $button;
  # group the previous elements in widget 'insert'
    $widget->child_insert($field_picture, 'picture');
    $widget->child_insert($button,        'button');
    return $widget;
  }

}}