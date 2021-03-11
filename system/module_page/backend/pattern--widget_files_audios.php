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
  public $cover_max_file_size = '1M';
  public $cover_types_allowed = [
    'png'  => 'png',
    'gif'  => 'gif',
    'jpg'  => 'jpg',
    'jpeg' => 'jpeg'];
  public $audio_player_is_visible = true;
  public $audio_player_controls = true;
  public $audio_player_preload = 'metadata';
  public $audio_player_name = 'default';
  public $audio_player_timeline_is_visible = 'false';

  function widget_manage_get($item, $c_row_id) {
    $widget = parent::widget_manage_get($item, $c_row_id);
    $widget->attribute_insert('data-is-new', $item->object->get_current_state() === 'pre' ? 'true' : 'false');
    if (media::media_class_get($item->object->type) === 'audio') {
      if ($this->audio_player_is_visible) {
        $player_markup = new markup('audio', ['src' => '/'.$item->object->get_current_path(true), 'controls' => $this->audio_player_controls, 'preload' => $this->audio_player_preload, 'data-player-name' => $this->audio_player_name, 'data-player-timeline-is-visible' => $this->audio_player_timeline_is_visible], [], +450);
        $widget->child_insert($player_markup, 'player');
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
    $field_file_audio->multiple_set();
    $field_file_audio->name_set($this->name_get_complex().'__file[]');
  # button for insertion of the new item
    $button = new button(null, ['data-style' => 'narrow-insert', 'title' => new text('insert')]);
    $button->break_on_validate = true;
    $button->build();
    $button->value_set($this->name_get_complex().'__insert');
    $button->_type = 'insert';
  # relate new controls with the widget
    $this->controls['#file'  ] = $field_file_audio;
    $this->controls['~insert'] = $button;
    $widget->child_insert($field_file_audio, 'file');
    $widget->child_insert($button, 'button');
    return $widget;
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

  static function item_markup_get($item, $row_id, $controls = true, $preload = 'metadata', $player_name = 'default', $timeline_is_visible = true) {
    return new markup('audio', ['src' => '/'.$item->object->get_current_path(true), 'controls' => $controls, 'preload' => $preload, 'data-player-name' => $player_name, 'data-player-timeline-is-visible' => $timeline_is_visible]);
  }

}}