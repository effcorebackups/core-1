<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use const \effcore\dir_dynamic;
          use const \effcore\dir_root;
          use const \effcore\dir_system;
          use \effcore\core;
          use \effcore\data;
          use \effcore\event;
          use \effcore\file;
          use \effcore\media;
          use \effcore\module;
          use \effcore\url;
          abstract class events_file {

  static function on_load_not_found($event, &$type_info, &$file, $real_path, $phase) {
    switch ($file->path_get()) {
      case dir_root.'sitemap.xml':
        $settings = module::settings_get('page');
        $file = new file(data::directory.'sitemap.xml');
        if ($file->is_exist()) {
          $type = file::types_get()[$settings->apply_tokens_for_sitemap ? 'xmld' : 'xml'];
          if ($settings->apply_tokens_for_sitemap)
               event::start('on_file_load', 'dynamic', [&$type, &$file]);
          else event::start('on_file_load', 'static',  [&$type, &$file]);
          exit();
        } break;
      case dir_root.'robots.txt':
        $settings = module::settings_get('page');
        $file = new file(data::directory.'robots.txt');
        if ($file->is_exist()) {
          $type = file::types_get()[$settings->apply_tokens_for_robots ? 'txtd' : 'txt'];
          if ($settings->apply_tokens_for_robots)
               event::start('on_file_load', 'dynamic', [&$type, &$file]);
          else event::start('on_file_load', 'static',  [&$type, &$file]);
          exit();
        } break;
      case dir_root.'favicon.ico':
        core::send_header_and_exit('moved_permanently');
    }
  }

  # ─────────────────────────────────────────────────────────────────────
  # meta                      → phar://test.picture/meta
  # test.picture              → phar://test.picture/original
  # test.picture?thumb=small  → phar://test.picture/small
  # test.picture?thumb=middle → phar://test.picture/middle
  # test.picture?thumb=big    → phar://test.picture/big
  # ─────────────────────────────────────────────────────────────────────

  const prepath_thumbnail_error = dir_system.'module_core/frontend/pictures/thumbnail-error';

  static function on_load_static_picture($event, &$type_info, &$file) {
    if ($type_info->type === 'picture') {
      $path = $file->path_get();
      $path_container    = 'phar://'.$path;
      $path_meta         = 'phar://'.$path.'/meta';
      $path_original     = 'phar://'.$path.'/original';
      $path_thumb_small  = 'phar://'.$path.'/small';
      $path_thumb_middle = 'phar://'.$path.'/middle';
      $path_thumb_big    = 'phar://'.$path.'/big';
      if (file_exists($path_meta) &&
          file_exists($path_original)) {
        $meta = unserialize(file_get_contents($path_meta));
        $type_info = file::types_get()[$meta['original']['type']];
        switch (url::get_current()->query_arg_select('thumb')) {
          case 'small' : $size = 'small';  break;
          case 'middle': $size = 'middle'; break;
          case 'big'   : $size = 'big';    break;
          default      : $size = 'original';
        }
        if ($size === 'original') {
          $file = new file($path_original);
          return true;
        }
        if ($size === 'small' ) $path_thumbnail = $path_thumb_small;
        if ($size === 'middle') $path_thumbnail = $path_thumb_middle;
        if ($size === 'big'   ) $path_thumbnail = $path_thumb_big;
        if (file_exists($path_thumbnail)) {
          $file = new file($path_thumbnail);
          return true;
        }
      # generate thumbnail and insert it into container
        if (strpos($path, dir_dynamic) === 0) {
          if (in_array($size, $meta['thumbnails_allowed'])) {
            $settings = module::settings_get('page');
            if ($size === 'small' ) $width = $settings->thumbnail_small_width;
            if ($size === 'middle') $width = $settings->thumbnail_middle_width;
            if ($size === 'big'   ) $width = $settings->thumbnail_big_width;
            $path_thumbnail_tmp = $path.'.'.$size.'.'.$meta['original']['type'];
            $result = media::picture_thumbnail_create($path_original, $path_thumbnail_tmp, $width, null, $settings->thumbnail_jpeg_quality);
            if ($result && file_exists($path_thumbnail_tmp)) {
              if (media::container_picture_thumbnail_insert($path_container, $path_thumbnail_tmp, $size)) {
                @unlink($path_thumbnail_tmp);
                $file = new file($path_thumbnail);
                return true;
              }
            }
          # show dummy if an error
            $file = new file(static::prepath_thumbnail_error.'.'.$meta['original']['type']);
          } $file = new file(static::prepath_thumbnail_error.'.'.$meta['original']['type']);
        }   $file = new file(static::prepath_thumbnail_error.'.'.$meta['original']['type']);
      } else core::send_header_and_exit('unsupported_media_type');
    }
  }

}}