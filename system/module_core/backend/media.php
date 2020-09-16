<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class media {

  static function picture_thumbnail_create($src_path, $dst_path, $dst_w = 100, $result_format = null) {
    $type = @exif_imagetype($src_path);
    if ($type !== false) {
      if ($type === IMAGETYPE_GIF  && function_exists('imagecreatefromgif' )) $src_resource = @imagecreatefromgif ($src_path);
      if ($type === IMAGETYPE_JPEG && function_exists('imagecreatefromjpeg')) $src_resource = @imagecreatefromjpeg($src_path);
      if ($type === IMAGETYPE_PNG  && function_exists('imagecreatefrompng' )) $src_resource = @imagecreatefrompng ($src_path);
      if (isset($src_resource) &&
                $src_resource) {
        $src_w = imagesx($src_resource);
        $src_h = imagesy($src_resource);
        $dst_h = (int)($src_h / ($src_w / $dst_w));
        $dst_resource = @imagecreatetruecolor($dst_w, $dst_h);
        @imagecolortransparent($dst_resource, imagecolorallocate($dst_resource, 0, 0, 0));
        @imagealphablending   ($dst_resource, $type === IMAGETYPE_GIF);
        @imagesavealpha       ($dst_resource, true);
        if ($dst_resource) {
          @imagecopyresampled($dst_resource, $src_resource, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
          if ((($type === IMAGETYPE_PNG  && $result_format === null) || $result_format === 'png' ) && function_exists('imagepng' )) $result = @imagepng ($dst_resource, $dst_path.'.png' );
          if ((($type === IMAGETYPE_JPEG && $result_format === null) || $result_format === 'jpeg') && function_exists('imagejpeg')) $result = @imagejpeg($dst_resource, $dst_path.'.jpeg');
          if ((($type === IMAGETYPE_GIF  && $result_format === null) || $result_format === 'gif' ) && function_exists('imagegif' )) $result = @imagegif ($dst_resource, $dst_path.'.gif' );
          @imagedestroy($dst_resource);
          return $result ?? null;
        }
      }
    }
  }

}}