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
  public $allowed_types = [
    'mp3' => 'mp3'
  ];

  protected function pool_manager_action_insert_get_field_text($item, $id, $type) {
    return new text('delete audio "%%_audio"', ['audio' => $item->file]);
  }

}}