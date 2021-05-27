<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          trait widget_files_videos__shared {

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
    'autoplay'    => null,
    'buffered'    => null,
    'controls'    => true,
    'crossorigin' => null,
    'loop'        => null,
    'muted'       => null,
    'played'      => null,
    'preload'     => 'metadata'
  ];

  function on_values_validate_poster($form, $npath, $button) {
    return field_file::on_validate_manual($this->controls['#poster'], $form, $npath);
  }

  function on_file_prepare_video($form, $npath, $button, &$items, &$new_item) {
    $pre_path = temporary::directory.'validation/'.$form->validation_cache_date_get().'/'.$form->validation_id.'-'.$this->name_get_complex().'-'.core::array_key_last($items).'.'.$new_item->object->type;
    if ($new_item->object->move_tmp_to_pre($pre_path)) {
      $new_item->settings = $this->video_player_default_settings;
      $new_item->settings['data-poster-is-embedded'] = false;
      if ($this->poster_is_allowed) {
        if (media::media_class_get($new_item->object->type) === 'video') {
          $values = $this->on_values_validate_poster($form, $npath, $button);
          $poster = reset($values);
          if ($poster instanceof file_history) {
            if (media::media_class_get($poster->type) === 'picture') {
              if (media::is_type_for_thumbnail($poster->type)) {
                if ($poster->move_tmp_to_pre($pre_path.'.'.$poster->type)) {
                  if ($new_item->object->container_video_make($this->poster_thumbnails, $poster->get_current_path())) {
                    $new_item->settings['data-poster-is-embedded'] = true;
                    @unlink($pre_path.'.'.$poster->type);
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

  ###########################
  ### static declarations ###
  ###########################

  static function widget_manage_video_item_make(&$widget, &$item, $c_row_id, &$root) {
    if (media::media_class_get($item->object->type) === 'video') {
      if (!empty($item->settings['data-poster-is-embedded'])) {
        $widget->child_insert(new markup_simple('img', ['src' => '/'.$item->object->get_current_path(true).'?poster=small', 'alt' => new text('thumbnail'), 'width' => '44', 'height' => '44', 'data-type' => 'thumbnail'], +450), 'thumbnail');
      }
    }
  }

}}