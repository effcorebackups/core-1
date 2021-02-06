<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class media {

  static function container_picture_make($src_path, $dst_path, $meta = []) {
    try {
      @unlink($dst_path);
      $container = new \PharData($dst_path, 0, null, \Phar::TAR);
      $container->startBuffering();
      $container['meta'] = serialize($meta);
      $container->addFile($src_path, 'original');
      $container->stopBuffering();
      return $container;
    } catch (Exception $e) {
      return;
    }
  }

  static function container_picture_thumbnail_insert($container_path, $thumbnail_path, $thumbnail_path_local) {
    try {
      $container = new \PharData($container_path, 0, null, \Phar::TAR);
      $container->addFile($thumbnail_path, $thumbnail_path_local);
      return $container;
    } catch (Exception $e) {
      return;
    }
  }

  static function media_class_get($type) {
    switch ($type) {
      case 'mp3'    : return 'audio';
      case 'mp4'    : return 'video';
      case 'picture': return 'picture';
      case 'png'    : return 'picture';
      case 'gif'    : return 'picture';
      case 'jpg'    : return 'picture';
      case 'jpeg'   : return 'picture';
      case 'svg'    : return 'picture';
    }
  }

  static function is_type_for_picture_thumbnail($type) {
    if ($type === 'jpg' ) return true;
    if ($type === 'jpeg') return true;
    if ($type === 'png' ) return true;
    if ($type === 'gif' ) return true;
  }

  static function picture_thumbnail_create($src_path, $dst_path, $dst_w = 100, $dst_h = null, $jpeg_quality = -1) {
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