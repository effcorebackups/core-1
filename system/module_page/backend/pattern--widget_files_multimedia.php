<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_files_multimedia extends widget_files {

  use widget_files_pictures__shared;
  use widget_files_videos__shared;
  use widget_files_audios__shared;

  public $title = 'Multimedia';
  public $item_title = 'File';
  public $attributes = ['data-type' => 'items-files-multimedia'];
  public $name_complex = 'widget_files_multimedia';
# ─────────────────────────────────────────────────────────────────────
  public $upload_dir = 'multimedia/';
  public $fixed_name = 'multimedia-multiple-%%_item_id_context';

# ── picture ──────────────────────────────────────────────────────────
  public $picture_max_file_size = '1M';
  public $picture_types_allowed = [
    'png'  => 'png',
    'gif'  => 'gif',
    'jpg'  => 'jpg',
    'jpeg' => 'jpeg'];
# ─────────────────────────────────────────────────────────────────────
  public $thumbnails_is_allowed = true;
  public $thumbnails = [
    'small'  => 'small',
    'middle' => 'middle'
  ];

# ── video ────────────────────────────────────────────────────────────
  public $video_max_file_size = '50M';
  public $video_types_allowed = [
    'mp4' => 'mp4'];
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
    'jpeg' => 'jpeg'];
# ─────────────────────────────────────────────────────────────────────
  public $video_player_default_settings = [
    'autoplay'    => false,
    'buffered'    => null,
    'controls'    => true,
    'crossorigin' => null,
    'loop'        => false,
    'muted'       => false,
    'played'      => null,
    'preload'     => 'metadata'
  ];

