<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class media {

  const lock_life_time         = 3;
  const lock_checks_sleep_time = 1;
  const lock_checks_count      = 10;

  static function container_make($file_path, $container_path, $meta = []) {
    try {
      @unlink($container_path);
      $fl = new file($container_path);
      for ($i = 0; $i < static::lock_checks_count; $i++)
        if ($fl->lock_is_set(static::lock_life_time) === file::lock_is_active)
             sleep(static::lock_checks_sleep_time);
        else break;
      $fl->lock_insert();
      $container = new \PharData($container_path, 0, null, \Phar::TAR);
      $container->startBuffering();
      $container->addFromString('meta', str_pad(serialize($meta), 2048)); # str_pad reserves space for growing 'meta' file in parallel processes (Phar bug in caching its files)
      $container->addFile($file_path, 'original');
      $container->stopBuffering();
      $fl->lock_delete();
      return $container;
    } catch (Exception $e) {
      return;
    }
  }

  static function container_file_insert($container_path, $file_path, $path_local) {
    try {
      $fl = new file($container_path);
      for ($i = 0; $i < static::lock_checks_count; $i++)
        if ($fl->lock_is_set(static::lock_life_time) === file::lock_is_active)
             sleep(static::lock_checks_sleep_time);
        else break;
      $fl->lock_insert();
      $container = new \PharData($container_path, 0, null, \Phar::TAR);
      $container->startBuffering();
      $meta = unserialize($container['meta']->getContent());
      $file = new file($file_path);
      if ($meta) {
        $meta[$path_local]['type'] = $file->type_get();
        $meta[$path_local]['mime'] = $file->mime_get();
        $meta[$path_local]['size'] = $file->size_get();
        $container->addFromString('meta', str_pad(serialize($meta), 2048)); # str_pad reserves space for growing 'meta' file in parallel processes (Phar bug in caching its files)
        $container->addFile($file_path, $path_local);
        $container->stopBuffering();
        $fl->lock_delete();
        return $container;
      }
    } catch (Exception $e) {
      return;
    }
  }

  static function media_class_get($type) {
    switch ($type) {
      case 'audio'  : return 'audio';
      case 'mp3'    : return 'audio';
      case 'video'  : return 'video';
      case 'mp4'    : return 'video';
      case 'picture': return 'picture';
      case 'png'    : return 'picture';
      case 'gif'    : return 'picture';
      case 'jpg'    : return 'picture';
      case 'jpeg'   : return 'picture';
      case 'svg'    : return 'picture';
    }
  }

  static function is_type_for_thumbnail($type) {
    if ($type === 'jpg' ) return true;
    if ($type === 'jpeg') return true;
    if ($type === 'png' ) return true;
    if ($type === 'gif' ) return true;
  }

  static function thumbnail_create($src_path, $dst_path, $dst_w = 100, $dst_h = null, $jpeg_quality = -1) {
    $type = @exif_imagetype($src_path);
    if ($type !== false) {
      if ($type === IMAGETYPE_GIF  && function_exists('imagecreatefromgif' )) $src_resource = @imagecreatefromgif ($src_path);
      if ($type === IMAGETYPE_JPEG && function_exists('imagecreatefromjpeg')) $src_resource = @imagecreatefromjpeg($src_path);
      if ($type === IMAGETYPE_PNG  && function_exists('imagecreatefrompng' )) $src_resource = @imagecreatefrompng ($src_path);
      if (isset($src_resource) && $src_resource) {
        $src_w = @imagesx($src_resource);
        $src_h = @imagesy($src_resource);
        if (is_int($src_w) && $src_w > 0 &&
            is_int($src_h) && $src_w > 0) {
          if ($dst_w || $dst_h) {
            if (!$dst_h) $dst_h = (int)($src_h / ($src_w / $dst_w));
            if (!$dst_w) $dst_w = (int)($src_w / ($src_h / $dst_h));
            $dst_resource = @imagecreatetruecolor($dst_w, $dst_h);
            if ($dst_resource) {
              $dst_file = new file($dst_path);
              if ($dst_file->type === 'jpg' || # fill with white background in JPG for beautiful degrade of PNG/GIF transparency in $src_resource
                  $dst_file->type === 'jpeg') @imagefilledrectangle ($dst_resource, 0, 0, $dst_w - 1, $dst_h - 1, imagecolorallocate($dst_resource, 255, 255, 255));
              if ($dst_file->type === 'gif' ) @imagecolortransparent($dst_resource,                               imagecolorallocate($dst_resource,   0,   0,   0));
              if ($dst_file->type === 'png' ) @imagealphablending   ($dst_resource, false);
              if ($dst_file->type === 'png' ) @imagesavealpha       ($dst_resource, true);
              @imagecopyresampled($dst_resource, $src_resource, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
              if ($dst_file->type === 'png'  && function_exists('imagepng' )) $result = @imagepng ($dst_resource, $dst_file->dirs.$dst_file->name.'.png'                );
              if ($dst_file->type === 'jpg'  && function_exists('imagejpeg')) $result = @imagejpeg($dst_resource, $dst_file->dirs.$dst_file->name.'.jpg',  $jpeg_quality);
              if ($dst_file->type === 'jpeg' && function_exists('imagejpeg')) $result = @imagejpeg($dst_resource, $dst_file->dirs.$dst_file->name.'.jpeg', $jpeg_quality);
              if ($dst_file->type === 'gif'  && function_exists('imagegif' )) $result = @imagegif ($dst_resource, $dst_file->dirs.$dst_file->name.'.gif'                );
              @imagedestroy($dst_resource);
              return $result ?? null;
            }
          }
        }
      }
    }
  }

}}