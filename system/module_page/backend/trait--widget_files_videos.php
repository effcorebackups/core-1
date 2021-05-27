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