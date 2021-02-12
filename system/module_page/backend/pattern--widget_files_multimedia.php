<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_files_multimedia extends widget_files {

  public $title = 'Multimedia';
  public $item_title = 'File';
  public $attributes = ['data-type' => 'items-files-multimedia'];
  public $name_complex = 'widget_files_multimedia';
# ─────────────────────────────────────────────────────────────────────
  public $upload_dir = 'multimedia/';
  public $fixed_name = 'multimedia-multiple-%%_item_id_context';
  public $max_file_size = '10M';
  public $types_allowed = [
    'mp3'  => 'mp3',
    'mp4'  => 'mp4',
    'png'  => 'png',
    'gif'  => 'gif',
    'jpg'  => 'jpg',
    'jpeg' => 'jpeg'];
  public $thumbnails_is_visible = true;
  public $thumbnails_allowed = [];
# ─────────────────────────────────────────────────────────────────────
  public $player_audio_is_visible = true;
  public $player_audio_controls = true;
  public $player_audio_preload = 'metadata';
  public $player_audio_name = 'default';
  public $player_audio_timeline_is_visible = 'false';

  function widget_manage_get($item, $c_row_id) {
    $widget = parent::widget_manage_get($item, $c_row_id);
    $widget->attribute_insert('data-is-new', $item->object->get_current_state() === 'pre' ? 'true' : 'false');
    if (media::media_class_get($item->object->type) === 'picture') {
      if ($this->thumbnails_is_visible) {
        $thumbnail_markup = new markup_simple('img', ['src' => '/'.$item->object->get_current_path(true).'?thumb=small', 'alt' => new text('thumbnail'), 'width' => '44', 'height' => '44', 'data-type' => 'thumbnail'], +450);
        $widget->child_insert($thumbnail_markup, 'thumbnail');
      }
    }
    if (media::media_class_get($item->object->type) === 'audio') {
      if ($this->player_audio_is_visible) {
        $player_markup = new markup('audio', ['src' => '/'.$item->object->get_current_path(true), 'controls' => $this->player_audio_controls, 'preload' => $this->player_audio_preload, 'data-player-name' => $this->player_audio_name, 'data-player-timeline-is-visible' => $this->player_audio_timeline_is_visible], [], +450);
        $widget->child_insert($player_markup, 'player');
      }
    }
    return $widget;
  }

  function widget_insert_get() {
    $widget = new markup('x-widget', [
      'data-type' => 'insert']);
  # control for upload new file
    $field_file = new field_file;
    $field_file->title = 'File';
    $field_file->max_file_size    = $this->max_file_size;
    $field_file->types_allowed    = $this->types_allowed;
    $field_file->cform            = $this->cform;
    $field_file->min_files_number = null;
    $field_file->max_files_number = null;
    $field_file->has_on_validate  = false;
    $field_file->build();
    $field_file->multiple_set();
    $field_file->name_set($this->name_get_complex().'__file[]');
    $this->controls['#file'] = $field_file;
  # button for insertion of the new item
    $button = new button(null, ['data-style' => 'narrow-insert', 'title' => new text('insert')]);
    $button->break_on_validate = true;
    $button->build();
    $button->value_set($this->name_get_complex().'__insert');
    $button->_type = 'insert';
    $this->controls['~insert'] = $button;
  # grouping of previous elements in widget 'insert'
    $widget->child_insert($field_file, 'file');
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
              $items[$c_id]->object = widget_files_pictures::container_picture_make($c_item->object, $this->thumbnails_allowed);
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
    $decorator->id = 'widget_files-multimedia-items';
    $decorator->view_type = 'template';
    $decorator->template = 'content';
    $decorator->template_row = 'gallery_row';
    $decorator->template_row_mapping = core::array_kmap(['num', 'type', 'children']);
    if ($complex) {
      core::array_sort_by_weight($complex);
      foreach ($complex as $c_row_id => $c_item) {
        if (in_array(media::media_class_get($c_item->object->type), ['picture', 'audio', 'video'])) {
          $decorator->data[$c_row_id] = [
            'type'     => ['value' => media::media_class_get($c_item->object->type)],
            'num'      => ['value' => $c_row_id],
            'children' => ['value' => static::item_markup_get($c_item, $c_row_id)]
          ];
        }
      }
    }
    return $decorator;
  }

  static function item_markup_get($item, $row_id) {
    if (media::media_class_get($item->object->type) === 'picture') return widget_files_pictures::item_markup_get($item, $row_id);
    if (media::media_class_get($item->object->type) === 'audio'  ) return widget_files_audios  ::item_markup_get($item, $row_id);
    if (media::media_class_get($item->object->type) === 'video'  ) return widget_files_videos  ::item_markup_get($item, $row_id);
  }

}}