<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_file_picture extends field_file {

  public $title = 'Picture';
  public $item_title = 'Picture';
  public $attributes = ['data-type' => 'file-picture'];
  public $element_attributes = [
    'type' => 'file',
    'name' => 'picture'];
  public $max_file_size = '500K';
  public $types_allowed = [
    'jpg'  => 'jpg',
    'jpeg' => 'jpeg',
    'png'  => 'png',
    'gif'  => 'gif'];
  public $thumbnails_is_visible = true;
  public $thumbnails_allowed = [];

  protected function items_set($id, $items) {
    if (count($this->thumbnails_allowed)) {
      if (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[1]['function'] === 'on_values_pre_insert') {
        foreach ($items as $c_item) {
          if ($c_item->get_current_state() === 'pre') {
            if (media::is_type_for_thumbnail($c_item->type)) {
              $c_item->container_picture_make($this->thumbnails_allowed);
            }
          }
        }
      }
    }
    parent::items_set($id, $items);
  }

  protected function pool_manager_action_insert_get_field_text($item, $id, $type) {
    if ($this->thumbnails_is_visible) {
      $thumbnail_markup = new markup_simple('img', ['src' => '/'.$item->get_current_path(true).'?thumb=small', 'alt' => new text('thumbnail'), 'width' => '44', 'height' => '44', 'data-type' => 'thumbnail'], +450);
           return new node([], [$thumbnail_markup, new text('delete picture "%%_picture"', ['picture' => $item->file])]);
    } else return new node([], [                   new text('delete picture "%%_picture"', ['picture' => $item->file])]);
  }

}}