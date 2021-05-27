<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          trait widget_files_pictures__shared {

  public $thumbnails_is_allowed = true;
  public $thumbnails = [
    'small'  => 'small',
    'middle' => 'middle'];
# ─────────────────────────────────────────────────────────────────────
  public $picture_default_settings = [
    'title'  => 'click to open in new window',
    'alt'    => 'thumbnail',
    'target' => 'widget_files-pictures-items'
  ];

  ###########################
  ### static declarations ###
  ###########################

  static function widget_manage_picture_item_make(&$widget, &$item, $c_row_id, &$root) {
    if (media::media_class_get($item->object->type) === 'picture') {
      if (!empty($item->settings['data-thumbnails-is-embedded'])) {
        $widget->child_insert(new markup_simple('img', ['src' => '/'.$item->object->get_current_path(true).'?thumb=small', 'alt' => new text('thumbnail'), 'width' => '44', 'height' => '44', 'data-type' => 'thumbnail'], +450), 'thumbnail');
      }
    }
  }

}}