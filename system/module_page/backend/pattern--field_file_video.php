<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_file_video extends field_file {

  public $title = 'Video';
  public $attributes = ['data-type' => 'file-video'];
  public $element_attributes = [
    'type' => 'file',
    'name' => 'video'];
  public $max_file_size = '50M';
  public $types_allowed = [
    'mp4' => 'mp4'
  ];

  protected function pool_manager_action_insert_get_field_text($item, $id, $type) {
    return new text('delete video "%%_video"', ['video' => $item->file]);
  }

}}