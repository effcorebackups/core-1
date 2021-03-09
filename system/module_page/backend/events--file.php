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

  const prepath_file_outside_of_dynamic_directory = dir_system.'module_core/frontend/pictures/file-outside-of-dynamic-directory';
  const prepath_thumbnail_not_allowed             = dir_system.'module_core/frontend/pictures/thumbnail-not-allowed';
  const prepath_thumbnail_creation_error          = dir_system.'module_core/frontend/pictures/thumbnail-creation-error';
  const prepath_thumbnail_embedding_error         = dir_system.'module_core/frontend/pictures/thumbnail-embedding-error';

  static function on_load_not_found($event, &$type_info, &$file, $real_path, $phase) {
    switch ($file->path_get()) {
      case dir_root.'sitemap.xml':
        $settings = module::settings_get('page');
        $file = new file(data::directory.'sitemap.xml');
        if ($file->is_exists()) {
          $type = file::types_get()[$settings->apply_tokens_for_sitemap ? 'xmld' : 'xml'];
          if ($settings->apply_tokens_for_sitemap)
               event::start('on_file_load', 'dynamic', ['type_info' => &$type, 'file' => &$file]);
          else event::start('on_file_load', 'static',  ['type_info' => &$type, 'file' => &$file]);
          exit();
        } break;
      case dir_root.'robots.txt':
        $settings = module::settings_get('page');
        $file = new file(data::directory.'robots.txt');
        if ($file->is_exists()) {
          $type = file::types_get()[$settings->apply_tokens_for_robots ? 'txtd' : 'txt'];
          if ($settings->apply_tokens_for_robots)
               event::start('on_file_load', 'dynamic', ['type_info' => &$type, 'file' => &$file]);
          else event::start('on_file_load', 'static',  ['type_info' => &$type, 'file' => &$file]);
          exit();
        } break;
      case dir_root.'favicon.ico':
        core::send_header_and_exit('moved_permanently');
    }
  }

  # ─────────────────────────────────────────────────────────────────────
  # audio
  # ─────────────────────────────────────────────────────────────────────

  static function on_load_static_audio($event, &$type_info, &$file) {
    if ($type_info->type === 'audio') {
      core::send_header_and_exit('unsupported_media_type');
    }
  }

  # ─────────────────────────────────────────────────────────────────────
  # video
  # ─────────────────────────────────────────────────────────────────────
  # test.video                → phar://test.video/original
  # test.video?poster=        → phar://test.video/poster
  # test.video?poster=unknown → phar://test.video/poster
  # test.video?poster=small   → phar://test.video/poster-small
  # test.video?poster=middle  → phar://test.video/poster-middle
  # test.video?poster=big     → phar://test.video/poster-big
  # ─────────────────────────────────────────────────────────────────────

  static function on_load_static_video($event, &$type_info, &$file) {
    if ($type_info->type === 'video') {
      $path = $file->path_get();
      $path_container     = 'phar://'.$path;
      $path_meta          = 'phar://'.$path.'/meta';
      $path_original      = 'phar://'.$path.'/original';
      $path_poster        = 'phar://'.$path.'/poster';
      $path_poster_small  = 'phar://'.$path.'/poster-small';
      $path_poster_middle = 'phar://'.$path.'/poster-middle';
      $path_poster_big    = 'phar://'.$path.'/poster-big';
      if (file_exists($path_meta) &&
          file_exists($path_original)) {
        $meta = @unserialize(file_get_contents($path_meta));
        $file_types = file::types_get();
        $arg = url::get_current()->query_arg_select('poster');
        if ($arg === null                     ) $target = 'original';
        if ($arg !== null                     ) $target = 'poster';
        if ($arg !== null && $arg === 'small' ) $target = 'poster-small';
        if ($arg !== null && $arg === 'middle') $target = 'poster-middle';
        if ($arg !== null && $arg === 'big'   ) $target = 'poster-big';
      # case for video (file "original")
        if ($target === 'original') {
          if (isset(             $meta['original']['type'] ) &&
              isset($file_types[ $meta['original']['type'] ])) {
            $type_info = $file_types[$meta['original']['type']];
            $file = new file($path_original);
            return true;
          } else core::send_header_and_exit('unsupported_media_type');
        }
      # case for poster or its thumbnails
        if ($target !== 'original') {
          if (isset(             $meta['poster']['type'] ) &&
              isset($file_types[ $meta['poster']['type'] ])) {
            $type_info = $file_types[$meta['poster']['type']];
            if ($target === 'poster'       ) $path_target = $path_poster;
            if ($target === 'poster-small' ) $path_target = $path_poster_small;
            if ($target === 'poster-middle') $path_target = $path_poster_middle;
            if ($target === 'poster-big'   ) $path_target = $path_poster_big;
            if (file_exists($path_target)) {
              $file = new file($path_target);
              return true;
            }
            if (file_exists($path_poster) === false) {
              core::send_header_and_exit('file_not_found');
            }
          # generate thumbnail and insert it into container
            if (strpos($path, dir_dynamic) === 0) {
              if (!empty($meta['poster_thumbnails'])) {
                $settings = module::settings_get('page');
                if ($target === 'poster-small' ) $width = $settings->thumbnail_small_width;
                if ($target === 'poster-middle') $width = $settings->thumbnail_middle_width;
                if ($target === 'poster-big'   ) $width = $settings->thumbnail_big_width;
                $path_thumbnail_tmp = $path.'.'.$target.'.'.$meta['poster']['type'];
                $result = media::thumbnail_create($path_poster, $path_thumbnail_tmp, $width, null, $settings->thumbnail_jpeg_quality);
                if ($result && file_exists($path_thumbnail_tmp)) {
                  if (media::container_file_insert($path_container, $path_thumbnail_tmp, $target)) {
                    @unlink($path_thumbnail_tmp);
                    $file = new file($path_target);
                    return true;
                  } else $file = new file(static::prepath_thumbnail_embedding_error        .'.'.$meta['poster']['type']);
                }   else $file = new file(static::prepath_thumbnail_creation_error         .'.'.$meta['poster']['type']);
              }     else $file = new file(static::prepath_thumbnail_not_allowed            .'.'.$meta['poster']['type']);
            }       else $file = new file(static::prepath_file_outside_of_dynamic_directory.'.'.$meta['poster']['type']);
          } else core::send_header_and_exit('unsupported_media_type');
        }
      } else core::send_header_and_exit('unsupported_media_type');
    }
  }

  # ─────────────────────────────────────────────────────────────────────
  # picture
  # ─────────────────────────────────────────────────────────────────────
  # test.picture               → phar://test.picture/original
  # test.picture?thumb=        → phar://test.picture/original
  # test.picture?thumb=unknown → phar://test.picture/original
  # test.picture?thumb=small   → phar://test.picture/thumbnail-small
  # test.picture?thumb=middle  → phar://test.picture/thumbnail-middle
  # test.picture?thumb=big     → phar://test.picture/thumbnail-big
  # ─────────────────────────────────────────────────────────────────────

  static function on_load_static_picture($event, &$type_info, &$file) {
    if ($type_info->type === 'picture') {
      $path = $file->path_get();
      $path_container        = 'phar://'.$path;
      $path_meta             = 'phar://'.$path.'/meta';
      $path_original         = 'phar://'.$path.'/original';
      $path_thumbnail_small  = 'phar://'.$path.'/thumbnail-small';
      $path_thumbnail_middle = 'phar://'.$path.'/thumbnail-middle';
      $path_thumbnail_big    = 'phar://'.$path.'/thumbnail-big';
      if (file_exists($path_meta) &&
          file_exists($path_original)) {
        $meta = @unserialize(file_get_contents($path_meta));
        $file_types = file::types_get();
        if (isset(             $meta['original']['type'] ) &&
            isset($file_types[ $meta['original']['type'] ])) {
          $type_info = $file_types[$meta['original']['type']];
          if (media::is_type_for_thumbnail($type_info->type)) {
            switch (url::get_current()->query_arg_select('thumb')) {
              case 'small' : $size = 'small';  break;
              case 'middle': $size = 'middle'; break;
              case 'big'   : $size = 'big';    break;
              default      : $size = 'original';
            }
          # case for picture (file "original")
            if ($size === 'original') {
              $file = new file($path_original);
              return true;
            }
          # case for thumbnails
            if ($size === 'small' ) $path_thumbnail = $path_thumbnail_small;
            if ($size === 'middle') $path_thumbnail = $path_thumbnail_middle;
            if ($size === 'big'   ) $path_thumbnail = $path_thumbnail_big;
            if (file_exists($path_thumbnail)) {
              $file = new file($path_thumbnail);
              return true;
            }
          # generate thumbnail and insert it into container
            if (isset($meta['thumbnails']) && is_array($meta['thumbnails'])) {
              if (strpos($path, dir_dynamic) === 0) {
                if (isset($meta['thumbnails'][$size])) {
                  $settings = module::settings_get('page');
                  if ($size === 'small' ) $width = $settings->thumbnail_small_width;
                  if ($size === 'middle') $width = $settings->thumbnail_middle_width;
                  if ($size === 'big'   ) $width = $settings->thumbnail_big_width;
                  $path_thumbnail_tmp = $path.'.thumbnail-'.$size.'.'.$meta['original']['type'];
                  $result = media::thumbnail_create($path_original, $path_thumbnail_tmp, $width, null, $settings->thumbnail_jpeg_quality);
                  if ($result && file_exists($path_thumbnail_tmp)) {
                    if (media::container_file_insert($path_container, $path_thumbnail_tmp, 'thumbnail-'.$size)) {
                      @unlink($path_thumbnail_tmp);
                      $file = new file($path_thumbnail);
                      return true;
                    } else $file = new file(static::prepath_thumbnail_embedding_error        .'.'.$meta['original']['type']);
                  }   else $file = new file(static::prepath_thumbnail_creation_error         .'.'.$meta['original']['type']);
                }     else $file = new file(static::prepath_thumbnail_not_allowed            .'.'.$meta['original']['type']);
              }       else $file = new file(static::prepath_file_outside_of_dynamic_directory.'.'.$meta['original']['type']);
            }         else core::send_header_and_exit('unsupported_media_type');
          }           else core::send_header_and_exit('unsupported_media_type');
        }             else core::send_header_and_exit('unsupported_media_type');
      }               else core::send_header_and_exit('unsupported_media_type');
    }
  }

}}