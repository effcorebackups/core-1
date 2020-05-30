<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_gallery_pictures extends widget_files {

  public $title = 'Pictures';
  public $item_title = 'Picture';
  public $attributes = ['data-type' => 'items-info-files-picture'];
  public $name_complex = 'widget_gallery_pictures';
# ─────────────────────────────────────────────────────────────────────
  public $upload_dir = 'galleries/';
  public $fixed_name = 'pictures-%%_instance_id_context-%%_item_id_context';
  public $max_file_size = '500K';
  public $allowed_types = [
    'jpg'  => 'jpg',
    'jpeg' => 'jpeg',
    'png'  => 'png',
    'gif'  => 'gif'
  ];

}}