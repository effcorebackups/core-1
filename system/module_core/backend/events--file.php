<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use const \effcore\dir_dynamic;
          use const \effcore\dir_root;
          use const \effcore\dir_system;
          use const \effcore\nl;
          use \effcore\console;
          use \effcore\core;
          use \effcore\event;
          use \effcore\file;
          use \effcore\locale;
          use \effcore\media;
          use \effcore\module;
          use \effcore\timer;
          use \effcore\token;
          use \effcore\url;
          abstract class events_file {

  static function on_load_dynamic($event, $type_info, &$file) {
    $data = token::apply($file->load());
    $etag = core::hash_get_etag($data);

  # send header '304 Not Modified' if the data has no changes
    if (isset($_SERVER['HTTP_IF_NONE_MATCH']) &&
              $_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
      header('HTTP/1.1 304 Not Modified');
      console::log_store();
      exit();
    }

  # send result data
    $result = $data;
    timer::tap('total');
    if (module::is_enabled('test')) {
      header('X-Time-total: '.locale::format_msecond(
        timer::period_get('total', 0, 1)
      ));
    }
    if ($file->type === 'cssd' ||
        $file->type === 'jsd') {
      if (console::visible_mode_get() === console::is_visible_for_everyone) {
        $result.= nl.'/*'.nl.console::text_get().nl.'*/'.nl;
      }
    }
    header('Content-Length: '.strlen($result));
    header('Cache-Control: private, no-cache');
    header('Accept-Ranges: none');
    header('Etag: '.$etag);
    if (!empty($type_info->headers)) {
      foreach ($type_info->headers as $c_key => $c_value) {
        header($c_key.': '.$c_value);
      }
    }
    print $result;
    console::log_store();
    exit();
  }

  # range support:
  # ┌────────────────────────────────────────┬───┐
  # │ header                                 │   │
  # ╞════════════════════════════════════════╪═══╡
  # │ Range: bytes=int-                      │ + │
  # │ Range: bytes=int-int                   │ + │
  # │ Range: bytes=int-int, int-int          │ - │
  # │ Range: bytes=int-int, int-int, int-int │ - │
  # │ Range: bytes=-<-length>                │ - │
  # └────────────────────────────────────────┴───┘

  # http ranges limits:
  # ─────────────────────────────────────────────────────────────────────
  #
  #    ┌┬┬┬┬┬┬┬┬┐
  #    ┝┷┷┷┷┷┷┷┷┿━━━━━━━━━━━━━━━━━━━━━┥
  #   0│min     │max                  │length
  #
  #
  #               ┌┬┬┬┬┬┬┬┬┐
  #    ┝━━━━━━━━━━┿┷┷┷┷┷┷┷┷┿━━━━━━━━━━┥
  #   0│       min│        │max       │length
  #
  #
  #                         ┌┬┬┬┬┬┬┬┬┐
  #    ┝━━━━━━━━━━━━━━━━━━━━┿┷┷┷┷┷┷┷┷┿┥
  #   0│                 min│     max││length
  #
  #
  # .....................................................................
  #
  #    0 ≤ min ≤ max < length
  #
  # ─────────────────────────────────────────────────────────────────────

  static function on_load_static($event, $type_info, &$file) {
    $last_modified = gmdate('D, d M Y H:i:s', filemtime($file->path_get())).' GMT';

  # send header '304 Not Modified' if the data has not changed
    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&
              $_SERVER['HTTP_IF_MODIFIED_SINCE'] === $last_modified) {
      header('HTTP/1.1 304 Not Modified');
      console::log_store();
      exit();
    }

  # ranges
    $length = filesize($file->path_get());
    $ranges = core::server_get_http_range();
    if ($ranges->has_range) {
      $min = $ranges->min;
      $max = $ranges->max;
      if ($min === null) {header('HTTP/1.1 416 Requested Range Not Satisfiable'); exit();}
      if ($max === null || $max >= $length) $max = $length - 1;
      if (!(0 <= $min &&
                 $min <= $max &&
                         $max < $length)) {header('HTTP/1.1 416 Requested Range Not Satisfiable'); exit();}
      header('HTTP/1.1 206 Partial Content');
      header('Content-Range: bytes '.$min.'-'.$max.'/'.$length);
    } else {
      $min = 0;
      $max = $length - 1;
    }

  # send headers
    header('Content-Length: '.($max - $min + 1));
    header('Accept-Ranges: bytes');
    header('Cache-Control: private, no-cache');
    header('Last-Modified: '.$last_modified);
    if (!empty($type_info->headers)) {
      foreach ($type_info->headers as $c_key => $c_value) {
        header($c_key.': '.$c_value);
      }
    }

  # send result data
    if ($resource = fopen($file->path_get(), 'rb')) {
      $c_print_length = $min;
      if (fseek($resource, $min) === 0) {
        while (!feof($resource)) {
          $c_data = fread($resource, 1024);
          for ($i = 0; $i < strlen($c_data); $i++, $c_print_length++) {
            if ($c_print_length > $max) break 2;
            print $c_data[$i];
          }
        }
      }
      fclose($resource);
    }
    console::log_store();
    exit();
  }

  # ─────────────────────────────────────────────────────────────────────
  # image_transparent-8bit.png.get_thumbnail  → image_transparent-8bit.small.thumb.png
  # image_transparent-24bit.png.get_thumbnail → image_transparent-24bit.small.thumb.png
  # image_transparent.gif.get_thumbnail       → image_transparent.small.thumb.gif
  # image.png.get_thumbnail                   → image.small.thumb.png
  # image.gif.get_thumbnail                   → image.small.thumb.gif
  # image.jpeg.get_thumbnail                  → image.small.thumb.jpeg
  # image.jpg.get_thumbnail                   → image.small.thumb.jpg
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
      if (media::is_type_with_thumbnail($picture->type_get())) {
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