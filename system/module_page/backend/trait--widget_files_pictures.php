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

  function widget_manage_picture_item_make(&$widget, &$item, $c_row_id) {
    if (media::media_class_get($item->object->type) === 'picture') {
      if (!empty($item->settings['data-thumbnails-is-embedded'])) {
        $widget->child_insert(new markup_simple('img', ['src' => '/'.$item->object->get_current_path(true).'?thumb=small', 'alt' => new text('thumbnail'), 'width' => '44', 'height' => '44', 'data-type' => 'thumbnail'], +450), 'thumbnail');
      }
    }
  }

  function on_file_prepare_picture($form, $npath, $button, &$items, &$new_item) {
    $pre_path = temporary::directory.'validation/'.$form->validation_cache_date_get().'/'.$form->validation_id.'-'.$this->name_get_complex().'-'.core::array_key_last($items).'.'.$new_item->object->type;
    if ($new_item->object->move_tmp_to_pre($pre_path)) {
      $new_item->settings = $this->picture_default_settings;
      $new_item->settings['data-thumbnails-is-embedded'] = false;
      if ($this->thumbnails_is_allowed) {
        if (media::media_class_get($new_item->object->type) === 'picture') {
          if (media::is_type_for_thumbnail($new_item->object->type)) {
            if ($new_item->object->container_picture_make($this->thumbnails)) {
              $new_item->settings['data-thumbnails-is-embedded'] = true;
            }
          }
        }
      }
      return true;
    }
  }

}}