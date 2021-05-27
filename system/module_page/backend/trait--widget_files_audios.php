<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          trait widget_files_audios__shared {

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
    'data-player-name'                => 'default',
    'data-player-timeline-is-visible' => 'false',
    'autoplay'    => null,
    'controls'    => true,
    'crossorigin' => null,
    'loop'        => null,
    'muted'       => null,
    'preload'     => 'metadata'];
  public $audio_player_default_settings = [
    'data-player-name'                => 'default',
    'data-player-timeline-is-visible' => 'true',
    'autoplay'    => null,
    'controls'    => true,
    'crossorigin' => null,
    'loop'        => null,
    'muted'       => null,
    'preload'     => 'metadata'
  ];

  static function widget_manage_audio_item_make(&$widget, &$item, $c_row_id, &$root) {
    if (media::media_class_get($item->object->type) === 'audio') {
      if (!empty($root->audio_player_on_manage_is_visible))  $widget->child_insert(new markup('audio',      ['src' => '/'.$item->object->get_current_path(true)] + $root->audio_player_on_manage_settings, [], +500), 'player');
      if (!empty($item->settings['data-cover-is-embedded'])) $widget->child_insert(new markup_simple('img', ['src' => '/'.$item->object->get_current_path(true).'?cover=small', 'alt' => new text('thumbnail'), 'width' => '44', 'height' => '44', 'data-type' => 'thumbnail'], +450), 'thumbnail');
    }
  }

  # ─────────────────────────────────────────────────────────────────────

  function on_values_validate_cover($form, $npath, $button) {
    return field_file::on_validate_manual($this->controls['#cover'], $form, $npath);
  }

  function on_file_prepare_audio($form, $npath, $button, &$items, &$new_item) {
    $pre_path = temporary::directory.'validation/'.$form->validation_cache_date_get().'/'.$form->validation_id.'-'.$this->name_get_complex().'-'.core::array_key_last($items).'.'.$new_item->object->type;
    if ($new_item->object->move_tmp_to_pre($pre_path)) {
      $new_item->settings = $this->audio_player_default_settings;
      $new_item->settings['data-cover-is-embedded'] = false;
      if ($this->cover_is_allowed) {
        if (media::media_class_get($new_item->object->type) === 'audio') {
          $values = $this->on_values_validate_cover($form, $npath, $button);
          $cover = reset($values);
          if ($cover instanceof file_history) {
            if (media::media_class_get($cover->type) === 'picture') {
              if (media::is_type_for_thumbnail($cover->type)) {
                if ($cover->move_tmp_to_pre($pre_path.'.'.$cover->type)) {
                  if ($new_item->object->container_audio_make($this->cover_thumbnails, $cover->get_current_path())) {
                    $new_item->settings['data-cover-is-embedded'] = true;
                    @unlink($pre_path.'.'.$cover->type);
                  }
                }
              }
            }
          }
        }
      }
      return true;
    }
  }

  static function on_button_click_insert_audio(&$widget, $form, $npath, $button) {
    if ($widget->cover_is_allowed) {
      $values       = $widget->on_values_validate      ($form, $npath, $button);
      $values_cover = $widget->on_values_validate_cover($form, $npath, $button);
      if (!$widget->controls['#file']->has_error() &&                                              count($values) === 0) {$widget->controls['#file']->error_set('Field "%%_title" cannot be blank!', ['title' => (new text($widget->controls['#file']->title))->render() ]); return;}
      if (!$widget->controls['#file']->has_error() && !$widget->controls['#cover']->has_error() && count($values) !== 0)
           return widget_files::on_button_click_insert($widget, $form, $npath, $button);
    } else return widget_files::on_button_click_insert($widget, $form, $npath, $button);
  }

}}