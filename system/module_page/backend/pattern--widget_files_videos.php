<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_files_videos extends widget_files {

  use widget_files_videos__shared;

  public $title = 'Videos';
  public $item_title = 'Video';
  public $attributes = ['data-type' => 'items-files-videos'];
  public $name_complex = 'widget_files_videos';
# ─────────────────────────────────────────────────────────────────────
  public $upload_dir = 'videos/';
  public $fixed_name = 'video-multiple-%%_item_id_context';
# ─────────────────────────────────────────────────────────────────────
  public $max_file_size = '50M';
  public $types_allowed = [
    'mp4' => 'mp4'
  ];

  function widget_manage_get($item, $c_row_id) {
    $widget = parent::widget_manage_get($item, $c_row_id);
    $widget->attribute_insert('data-is-new', $item->object->get_current_state() === 'pre' ? 'true' : 'false');
    if (media::media_class_get($item->object->type) === 'video') {
      if ($item->settings['data-poster-is-embedded']) {
        $poster_thumbnail_markup = new markup_simple('img', ['src' => '/'.$item->object->get_current_path(true).'?poster=small', 'alt' => new text('thumbnail'), 'width' => '44', 'height' => '44', 'data-type' => 'thumbnail'], +450);
        $widget->child_insert($poster_thumbnail_markup, 'thumbnail');
      }
    }
    return $widget;
  }

  function widget_insert_get() {
    $widget = new markup('x-widget', [
      'data-type' => 'insert']);
  # control for upload new video
    $field_file_video = new field_file_video;
    $field_file_video->title            = 'Video';
    $field_file_video->max_file_size    = $this->max_file_size;
    $field_file_video->types_allowed    = $this->types_allowed;
    $field_file_video->cform            = $this->cform;
    $field_file_video->min_files_number = null;
    $field_file_video->max_files_number = null;
    $field_file_video->has_on_validate  = false;
    $field_file_video->build();
    $field_file_video->name_set($this->name_get_complex().'__file');
  # control for upload new video poster
    $field_file_poster = new field_file_picture;
    $field_file_poster->title            = 'Poster';
    $field_file_poster->max_file_size    = $this->poster_max_file_size;
    $field_file_poster->types_allowed    = $this->poster_types_allowed;
    $field_file_poster->cform            = $this->cform;
    $field_file_poster->min_files_number = null;
    $field_file_poster->max_files_number = null;
    $field_file_poster->has_on_validate  = false;
    $field_file_poster->build();
    $field_file_poster->name_set($this->name_get_complex().'__poster');
  # button for insertion of the new item
    $button = new button(null, ['data-style' => 'narrow-insert', 'title' => new text('insert')]);
    $button->break_on_validate = true;
    $button->build();
    $button->value_set($this->name_get_complex().'__insert');
    $button->_type = 'insert';
  # relate new controls with the widget
    if (true                    ) $this->controls['#file'  ] = $field_file_video;
    if ($this->poster_is_allowed) $this->controls['#poster'] = $field_file_poster;
    if (true                    ) $this->controls['~insert'] = $button;
    if (true                    ) $widget->child_insert($field_file_video, 'file');
    if ($this->poster_is_allowed) $widget->child_insert($field_file_poster, 'poster');
    if (true                    ) $widget->child_insert($button, 'button');
    return $widget;
  }

  # ─────────────────────────────────────────────────────────────────────

  function on_file_prepare($form, $npath, $button, &$items, &$new_item) {
    return $this->on_file_prepare_video($form, $npath, $button, $items, $new_item);
  }

  function on_button_click_insert($form, $npath, $button) {
    return $this->on_button_click_insert_video($form, $npath, $button);
  }

  ###########################
  ### static declarations ###
  ###########################

  static function complex_value_to_markup($complex) {
    $decorator = new decorator;
    $decorator->id = 'widget_files-videos-items';
    $decorator->view_type = 'template';
    $decorator->template = 'content';
    $decorator->template_row = 'gallery_row';
    $decorator->template_row_mapping = core::array_kmap(['num', 'type', 'children']);
    if ($complex) {
      core::array_sort_by_weight($complex);
      foreach ($complex as $c_row_id => $c_item) {
        if (media::media_class_get($c_item->object->type) === 'video') {
          $decorator->data[$c_row_id] = [
            'type'     => ['value' => 'video'  ],
            'num'      => ['value' => $c_row_id],
            'children' => ['value' => static::item_markup_get($c_item, $c_row_id)]
          ];
        }
      }
    }
    return $decorator;
  }

  static function item_markup_get($item, $row_id) {
    if ($item->settings['data-poster-is-embedded'])
         return new markup('video', ['src' => '/'.$item->object->get_current_path(true), 'poster' => '/'.$item->object->get_current_path(true).'?poster=big'] + $item->settings);
    else return new markup('video', ['src' => '/'.$item->object->get_current_path(true)                                                                     ] + $item->settings);
  }

}}