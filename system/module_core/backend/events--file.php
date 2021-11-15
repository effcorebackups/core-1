<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use const \effcore\nl;
          use \effcore\console;
          use \effcore\core;
          use \effcore\locale;
          use \effcore\module;
          use \effcore\request;
          use \effcore\response;
          use \effcore\timer;
          use \effcore\token;
          abstract class events_file {

  static function on_load_not_found($event, &$type_info, &$file, $real_path, $phase) {
    response::send_header_and_exit('file_not_found');
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  static function on_load_dynamic($event, &$type_info, &$file) {
    $data = token::apply($file->load());
    $etag = core::hash_get($data);

  # send header '304 Not Modified' if the data has no changes
    if (isset($_SERVER['HTTP_IF_NONE_MATCH']) &&
              $_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
      header('HTTP/1.1 304 Not Modified');
      exit();
    }

  # send result data
    $result = $data;
    timer::tap('total');
    if (module::is_enabled('test')) {
      header('X-PHP-Memory-usage: '.memory_get_usage(true));
      header('X-Time-total: '.timer::period_get('total', 0, 1));
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
    exit();
  }

  # ┌────────────────────────────────────────╥─────────┐
  # │ headers                                ║ support │
  # ╞════════════════════════════════════════╬═════════╡
  # │ Range: bytes=int-                      ║    +    │
  # │ Range: bytes=int-int                   ║    +    │
  # │ Range: bytes=int-int, int-int          ║    -    │
  # │ Range: bytes=int-int, int-int, int-int ║    -    │
  # │ Range: bytes=-<-length>                ║    -    │
  # └────────────────────────────────────────╨─────────┘

  # ─────────────────────────────────────────────────────────────────────
  # http ranges limits:
  # ═════════════════════════════════════════════════════════════════════
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
  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
  #
  #    0 ≤ min ≤ max < length
  #
  # ─────────────────────────────────────────────────────────────────────

  const read_block_size = 1024;

  static function on_load_static($event, &$type_info, &$file) {
    $last_modified = gmdate('D, d M Y H:i:s', filemtime($file->path_get())).' GMT';

  # send header '304 Not Modified' if the data has not changed
    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&
              $_SERVER['HTTP_IF_MODIFIED_SINCE'] === $last_modified) {
      header('HTTP/1.1 304 Not Modified');
      exit();
    }

  # send headers
    header('Accept-Ranges: bytes');
    header('Cache-Control: private, no-cache');
    header('Last-Modified: '.$last_modified);
    if (!empty($type_info->headers)) {
      foreach ($type_info->headers as $c_key => $c_value) {
        header($c_key.': '.$c_value);
      }
    }

  # if the file is empty
    $length = filesize($file->path_get());
    if ($length === 0) {
      header('Content-Length: 0');
      exit();
    }

  # if no ranges are specified
    $ranges = request::http_range_get();
    if ($ranges->has_range !== true) {
      header('Content-Length: '.$length);
      if ($handle = fopen($file->path_get(), 'rb')) {
        fseek($handle, 0, SEEK_SET);
        fpassthru($handle);
        fclose($handle);
      }
      exit();
    }

  # if ranges are specified
    if ($ranges->has_range === true) {
      $min = $ranges->min;
      $max = $ranges->max;
      if ($min === null) {header('HTTP/1.1 416 Requested Range Not Satisfiable'); exit();}
      if ($max === null || $max >= $length) $max = $length - 1;
      if (!(0 <= $min &&
                 $min <= $max &&
                         $max < $length)) {header('HTTP/1.1 416 Requested Range Not Satisfiable'); exit();}
      header('HTTP/1.1 206 Partial Content');
      header('Content-Range: bytes '.$min.'-'.$max.'/'.$length);
      header('Content-Length: '.($max + 1 - $min));
      $cur = $min;
      if ($handle = fopen($file->path_get(), 'rb')) {
        fseek($handle, $min, SEEK_SET);
        while (strlen($c_data = fread($handle, static::read_block_size))) {
          $cur += strlen($c_data);
          if ($cur  <  $max + 1) {print        $c_data;                            }
          if ($cur === $max + 1) {print        $c_data;                      break;}
          if ($cur  >  $max + 1) {print substr($c_data, 0, $max + 1 - $cur); break;}
        }
        fclose($handle);
      }
      exit();
    }

  }

}}