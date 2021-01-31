<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_file_audio extends field_file {

  public $title = 'Audio';
  public $attributes = ['data-type' => 'file-audio'];
  public $element_attributes = [
    'type' => 'file',
    'name' => 'audio'];
  public $max_file_size = '10M';
  public $types_allowed = [
    'mp3' => 'mp3'];
  public $player_is_visible = true;
  public $player_controls = true;
  public $player_preload = 'metadata';
  public $player_name = 'default';
  public $player_timeline_is_visible = 'true';

  protected function pool_manager_action_insert_get_field_text($item, $id, $type) {
    if ($this->player_is_visible) {
      $player_markup = new markup('audio', ['src' => '/'.$item->get_current_path(true), 'controls' => $this->player_controls, 'preload' => $this->player_preload, 'data-player-name' => $this->player_name, 'data-player-timeline-is-visible' => $this->player_timeline_is_visible], [], +450);
           return new node([], [$player_markup, new text('delete audio "%%_audio"', ['audio' => $item->file])]);
    } else return new node([], [                new text('delete audio "%%_audio"', ['audio' => $item->file])]);
  }

}}