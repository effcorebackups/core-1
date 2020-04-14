<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_pictures extends widget_fields {

  public $title = 'Pictures';
  public $item_title = 'Picture';
  public $attributes = ['data-type' => 'fields-info-pictures'];
  public $name_complex = 'widget_pictures';

  # ─────────────────────────────────────────────────────────────────────

  function widget_manage_get($item, $c_row_id) {
    $widget = parent::widget_manage_get($item, $c_row_id);
  # info markup
    $info_markup = new markup('x-info',  [], [
        'title' => new markup('x-title', [], 'TITLE'),
        'id'    => new markup('x-id',    [], new text_simple('id') ) ]);
  # grouping of previous elements in widget 'manage'
    $widget->child_insert($info_markup, 'info');
    return $widget;
  }

  function widget_insert_get() {
    $widget = new markup('x-widget', [
      'data-type' => 'insert']);
  # control for upload new picture
    $field_picture = new field_picture;
    $field_picture->build();
    $field_picture->name_set($this->name_complex.'__picture');
    $field_picture->cform = $this->cform;
    $this->controls['#picture'] = $field_picture;
  # button for insertion of the new item
    $button = new button(null, ['data-style' => 'narrow-insert', 'title' => new text('insert')]);
    $button->break_on_validate = true;
    $button->build();
    $button->value_set($this->name_complex.'__insert');
    $button->_type = 'insert';
    $this->controls['~insert'] = $button;
  # grouping of previous elements in widget 'insert'
    $widget->child_insert($field_picture, 'picture');
    $widget->child_insert($button,        'button');
    return $widget;
  }

  # ─────────────────────────────────────────────────────────────────────

  function on_button_click_insert($form, $npath, $button) {
  }

}}