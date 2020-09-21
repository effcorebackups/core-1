<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class media {

  static function picture_thumbnail_create($src_path, $dst_path, $dst_w = 100, $dst_h = null, $jpeg_quality = -1) {
    $type = @exif_imagetype($src_path);
    if ($type !== false) {
      if ($type === IMAGETYPE_GIF  && function_exists('imagecreatefromgif' )) $src_resource = @imagecreatefromgif ($src_path);
      if ($type === IMAGETYPE_JPEG && function_exists('imagecreatefromjpeg')) $src_resource = @imagecreatefromjpeg($src_path);
      if ($type === IMAGETYPE_PNG  && function_exists('imagecreatefrompng' )) $src_resource = @imagecreatefrompng ($src_path);
      if (isset($src_resource) && is_resource($src_resource)) {
        $src_w = @imagesx($src_resource);
        $src_h = @imagesy($src_resource);
        if (is_int($src_w) && $src_w > 0 &&
            is_int($src_h) && $src_w > 0) {
          if ($dst_w || $dst_h) {
            if (!$dst_h) $dst_h = (int)($src_h / ($src_w / $dst_w));
            if (!$dst_w) $dst_w = (int)($src_w / ($src_h / $dst_h));
            $dst_resource = @imagecreatetruecolor($dst_w, $dst_h);
            if (is_resource($dst_resource)) {
              $dst_file = new file($dst_path);
              @imagecolortransparent($dst_resource, imagecolorallocate($dst_resource, 0, 0, 0));
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