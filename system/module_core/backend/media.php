<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class media {

  const iptc_object_name = 5;
  const iptc_edit_status = 7;
  const iptc_priority = 10;
  const iptc_category = 15;
  const iptc_supplemental_category = 20;
  const iptc_fixture_identifier = 22;
  const iptc_keywords = 25;
  const iptc_release_date = 30;
  const iptc_release_time = 35;
  const iptc_special_instructions = 40;
  const iptc_reference_service = 45;
  const iptc_reference_date = 47;
  const iptc_reference_number = 50;
  const iptc_created_date = 55;
  const iptc_created_time = 60;
  const iptc_originating_program = 65;
  const iptc_program_version = 70;
  const iptc_object_cycle = 75;
  const iptc_byline = 80;
  const iptc_byline_title = 85;
  const iptc_city = 90;
  const iptc_province_state = 95;
  const iptc_country_code = 100;
  const iptc_country = 101;
  const iptc_original_transmission_reference = 103;
  const iptc_headline = 105;
  const iptc_credit = 110;
  const iptc_source = 115;
  const iptc_copyright_string = 116;
  const iptc_caption = 120;
  const iptc_local_caption = 121;

  static function iptc_make_tag($key, $value) {
    $length = strlen($value);
    $result = chr(0x1c).chr(0x2).chr($key);
    if ($length < 2 ** 15)
         $result.= chr($length >> 8).chr($length & 0xff);
    else $result.= chr(0x80).chr(0x4).
                   chr(($length >> 24) & 0xff).
                   chr(($length >> 16) & 0xff).
                   chr(($length >>  8) & 0xff).
                   chr(($length      ) & 0xff);
    return $result.$value;
  }

  static function is_picture_type_with_thumbnail($type) {
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

  static function picture_thumbnails_cleaning($path, $name_prefix = '') {
    if (file_exists($path)) {
      foreach (file::select_recursive($path, '%^.*/'.preg_quote($name_prefix).'.*\\.thumb\\.(jpg|jpeg|png|gif)$%S') as $c_path => $c_file) {
        @unlink($c_path);
      }
    }
  }

}}