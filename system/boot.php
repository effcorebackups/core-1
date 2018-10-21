<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {

  ##########################
  ### single entry point ###
  ##########################

  const nl = "\n";
  const tb = "\t";
  const br = "<br>";

  if (!ini_get('date.timezone')) {
    date_default_timezone_set('UTC');
  }

  #############################
  ### load required classes ###
  #############################

  require_once('module_core/backend/core.php');
  require_once('module_core/backend/timer.php');
  require_once('module_core/backend/console.php');
  require_once('module_core/backend/dynamic.php');
  require_once('module_core/backend/cache.php');
  require_once('module_core/backend/file.php');
  spl_autoload_register('\\effcore\\core::autoload');
  timer::tap('total');

  #######################
  ### return the file ###
  #######################

  # note:
  # ═══════════════════╦════════════════════════════════════════════════════════════════════
  # 1. url /           ║ is page 'page_front'
  # 2. url /page       ║ is page 'page'
  # 3. url /file.type  ║ is file 'file.type'
  # ───────────────────╫────────────────────────────────────────────────────────────────────
  # 4. url /page/      ║ is wrong notation - redirect to /page and interpreted as page 'page'
  # 5. url /file/      ║ is wrong notation - redirect to /file and interpreted as page 'file'
  # 6. url /file.type/ ║ is wrong notation - redirect to /file.type
  # ───────────────────╨────────────────────────────────────────────────────────────────────

  if (             url::current_get()->path_get() != '/' &&
            substr(url::current_get()->path_get(), -1) == '/') {
     url::go(rtrim(url::current_get()->path_get(), '/')); # p.s. trimming for single redirect
  }

  $type = url::current_get()->type_get();
  if ($type) {
    $file_types = file::types_get();
  # case for protected file
    if (!empty($file_types[$type]->protected)) {
      core::send_header_and_exit('access_denided', '',
        translation::get('file of this type is protected by: %%_name', ['name' => 'file_types.data']).br.br.
        translation::get('go to <a href="/">front page</a>')
      );
    }

  # define path of file directory
    $path_url = url::current_get()->path_get();
    if (substr($path_url, 0, 15) === '/dynamic/files/')
         $path = dynamic::dir_files.substr(ltrim($path_url, '/'), 14);
    else $path =                  dir_root.ltrim($path_url, '/');

    if (is_file    ($path) &&
        is_readable($path)) {

    # ─────────────────────────────────────────────────────────────────────
    # case for dynamic file
    # ─────────────────────────────────────────────────────────────────────
      if (!empty($file_types[$type]->dynamic)) {
        $file = new file($path);
        $data = token::replace($file->load());
        $etag = md5($data);

      # send header '304 Not Modified' if the data has not changed
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) &&
                  $_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
          header('HTTP/1.1 304 Not Modified');
          console::log_store();
          exit();
        }

      # send headers and data to the output buffer
        header('Content-Length: '.strlen($data));
        header('Accept-Ranges: none');
        header('Cache-Control: private, no-cache');
        header('Etag: '.$etag);
        if (!empty($file_types[$type]->headers)) {
          foreach ($file_types[$type]->headers as $c_key => $c_value) {
            header($c_key.': '.$c_value);
          }
        }
        print $data;
        console::log_store();
        exit();

    # ─────────────────────────────────────────────────────────────────────
    # case for static file
    # ─────────────────────────────────────────────────────────────────────
      } else {

        $last_modified = gmdate('D, d M Y H:i:s', filemtime($path)).' GMT';

      # send header '304 Not Modified' if the data has not changed
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&
                  $_SERVER['HTTP_IF_MODIFIED_SINCE'] === $last_modified) {
          header('HTTP/1.1 304 Not Modified');
          console::log_store();
          exit();
        }

        # http ranges limits:
        # ─────────────────────────────────────────────────────────────────────
        #
        #           ┌┬┬┬┬┬┬┬┬┐
        #    ┝━━━━━━┿┷┷┷┷┷┷┷┷┿━━━━━━━━━━━━━━┥
        #    │0     │min     │max           │length
        #
        # .....................................................................
        #
        #    0 ≤ MIN ≤ max < length  →  MIN ≥ 0 ≤ max < length
        #    0 ≤ min ≤ MAX < length  →  MAX ≥ 0 ≥ min < length
        #
        #    min ≥ 0 │ min ≤ max │ min < length
        #    max ≥ 0 │ max ≥ min │ max < length
        #
        # ─────────────────────────────────────────────────────────────────────

        $length = filesize($path);
        $ranges = core::server_http_range_get();
        $min = $ranges->min !== null ? $ranges->min : 0;
        $max = $ranges->max !== null ? $ranges->max : $length - 1;
        if ($max >= $length) $max = $length - 1;
        if (!($min >= 0 && $min <= $max && $min < $length))   {header('HTTP/1.1 416 Requested Range Not Satisfiable'); exit();}
        if (!($max >= 0 && $max >= $min && $max < $length))   {header('HTTP/1.1 416 Requested Range Not Satisfiable'); exit();}
        if (!($ranges->min === null && $ranges->max === null)) header('HTTP/1.1 206 Partial Content');
        if (!($ranges->min === null && $ranges->max === null)) header('Content-Range: bytes '.$min.'-'.$max.'/'.$length);
        header('Content-Length: '.($max - $min + 1));
        header('Accept-Ranges: bytes');
        header('Cache-Control: private, no-cache');
        header('Last-Modified: '.$last_modified);
        if (!empty($file_types[$type]->headers)) {
          foreach ($file_types[$type]->headers as $c_key => $c_value) {
            header($c_key.': '.$c_value);
          }
        }
        if ($resource = fopen($path, 'rb')) {
          $c_print_length = $min;
          if (fseek($resource, $min) == 0) {
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

    } else {
      core::send_header_and_exit('file_not_found');
    }
  }

  #######################
  ### return the page ###
  #######################

  if (!storage::is_installed()) {
    if (strpos(url::current_get()->path_get(), '/install') !== 0) {
      url::go('/install/en');
    }
  }

  ob_start();
  $output = '';
  foreach (event::start('on_module_start') as $c_results) {
    foreach ($c_results as $c_result) {
      $output.= str_replace(nl.nl, '', $c_result);
    }
  }
  header('Content-Length: '.strlen($output));
  header('Cache-Control: private, no-cache');
  print $output;
  console::log_store();
  exit();

}
