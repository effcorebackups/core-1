<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_files_videos extends widget_files {

  public $title = 'Videos';
  public $item_title = 'Video';
  public $attributes = ['data-type' => 'items-files-videos'];
  public $name_complex = 'widget_files_videos';
# ─────────────────────────────────────────────────────────────────────
  public $upload_dir = 'videos/';
  public $fixed_name = 'video-multiple-%%_item_id_context';
  public $max_file_size = '50M';
  public $types_allowed = [
    'mp4' => 'mp4'
  ];

}}