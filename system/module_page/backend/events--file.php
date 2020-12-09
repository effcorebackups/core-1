<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use const \effcore\dir_dynamic;
          use const \effcore\dir_system;
          use \effcore\core;
          use \effcore\event;
          use \effcore\file;
          use \effcore\media;
          use \effcore\url;
          abstract class events_file {

  # ─────────────────────────────────────────────────────────────────────
  # picture.jpg.get_thumbnail              → picture.small.thumb.jpg
  # picture.jpg.get_thumbnail?size=small   → picture.small.thumb.jpg
  # picture.jpg.get_thumbnail?size=middle  → picture.middle.thumb.jpg
  # picture.jpg.get_thumbnail?size=big     → picture.big.thumb.jpg
  # picture.jpeg.get_thumbnail             → picture.small.thumb.jpeg
  # picture.jpeg.get_thumbnail?size=small  → picture.small.thumb.jpeg
  # picture.jpeg.get_thumbnail?size=middle → picture.middle.thumb.jpeg
  # picture.jpeg.get_thumbnail?size=big    → picture.big.thumb.jpeg
  # picture.png.get_thumbnail              → picture.small.thumb.png
  # picture.png.get_thumbnail?size=small   → picture.small.thumb.png
  # picture.png.get_thumbnail?size=middle  → picture.middle.thumb.png
  # picture.png.get_thumbnail?size=big     → picture.big.thumb.png
  # picture.gif.get_thumbnail              → picture.small.thumb.gif
  # picture.gif.get_thumbnail?size=small   → picture.small.thumb.gif
  # picture.gif.get_thumbnail?size=middle  → picture.middle.thumb.gif
  # picture.gif.get_thumbnail?size=big     → picture.big.thumb.gif
  # ─────────────────────────────────────────────────────────────────────

  const prepath_media_error_thumbnail_creation_error = dir_system.'module_core/frontend/pictures/media-error-thumbnail-creation-error';
  const prepath_media_error_extensions_not_loaded    = dir_system.'module_core/frontend/pictures/media-error-extensions-not-loaded';
  const thumbnail_jpeg_quality = 90;
  const thumbnail_small_width = 44;
  const thumbnail_middle_width = 300;
  const thumbnail_big_width = 600;

  static function on_load_virtual_get_thumbnail($event, $type_info, &$file) {
    if ($type_info->type === 'get_thumbnail') {
      switch (url::get_current()->query_arg_select('size')) {
        case 'small' : $size = 'small';  $size_int = static::thumbnail_small_width;  break;
        case 'middle': $size = 'middle'; $size_int = static::thumbnail_middle_width; break;
        case 'big'   : $size = 'big';    $size_int = static::thumbnail_big_width;    break;
        default      : $size = 'small';  $size_int = static::thumbnail_small_width;
      }
      $picture = new file($file->dirs_get().$file->name_get());
      $real_path = core::validate_realpath($picture->path_get());
      if ($real_path === false)                          core::send_header_and_exit('file_not_found');
      if ($real_path !== $picture->path_get())           core::send_header_and_exit('file_not_found');
      if (strpos($real_path, dir_dynamic) !== 0)         core::send_header_and_exit('file_not_found');
      if (!is_file    ($picture->path_get()))            core::send_header_and_exit('file_not_found');
      if (!is_readable($picture->path_get()))            core::send_header_and_exit('access_forbidden');
      if (substr($picture->name_get(), -6) === '.thumb') core::send_header_and_exit('access_forbidden');
      if (media::is_picture_type_with_thumbnail($picture->type_get())) {
        $thumbnail = new file($picture->path_get());
        $thumbnail->name_set($thumbnail->name_get().'.'.$size.'.thumb');
        $file_types = file::types_get();
        if ($thumbnail->is_exist()) {
          event::start('on_file_load', 'static', [$file_types[$thumbnail->type_get()], &$thumbnail]);
          exit();
        }
        if (extension_loaded('exif') && extension_loaded('gd')) {
          $result = media::picture_thumbnail_create($picture->path_get(), $thumbnail->path_get(), $size_int, null, static::thumbnail_jpeg_quality);
          if (!$result) $thumbnail = new file(static::prepath_media_error_thumbnail_creation_error.'.'.$thumbnail->type_get());
          event::start('on_file_load', 'static', [$file_types[$thumbnail->type_get()], &$thumbnail]);
          exit();
        } else {
          $thumbnail = new file(static::prepath_media_error_extensions_not_loaded.'.'.$thumbnail->type_get());
          event::start('on_file_load', 'static', [$file_types[$thumbnail->type_get()], &$thumbnail]);
          exit();
        }
      } else {
        core::send_header_and_exit('unsupported_media_type');
      }
    }
  }

}}