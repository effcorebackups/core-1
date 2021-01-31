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
    if ($this->thumbnails_is_visible) {
      if (in_array($item->object->type, ['picture', 'png', 'gif', 'jpg', 'jpeg'])) {
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
            if (media::is_type_for_picture_thumbnail_create($c_item->object->type)) {
              $c_file_src = new file($c_item->object->get_current_path());
              $c_file_dst = new file($c_file_src->dirs_get().
                                     $c_file_src->name_get().'.picture');
              $result = media::container_picture_make($c_file_src->path_get(), $c_file_dst->path_get(), [
                'thumbnails_allowed' => $this->thumbnails_allowed,
                'original' => [
                  'type' => $c_item->object->type,
                  'mime' => $c_item->object->mime,
                  'size' => $c_item->object->size
              ]]);
              if ($result) {
                @unlink($c_file_src->path_get());
                $items[$c_id]->object->type     = 'picture';
                $items[$c_id]->object->file     = $items[$c_id]->object->name.'.picture';
                $items[$c_id]->object->mime     = $c_file_dst->mime_get();
                $items[$c_id]->object->pre_path = $c_file_dst->path_get();
                $items[$c_id]->object->size     = $c_file_dst->size_get();
              }
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
        if (in_array($c_item->object->type, ['picture', 'png', 'gif', 'jpg', 'jpeg'])) {
          $decorator->data[$c_row_id] = [
            'type'     => ['value' => 'picture'],
            'num'      => ['value' => $c_row_id],
            'children' => ['value' => new markup('a', ['data-type' => 'picture-wrapper', 'title' => new text('click to open in new window'), 'target' => 'widget_files-pictures-items', 'href' => '/'.$c_item->object->get_current_path(true).'?thumb=big'], new markup_simple('img', ['src' => '/'.$c_item->object->get_current_path(true).'?thumb=middle', 'alt' => new text('thumbnail')]))]
          ];
        }
      }
    }
    return $decorator;
  }

}}