<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_picture extends field_file {

  public $title = 'Picture';
  public $attributes = ['data-type' => 'file-picture'];
  public $element_attributes = [
    'type' => 'file',
    'name' => 'picture'];
  public $allowed_types = [
    'jpg'  => 'jpg',
    'jpeg' => 'jpeg',
    'png'  => 'png',
    'gif'  => 'gif'
  ];

}}