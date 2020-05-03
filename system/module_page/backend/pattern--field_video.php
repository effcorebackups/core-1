<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_video extends field_file {

  public $title = 'Video';
  public $attributes = ['data-type' => 'file-video'];
  public $element_attributes = [
    'type' => 'file',
    'name' => 'video'];
  public $allowed_types = [
    'mp4' => 'mp4'
  ];

}}