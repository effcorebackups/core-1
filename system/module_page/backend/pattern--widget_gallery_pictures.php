<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_gallery_pictures extends widget_files {

  public $title = 'Pictures';
  public $item_title = 'Picture';
  public $attributes = ['data-type' => 'items-info-files-picture'];
  public $name_complex = 'widget_gallery_pictures';
# ─────────────────────────────────────────────────────────────────────
  public $upload_dir = 'galleries/';
  public $fixed_name = 'pictures-%%_instance_id_context-%%_item_id_context';
  public $max_file_size = '1M';
  public $allowed_types = [
    'jpg'  => 'jpg',
    'jpeg' => 'jpeg',
    'png'  => 'png',
    'gif'  => 'gif'
  ];

  function widget_insert_get() {
    $widget = new markup('x-widget', [
      'data-type' => 'insert']);
  # control for upload new file
    $field_file_picture = new field_file_picture;
    $field_file_picture->title = 'File';
    $field_file_picture->max_file_size    = $this->max_file_size;
    $field_file_picture->allowed_types    = $this->allowed_types;
    $field_file_picture->cform            = $this->cform;
    $field_file_picture->min_files_number = null;
    $field_file_picture->max_files_number = null;
    $field_file_picture->has_on_validate         = false;
    $field_file_picture->has_on_validate_phase_3 = false;
    $field_file_picture->build();
    $field_file_picture->multiple_set();
    $field_file_picture->name_set($this->name_get_complex().'__file[]');
    $this->controls['#file'] = $field_file_picture;
  # button for insertion of the new item
    $button = new button(null, ['data-style' => 'narrow-insert', 'title' => new text('insert')]);
    $button->break_on_validate = true;
    $button->build();
    $button->value_set($this->name_get_complex().'__insert');
    $button->_type = 'insert';
    $this->controls['~insert'] = $button;
  # grouping of previous elements in widget 'insert'
    $widget->child_insert($field_file_picture, 'file');
    $widget->child_insert($button, 'button');
    return $widget;
  }

}}