<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_files_videos extends widget_files {

  public $title = 'Videos';
  public $item_title = 'Video';
  public $attributes = ['data-type' => 'items-files-videos'];
  public $name_complex = 'widget_files_videos';
# ─────────────────────────────────────────────────────────────────────
  public $upload_dir = 'videos/';
  public $fixed_name = 'video-multiple-%%_item_id_context';
  public $max_file_size = '50M';
  public $types_allowed = [
    'mp4' => 'mp4'];
# ─────────────────────────────────────────────────────────────────────
  public $video_player_autoplay = false;
  public $video_player_controls = true;
  public $video_player_loop = false;
  public $video_player_name = 'default';
  public $video_player_preload = 'metadata';
# ─────────────────────────────────────────────────────────────────────
  public $poster_is_allowed = true;
  public $poster_thumbnails = [
    'small'  => 'small',
    'middle' => 'middle'];
  public $poster_max_file_size = '1M';
  public $poster_types_allowed = [
    'png'  => 'png',
    'gif'  => 'gif',
    'jpg'  => 'jpg',
    'jpeg' => 'jpeg'
  ];

  function widget_manage_get($item, $c_row_id) {
    $widget = parent::widget_manage_get($item, $c_row_id);
    $widget->attribute_insert('data-is-new', $item->object->get_current_state() === 'pre' ? 'true' : 'false');
    if (media::media_class_get($item->object->type) === 'video') {
      if ($item->settings['poster_is_embedded']) {
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
  # control for upload new poster
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

  function on_values_validate($form, $npath, $button) {
    $result =              ['poster' => [], 'file' => field_file::on_manual_validate_and_return_value($this->controls['#file'  ], $form, $npath)];
    if ($this->poster_is_allowed) $result['poster'] = field_file::on_manual_validate_and_return_value($this->controls['#poster'], $form, $npath);
    return $result;
  }

  function on_button_click_insert($form, $npath, $button) {
    $values = $this->on_values_validate($form, $npath, $button);
    if (count($values['file'])) {
      $items = $this->items_get();
      foreach ($values['file'] as $c_value) {
        $min_weight = 0;
        foreach ($items as $c_row_id => $c_item)
          $min_weight = min($min_weight, $c_item->weight);
        $c_new_item = new \stdClass;
        $c_new_item->is_deleted = false;
        $c_new_item->weight = count($items) ? $min_weight - 5 : 0;
        $c_new_item->object = $c_value;
        $c_new_item->settings['video_player_autoplay'] = $this->video_player_autoplay;
        $c_new_item->settings['video_player_controls'] = $this->video_player_controls;
        $c_new_item->settings['video_player_loop'    ] = $this->video_player_loop;
        $c_new_item->settings['video_player_name'    ] = $this->video_player_name;
        $c_new_item->settings['video_player_preload' ] = $this->video_player_preload;
        $c_new_item->settings['poster_is_embedded'   ] = false;
        $items[] = $c_new_item;
        $c_new_row_id = core::array_key_last($items);
        $c_pre_path = temporary::directory.'validation/'.$form->validation_cache_date_get().'/'.$form->validation_id.'-'.$this->name_get_complex().'-'.$c_new_row_id;
        if ($c_value->move_tmp_to_pre($c_pre_path)) {
          if ($this->poster_is_allowed) {
            if (media::media_class_get($c_new_item->object->type) === 'video') {
              if ($c_new_item->object->get_current_state() === 'pre') {
                $c_poster = reset($values['poster']);
                if ($c_poster instanceof file_history) {
                    $c_poster->move_tmp_to_pre($c_pre_path.'.'.$c_poster->type);
                       $c_new_item->settings['poster_is_embedded'] = true;
                       $c_new_item->object->container_video_make($this->poster_thumbnails, $c_poster->get_current_path()); @unlink($c_pre_path.'.'.$c_poster->type);
                } else $c_new_item->object->container_video_make($this->poster_thumbnails, null);
              }
            }
          }
          $this->items_set($items);
          message::insert(new text(
            'Item of type "%%_type" with ID = "%%_id" was inserted.', [
            'type' => (new text($this->item_title))->render(),
            'id'   => $c_new_item->object->file]));
        } else {
          $form->error_set();
          return;
        }
      }
      message::insert('Do not forget to save the changes!');
      return true;
    } elseif (!$this->controls['#file']->has_error()) {
      $this->controls['#file']->error_set(
        'Field "%%_title" cannot be blank!', ['title' => (new text($this->controls['#file']->title))->render() ]
      );
    }
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

  static function item_markup_get($item, $row_id, $autoplay = null, $controls = null, $player_name = null, $loop = null, $preload = null) {
    return new markup_simple('video', ['src' => '/'.$item->object->get_current_path(true),
      'poster'           => '/'.$item->object->get_current_path(true).'?poster=big',
      'autoplay'         => $autoplay    !== null ? $autoplay    : $item->settings['video_player_autoplay'],
      'controls'         => $controls    !== null ? $controls    : $item->settings['video_player_controls'],
      'data-player-name' => $player_name !== null ? $player_name : $item->settings['video_player_name'    ],
      'loop'             => $loop        !== null ? $loop        : $item->settings['video_player_loop'    ],
      'preload'          => $preload     !== null ? $preload     : $item->settings['video_player_preload' ]
    ]);
  }

}}