# ── audio ────────────────────────────────────────────────────────────
  public $audio_max_file_size = '10M';
  public $audio_types_allowed = [
    'mp3' => 'mp3'];
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
# ─────────────────────────────────────────────────────────────────────
  public $audio_player_on_manage_is_visible = true;
  public $audio_player_on_manage_settings = [
    'autoplay'                        => false,
    'controls'                        => true,
    'crossorigin'                     => null,
    'loop'                            => false,
    'muted'                           => false,
    'preload'                         => 'metadata',
    'data-player-name'                => 'default',
    'data-player-timeline-is-visible' => 'false'];
  public $audio_player_default_settings = [
    'autoplay'                        => false,
    'controls'                        => true,
    'crossorigin'                     => null,
    'loop'                            => false,
    'muted'                           => false,
    'preload'                         => 'metadata',
    'data-player-name'                => 'default',
    'data-player-timeline-is-visible' => 'true'
  ];

  function widget_insert_get() {
    $widget = new markup('x-widget', [
      'data-type' => 'insert']);
  # fieldsets
    $fieldset_pictures = new fieldset('Pictures', null, ['data-type' => 'pictures']);
    $fieldset_video    = new fieldset('Video',    null, ['data-type' => 'video'   ]);
    $fieldset_audio    = new fieldset('Audio',    null, ['data-type' => 'audio'   ]);
    $fieldset_pictures->state = 'closed';
    $fieldset_video   ->state = 'closed';
    $fieldset_audio   ->state = 'closed';
  # control for upload new picture
    $field_file_picture = new field_file_picture;
    $field_file_picture->title            = 'Picture';
    $field_file_picture->max_file_size    = $this->picture_max_file_size;
    $field_file_picture->types_allowed    = $this->picture_types_allowed;
    $field_file_picture->cform            = $this->cform;
    $field_file_picture->min_files_number = null;
    $field_file_picture->max_files_number = null;
    $field_file_picture->has_on_validate  = false;
    $field_file_picture->build();
    $field_file_picture->multiple_set();
    $field_file_picture->name_set($this->name_get_complex().'__file[]');
  # control for upload new video
    $field_file_video = new field_file_video;
    $field_file_video->title            = 'Video';
    $field_file_video->max_file_size    = $this->video_max_file_size;
    $field_file_video->types_allowed    = $this->video_types_allowed;
    $field_file_video->cform            = $this->cform;
    $field_file_video->min_files_number = null;
    $field_file_video->max_files_number = null;
    $field_file_video->has_on_validate  = false;
    $field_file_video->build();
    $field_file_video->name_set($this->name_get_complex().'__file_video');
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
  # control for upload new audio
    $field_file_audio = new field_file_audio;
    $field_file_audio->title            = 'Audio';
    $field_file_audio->max_file_size    = $this->audio_max_file_size;
    $field_file_audio->types_allowed    = $this->audio_types_allowed;
    $field_file_audio->cform            = $this->cform;
    $field_file_audio->min_files_number = null;
    $field_file_audio->max_files_number = null;
    $field_file_audio->has_on_validate  = false;
    $field_file_audio->build();
    $field_file_audio->name_set($this->name_get_complex().'__file_audio');
  # control for upload new audio cover
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
    $button_insert_picture = new button(null, ['data-style' => 'narrow-insert', 'title' => new text('insert')]);
    $button_insert_picture->break_on_validate = true;
    $button_insert_picture->build();
    $button_insert_picture->value_set($this->name_get_complex().'__insert_picture');
    $button_insert_picture->_type = 'insert';
    $button_insert_picture->_kind = 'picture';
    $button_insert_video = new button(null, ['data-style' => 'narrow-insert', 'title' => new text('insert')]);
    $button_insert_video->break_on_validate = true;
    $button_insert_video->build();
    $button_insert_video->value_set($this->name_get_complex().'__insert_video');
    $button_insert_video->_type = 'insert';
    $button_insert_video->_kind = 'video';
    $button_insert_audio = new button(null, ['data-style' => 'narrow-insert', 'title' => new text('insert')]);
    $button_insert_audio->break_on_validate = true;
    $button_insert_audio->build();
    $button_insert_audio->value_set($this->name_get_complex().'__insert_audio');
    $button_insert_audio->_type = 'insert';
    $button_insert_audio->_kind = 'audio';
  # relate new controls with the widget
    $this->controls['#file_picture'] = $field_file_picture;
    $this->controls['#file_video'  ] = $field_file_video;
    $this->controls['#file_audio'  ] = $field_file_audio;
    $this->controls['#poster'      ] = $field_file_poster;
    $this->controls['#cover'       ] = $field_file_cover;
    $this->controls['~insert_picture'] = $button_insert_picture;
    $this->controls['~insert_video'  ] = $button_insert_video;
    $this->controls['~insert_audio'  ] = $button_insert_audio;
    $this->controls['*fieldset_pictures'] = $fieldset_pictures;
    $this->controls['*fieldset_video'   ] = $fieldset_video;
    $this->controls['*fieldset_audio'   ] = $fieldset_audio;
    $fieldset_pictures->child_insert($field_file_picture, 'file');
    $fieldset_pictures->child_insert($button_insert_picture, 'button');
    $fieldset_video   ->child_insert($field_file_video, 'file');
    $fieldset_video   ->child_insert($field_file_poster, 'poster');
    $fieldset_video   ->child_insert($button_insert_video, 'button');
    $fieldset_audio   ->child_insert($field_file_audio, 'file');
    $fieldset_audio   ->child_insert($field_file_cover, 'cover');
    $fieldset_audio   ->child_insert($button_insert_audio, 'button');
    $widget->child_insert($fieldset_pictures, 'pictures');
    $widget->child_insert($fieldset_video, 'video');
    $widget->child_insert($fieldset_audio, 'audio');
    return $widget;
  }

  # ─────────────────────────────────────────────────────────────────────

  function on_values_validate($form, $npath, $button) {
    if ($button->_kind === 'picture') return field_file::on_manual_validate_and_return_value($this->controls['#file'      ], $form, $npath);
    if ($button->_kind === 'video'  ) return field_file::on_manual_validate_and_return_value($this->controls['#file_video'], $form, $npath);
    if ($button->_kind === 'audio'  ) return field_file::on_manual_validate_and_return_value($this->controls['#file_audio'], $form, $npath);
  }

  function on_file_prepare($form, $npath, $button, &$items, &$new_item) {
 // if ($button->_kind === 'picture') {$this->controls['#file'] = $this->controls['#file'      ]; return $this->on_file_prepare_picture($form, $npath, $button, $items, $new_item);}
    if ($button->_kind === 'video'  ) {$this->controls['#file'] = $this->controls['#file_video']; return $this->on_file_prepare_video  ($form, $npath, $button, $items, $new_item);}
    if ($button->_kind === 'audio'  ) {$this->controls['#file'] = $this->controls['#file_audio']; return $this->on_file_prepare_audio  ($form, $npath, $button, $items, $new_item);}
  }

  function on_button_click_insert($form, $npath, $button) {
 // if ($button->_kind === 'picture') {$this->controls['#file'] = $this->controls['#file'      ]; return $this->on_button_click_insert      ($form, $npath, $button);}
    if ($button->_kind === 'video'  ) {$this->controls['#file'] = $this->controls['#file_video']; return $this->on_button_click_insert_video($form, $npath, $button);}
    if ($button->_kind === 'audio'  ) {$this->controls['#file'] = $this->controls['#file_audio']; return $this->on_button_click_insert_audio($form, $npath, $button);}
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