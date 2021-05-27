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

  function on_values_validate_cover($form, $npath, $button) {
    return field_file::on_validate_manual($this->controls['#cover'], $form, $npath);
  }

  ###########################
  ### static declarations ###
  ###########################

  static function widget_manage_audio_item_make(&$widget, &$item, $c_row_id, &$root) {
    if (media::media_class_get($item->object->type) === 'audio') {
      if (!empty($root->audio_player_on_manage_is_visible))  $widget->child_insert(new markup('audio',      ['src' => '/'.$item->object->get_current_path(true)] + $root->audio_player_on_manage_settings, [], +500), 'player');
      if (!empty($item->settings['data-cover-is-embedded'])) $widget->child_insert(new markup_simple('img', ['src' => '/'.$item->object->get_current_path(true).'?cover=small', 'alt' => new text('thumbnail'), 'width' => '44', 'height' => '44', 'data-type' => 'thumbnail'], +450), 'thumbnail');
    }
  }

}}