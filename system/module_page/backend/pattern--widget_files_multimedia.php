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
    'png'  => 'png',
    'gif'  => 'gif',
    'jpg'  => 'jpg',
    'jpeg' => 'jpeg'];
  public $thumbnails_is_visible = true;
  public $thumbnails_allowed = [];
  public $player_audio_is_visible = true;
  public $player_audio_controls = true;
  public $player_audio_preload = 'metadata';
  public $player_audio_name = 'default';
  public $player_audio_timeline_is_visible = 'false';

  function widget_manage_get($item, $c_row_id) {
    $widget = parent::widget_manage_get($item, $c_row_id);
    $widget->attribute_insert('data-is-new', $item->object->get_current_state() === 'pre' ? 'true' : 'false');
    if ($this->thumbnails_is_visible) {
      if (in_array($item->object->type, ['picture', 'png', 'gif', 'jpg', 'jpeg'])) {
        $file = new file($item->object->get_current_path());
        $thumbnail_markup = new markup_simple('img', ['src' => '/'.$file->path_get_relative().'?thumb=small', 'alt' => new text('thumbnail'), 'width' => '44', 'height' => '44', 'data-type' => 'thumbnail'], +450);
        $widget->child_insert($thumbnail_markup, 'thumbnail');
      }
    }
    if ($this->player_audio_is_visible) {
      if ($item->object->type === 'mp3') {
        $file = new file($item->object->get_current_path());
        $player_markup = new markup('audio', ['src' => '/'.$file->path_get_relative(), 'controls' => $this->player_audio_controls, 'preload' => $this->player_audio_preload, 'data-player-name' => $this->player_audio_name, 'data-player-timeline-is-visible' => $this->player_audio_timeline_is_visible], [], +450);
        $widget->child_insert($player_markup, 'player');
      }
    }
    return $widget;
  }

  ###########################
  ### static declarations ###
  ###########################

  static function complex_value_to_markup($complex) {  
    $decorator = new decorator;
    $decorator->id = 'widget_files-pictures-items';
    $decorator->view_type = 'template';
    $decorator->template = 'content';
    $decorator->template_row = 'gallery_row';
    $decorator->template_row_mapping = core::array_kmap(['num', 'type', 'children']);
    if ($complex) {
      core::array_sort_by_weight($complex);
      foreach ($complex as $c_row_id => $c_item) {
        $c_file = new file($c_item->object->get_current_path());
        if (in_array($c_item->object->type, ['picture', 'png', 'gif', 'jpg', 'jpeg'])) {
          $decorator->data[$c_row_id] = [
            'type'     => ['value' => 'picture'],
            'num'      => ['value' => $c_row_id],
            'children' => ['value' => new markup('a', ['data-type' => 'picture-wrapper', 'title' => new text('click to open in new window'), 'target' => 'widget_files-pictures-items', 'href' => '/'.$c_file->path_get_relative().'?thumb=big'], new markup_simple('img', ['src' => '/'.$c_file->path_get_relative().'?thumb=middle', 'alt' => new text('thumbnail')]))]
          ];
        }
        if ($c_item->object->type === 'mp3') {
          $decorator->data[$c_row_id] = [
            'type'     => ['value' => 'audio'  ],
            'num'      => ['value' => $c_row_id],
            'children' => ['value' => new markup('audio', ['src' => '/'.$c_file->path_get_relative(), 'controls' => true, 'preload' => 'metadata', 'data-player-name' => 'default', 'data-player-timeline-is-visible' => 'true'])]
          ];
        }
      }
    }
    return $decorator;
  }

}}