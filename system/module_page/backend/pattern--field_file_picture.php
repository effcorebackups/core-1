<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_file_picture extends field_file {

  public $title = 'Picture';
  public $attributes = ['data-type' => 'file-picture'];
  public $element_attributes = [
    'type' => 'file',
    'name' => 'picture'];
  public $max_file_size = '500K';
  public $allowed_types = [
    'jpg'  => 'jpg',
    'jpeg' => 'jpeg',
    'png'  => 'png',
    'gif'  => 'gif'
  ];

  protected function pool_manager_action_insert_get_field_text($item, $id, $type) {
    $file = new file($item->get_current_path());
    $thumbnail = new markup_simple('img', ['src' => '/'.$file->path_get_relative().'.get_thumbnail', 'alt' => new text('thumbnail'), 'width' => '50', 'data-type' => 'thumbnail']);
    return new node([], [$thumbnail, new text('delete picture "%%_picture"', ['picture' => $item->file])]);
  }

}}