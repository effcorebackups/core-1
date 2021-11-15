<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
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
  public $thumbnails_is_allowed = true;
  public $thumbnails = [];

  function items_set($id, $items) {
    if ($this->thumbnails_is_allowed)
      if (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[1]['function'] === 'on_button_click_insert')
        foreach ($items as $c_item)
          if (media::media_class_get($c_item->type) === 'picture')
            if (media::is_type_for_thumbnail($c_item->type))
              if ($c_item->get_current_state() === 'pre')
                  $c_item->container_picture_make($this->thumbnails);
    parent::items_set($id, $items);
  }

  ###########################
  ### static declarations ###
  ###########################

  static function widget_manage_action_text_get($field, $item, $id, $scope) {
    if ($field->thumbnails_is_allowed) {
      $thumbnail_markup = new markup_simple('img', ['src' => '/'.$item->get_current_path(true).'?thumb=small', 'alt' => new text('thumbnail'), 'width' => '44', 'height' => '44', 'data-type' => 'thumbnail'], +450);
           return new node([], [$thumbnail_markup, new text('picture "%%_picture"', ['picture' => $item->file])]);
    } else return new node([], [                   new text('picture "%%_picture"', ['picture' => $item->file])]);
  }

}}