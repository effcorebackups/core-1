<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_files_media extends widget_files {

  ###########################
  ### static declarations ###
  ###########################

  static function complex_value_to_markup($complex) {  
    $decorator = new decorator;
    $decorator->id = 'widget_files-pictures-items';
    $decorator->view_type = 'template';
    $decorator->template = 'content';
    $decorator->template_row = 'gallery_row';
    $decorator->template_row_mapping = core::array_kmap(['num', 'type', 'children']);
    if ($complex) {
      core::array_sort_by_weight($complex);
      foreach ($complex as $c_row_id => $c_item) {
        $c_file = new file($c_item->object->get_current_path());
        switch ($c_item->object->type) {
          case 'picture':
          case 'png':
          case 'gif':
          case 'jpg':
          case 'jpeg':
            $c_item_type = 'picture';
            $c_item_markup = new markup('a', ['data-type' => 'picture-wrapper', 'title' => new text('click to open in new window'), 'target' => 'widget_files-pictures-items', 'href' => '/'.$c_file->path_get_relative().'?thumb=big'], new markup_simple('img', ['src' => '/'.$c_file->path_get_relative().'?thumb=middle', 'alt' => new text('thumbnail')]));
            break;
          case 'mp3':
            $c_item_type = 'audio';
            $c_item_markup = new markup('audio', ['src' => '/'.$c_file->path_get_relative(), 'controls' => true, 'preload' => 'metadata', 'data-player-name' => 'default', 'data-player-timeline-is-visible' => 'true']);
            break;
          default:
            continue 2;
        }
        $decorator->data[$c_row_id] = [
          'type'     => ['value' => $c_item_type  ],
          'num'      => ['value' => $c_row_id     ],
          'children' => ['value' => $c_item_markup]
        ];
      }
    }
    return $decorator;
  }

}}