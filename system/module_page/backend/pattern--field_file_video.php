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
  public $allowed_types = [
    'mp4' => 'mp4'
  ];

}}