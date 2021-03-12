<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_files_audios extends widget_files {

  public $title = 'Audios';
  public $item_title = 'Audio';
  public $attributes = ['data-type' => 'items-files-audios'];
  public $name_complex = 'widget_files_audios';
# ─────────────────────────────────────────────────────────────────────
  public $upload_dir = 'audios/';
  public $fixed_name = 'audio-multiple-%%_item_id_context';
  public $max_file_size = '10M';
  public $types_allowed = [
    'mp3' => 'mp3'];
# ─────────────────────────────────────────────────────────────────────
  public $audio_player_is_visible = true;
  public $audio_player_controls = true;
  public $audio_player_preload = 'metadata';
  public $audio_player_name = 'default';
  public $audio_player_timeline_is_visible = 'false';
# ─────────────────────────────────────────────────────────────────────
  public $cover_is_allowed = true;
  public $cover_thumbnails = [
    'small'  => 'small',
    'middle' => 'middle'];
  public $cover_max_file_size = '1M';
  public $cover_types_allowed = [
    'png'  => 'png',
    'gif'  => 'gif',
    'jpg'  => 'jpg',
    'jpeg' => 'jpeg'];

  function widget_manage_get($item, $c_row_id) {
    $widget = parent::widget_manage_get($item, $c_row_id);
    $widget->attribute_insert('data-is-new', $item->object->get_current_state() === 'pre' ? 'true' : 'false');
    if (media::media_class_get($item->object->type) === 'audio') {
      if ($item->settings['audio_player_is_visible']) {
        $player_markup = new markup('audio', ['src' => '/'.$item->object->get_current_path(true),
          'controls'                        => $this->audio_player_controls,
          'preload'                         => $this->audio_player_preload,
          'data-player-name'                => $this->audio_player_name,
          'data-player-timeline-is-visible' => $this->audio_player_timeline_is_visible], [], +500);
        $widget->child_insert($player_markup, 'player');
      }
      if ($item->settings['cover_is_allowed']) {
        $cover_thumbnail_markup = new markup_simple('img', ['src' => '/'.$item->object->get_current_path(true).'?cover=small', 'alt' => new text('thumbnail'), 'width' => '44', 'height' => '44', 'data-type' => 'thumbnail'], +450);
        $widget->child_insert($cover_thumbnail_markup, 'thumbnail');
      }
    }
    return $widget;
  }

  function widget_insert_get() {
    $widget = new markup('x-widget', [
      'data-type' => 'insert']);
  # control for upload new audio
    $field_file_audio = new field_file_audio;
    $field_file_audio->title            = 'Audio';
    $field_file_audio->max_file_size    = $this->max_file_size;
    $field_file_audio->types_allowed    = $this->types_allowed;
    $field_file_audio->cform            = $this->cform;
    $field_file_audio->min_files_number = null;
    $field_file_audio->max_files_number = null;
    $field_file_audio->has_on_validate  = false;
    $field_file_audio->build();
    $field_file_audio->name_set($this->name_get_complex().'__file');
  # control for upload new cover
    $field_file_cover = new field_file_picture;
    $field_file_cover->title            = 'Cover';
    $field_file_cover->max_file_size    = $this->cover_max_file_size;
    $field_file_cover->types_allowed    = $this->cover_types_allowed;
    $field_file_cover->cform            = $this->cform;
    $field_file_cover->min_files_number = null;
    $field_file_cover->max_files_number = null;
    $field_file_cover->has_on_validate  = false;
    $field_file_cover->build();
    $field_file_cover->name_set($this->name_get_complex().'__cover');
  # button for insertion of the new item
    $button = new button(null, ['data-style' => 'narrow-insert', 'title' => new text('insert')]);
    $button->break_on_validate = true;
    $button->build();
    $button->value_set($this->name_get_complex().'__insert');
    $button->_type = 'insert';
  # relate new controls with the widget
    if (true                   ) $this->controls['#file'  ] = $field_file_audio;
    if ($this->cover_is_allowed) $this->controls['#cover' ] = $field_file_cover;
    if (true                   ) $this->controls['~insert'] = $button;
    if (true                   ) $widget->child_insert($field_file_audio, 'file');
    if ($this->cover_is_allowed) $widget->child_insert($field_file_cover, 'cover');
    if (true                   ) $widget->child_insert($button, 'button');
    return $widget;
  }

  # ─────────────────────────────────────────────────────────────────────

  function on_values_validate($form, $npath, $button) {
    $result =             ['cover' => [], 'file' => field_file::on_manual_validate_and_return_value($this->controls['#file' ], $form, $npath)];
    if ($this->cover_is_allowed) $result['cover'] = field_file::on_manual_validate_and_return_value($this->controls['#cover'], $form, $npath);
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
        $c_new_item->settings['audio_player_is_visible'         ] = $this->audio_player_is_visible;
        $c_new_item->settings['audio_player_controls'           ] = $this->audio_player_controls;
        $c_new_item->settings['audio_player_preload'            ] = $this->audio_player_preload;
        $c_new_item->settings['audio_player_name'               ] = $this->audio_player_name;
        $c_new_item->settings['audio_player_timeline_is_visible'] = $this->audio_player_timeline_is_visible;
        $c_new_item->settings['cover_is_allowed'                ] = false;
        $items[] = $c_new_item;
        $c_new_row_id = core::array_key_last($items);
        $c_pre_path = temporary::directory.'validation/'.$form->validation_cache_date_get().'/'.$form->validation_id.'-'.$this->name_get_complex().'-'.$c_new_row_id;
        if ($c_value->move_tmp_to_pre($c_pre_path)) {
          if ($this->cover_is_allowed) {
            if (media::media_class_get($c_new_item->object->type) === 'audio') {
              if ($c_new_item->object->get_current_state() === 'pre') {
                $c_cover = reset($values['cover']);
                if ($c_cover instanceof file_history) {
                    $c_cover->move_tmp_to_pre($c_pre_path.'.'.$c_cover->type);
                       $c_new_item->settings['cover_is_allowed'] = true;
                       $c_new_item->object->container_audio_make($this->cover_thumbnails, $c_cover->get_current_path()); @unlink($c_pre_path.'.'.$c_cover->type);
                } else $c_new_item->object->container_audio_make($this->cover_thumbnails, null);
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
    $decorator->id = 'widget_files-audios-items';
    $decorator->view_type = 'template';
    $decorator->template = 'content';
    $decorator->template_row = 'gallery_row';
    $decorator->template_row_mapping = core::array_kmap(['num', 'type', 'children']);
    if ($complex) {
      core::array_sort_by_weight($complex);
      foreach ($complex as $c_row_id => $c_item) {
        if (media::media_class_get($c_item->object->type) === 'audio') {
          $decorator->data[$c_row_id] = [
            'type'     => ['value' => 'audio'  ],
            'num'      => ['value' => $c_row_id],
            'children' => ['value' => static::item_markup_get($c_item, $c_row_id)]
          ];
        }
      }
    }
    return $decorator;
  }

  static function item_markup_get($item, $row_id, $controls = null, $preload = null, $player_name = null, $timeline_is_visible = true) {
    return new markup('audio', ['src' => '/'.$item->object->get_current_path(true),
      'controls'                        => $controls    !== null ? $controls    : $item->settings['audio_player_controls'],
      'preload'                         => $preload     !== null ? $preload     : $item->settings['audio_player_preload' ],
      'data-player-name'                => $player_name !== null ? $player_name : $item->settings['audio_player_name'    ],
      'data-player-timeline-is-visible' => $timeline_is_visible
    ]);
  }

}}