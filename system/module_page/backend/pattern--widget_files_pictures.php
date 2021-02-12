<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_files_pictures extends widget_files {

  public $title = 'Pictures';
  public $item_title = 'Picture';
  public $attributes = ['data-type' => 'items-files-pictures'];
  public $name_complex = 'widget_files_pictures';
# ─────────────────────────────────────────────────────────────────────
  public $upload_dir = 'pictures/';
  public $fixed_name = 'picture-multiple-%%_item_id_context';
  public $max_file_size = '1M';
  public $types_allowed = [
    'png'  => 'png',
    'gif'  => 'gif',
    'jpg'  => 'jpg',
    'jpeg' => 'jpeg'];
  public $thumbnails_is_visible = true;
  public $thumbnails_allowed = [];

  function widget_manage_get($item, $c_row_id) {
    $widget = parent::widget_manage_get($item, $c_row_id);
    $widget->attribute_insert('data-is-new', $item->object->get_current_state() === 'pre' ? 'true' : 'false');
    if (media::media_class_get($item->object->type) === 'picture') {
      if ($this->thumbnails_is_visible) {
        $thumbnail_markup = new markup_simple('img', ['src' => '/'.$item->object->get_current_path(true).'?thumb=small', 'alt' => new text('thumbnail'), 'width' => '44', 'height' => '44', 'data-type' => 'thumbnail'], +450);
        $widget->child_insert($thumbnail_markup, 'thumbnail');
      }
    }
    return $widget;
  }

  function widget_insert_get() {
    $widget = new markup('x-widget', [
      'data-type' => 'insert']);
  # control for upload new file
    $field_file_picture = new field_file_picture;
    $field_file_picture->title = 'File';
    $field_file_picture->max_file_size    = $this->max_file_size;
    $field_file_picture->types_allowed    = $this->types_allowed;
    $field_file_picture->cform            = $this->cform;
    $field_file_picture->min_files_number = null;
    $field_file_picture->max_files_number = null;
    $field_file_picture->has_on_validate  = false;
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

  # ─────────────────────────────────────────────────────────────────────

  function items_set($items, $once = false) {
    if (count($this->thumbnails_allowed)) {
      if (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[1]['function'] === 'on_button_click_insert') {
        foreach ($items as $c_id => $c_item) {
          if ($c_item->object->get_current_state() === 'pre') {
            if (media::is_type_for_thumbnail($c_item->object->type)) {
              $items[$c_id]->object = static::container_picture_make($c_item->object, $this->thumbnails_allowed);
            }
          }
        }
      }
    }
    parent::items_set($items, $once);
  }

  ###########################
  ### static declarations ###
  ###########################

  static function container_picture_make($file_history, $thumbnails_allowed) {
    $file_src = new file($file_history->get_current_path());
    $file_dst = new file($file_src->dirs_get().
                         $file_src->name_get().'.picture');
    $result = media::container_picture_make($file_src->path_get(), $file_dst->path_get(), [
      'thumbnails_allowed' => $thumbnails_allowed,
      'original' => [
        'type' => $file_history->type,
        'mime' => $file_history->mime,
        'size' => $file_history->size
    ]]);
    if ($result) {
      @unlink($file_src->path_get());
      $file_history->type     = 'picture';
      $file_history->file     = $file_history->name.'.picture';
      $file_history->mime     = $file_dst->mime_get();
      $file_history->pre_path = $file_dst->path_get();
      $file_history->size     = $file_dst->size_get();
      return $file_history;
    }
  }

  static function complex_value_to_markup($complex) {
    $decorator = new decorator;
    $decorator->id = 'widget_files-pictures-items';
    $decorator->view_type = 'template';
    $decorator->template = 'content';
    $decorator->template_row = 'gallery_row';
    $decorator->template_row_mapping = core::array_kmap(['num', 'type', 'children']);
    if ($complex) {
      core::array_sort_by_weight($complex);
      foreach ($complex as $c_row_id => $c_item) {
        if (media::media_class_get($c_item->object->type) === 'picture') {
          $decorator->data[$c_row_id] = [
            'type'     => ['value' => 'picture'],
            'num'      => ['value' => $c_row_id],
            'children' => ['value' => static::item_markup_get($c_item, $c_row_id)]
          ];
        }
      }
    }
    return $decorator;
  }

  static function item_markup_get($item, $row_id, $title = 'click to open in new window', $target = 'widget_files-pictures-items', $alt = 'thumbnail') {
    return new markup('a', ['data-type' => 'picture-wrapper', 'title' => new text($title), 'target' => $target, 'href' => '/'.$item->object->get_current_path(true).'?thumb=big'],
      new markup_simple('img', ['src' => '/'.$item->object->get_current_path(true).'?thumb=middle', 'alt' => new text($alt)])
    );
  }

}}