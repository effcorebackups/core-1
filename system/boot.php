<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {

  ##########################
  ### single entry point ###
  ##########################

  const nl = "\n";
  const cr = "\r";
  const tb = "\t";
  const br = "<br>";
  const hr = "<hr>";

  ini_set('pcre.jit', false);
  if (!ini_get('date.timezone')) {
    date_default_timezone_set('UTC');
  }

  #############################
  ### load required classes ###
  #############################

  require_once('module_core/backend/core.php'      );
  require_once('module_storage/backend/markers.php');
  require_once('module_core/backend/console.php'   );
  spl_autoload_register('\\effcore\\core::structure_autoload');
  console::log_insert('file', 'insertion', 'system/boot.php',                           'ok');
  console::log_insert('file', 'insertion', 'system/module_storage/backend/markers.php', 'ok');
  console::log_insert('file', 'insertion', 'system/module_core/backend/console.php',    'ok');
  timer::tap('total');

  #######################
  ### return the file ###
  #######################

  # note:
  # ═══════════════════╦════════════════════════════════════════════════════════════════════
  # 1. url /           ║ is page 'page-front'
  # 2. url /page       ║ is page 'page'
  # 3. url /file.type  ║ is file 'file.type'
  # ───────────────────╫────────────────────────────────────────────────────────────────────
  # 4. url /page/      ║ is wrong notation - redirect to /page and interpreted as page 'page'
  # 5. url /file/      ║ is wrong notation - redirect to /file and interpreted as page 'file'
  # 6. url /file.type/ ║ is wrong notation - redirect to /file.type
  # ───────────────────╨────────────────────────────────────────────────────────────────────

  if (core::server_get_request_uri()     !== '/' &&
      core::server_get_request_uri()[-1] === '/') {
    $new_url = rtrim(core::server_get_request_uri(), '/'); # note: trimming for single redirect
    url::go($new_url === '' ? '/' :
            $new_url);
  }

  $file_info = url::get_current()->file_info_get();
  if ($file_info && strlen($file_info->type)) {

    $file_types = file::types_get();

    # ─────────────────────────────────────────────────────────────────────
    # case for system file ('.type') - show 'forbidden' even if it does not exist!
    # ─────────────────────────────────────────────────────────────────────

    if ($file_info->type !== '' &&
        $file_info->name === '') {
      core::send_header_and_exit('access_forbidden', null, new text_multiline([
        'file of this type is protected',
        'go to <a href="/">front page</a>'
      ], [], br.br));
    }

    # ─────────────────────────────────────────────────────────────────────
    # case for protected file - show 'forbidden' even if it does not exist!
    # ─────────────────────────────────────────────────────────────────────

    if (isset($file_types[$file_info->type]->kind) &&
              $file_types[$file_info->type]->kind == 'protected') {
      core::send_header_and_exit('access_forbidden', null, new text_multiline([
        'file of this type is protected',
        'go to <a href="/">front page</a>'
      ], [], br.br));
    }

    # ─────────────────────────────────────────────────────────────────────
    # case for virtual file
    # ─────────────────────────────────────────────────────────────────────

    if (isset($file_types[$file_info->type]->kind) &&
              $file_types[$file_info->type]->kind == 'virtual') {
      call_user_func_array($file_types[$file_info->type]->handler, [$file_info]);
    }

    # ─────────────────────────────────────────────────────────────────────
    # define real path (breake all './', '../', '~/' and etc)
    # ─────────────────────────────────────────────────────────────────────

    $path_url = url::get_current()->path_get();
    $path = realpath(dir_root.ltrim($path_url, '/'));
    if (core::server_os_is_windows()) $path = str_replace('\\', '/', $path);
    if ($path === false || strpos($path, dir_root) !== 0) {
      core::send_header_and_exit('file_not_found');
    }

    # ─────────────────────────────────────────────────────────────────────
    # case for dynamic file
    # ─────────────────────────────────────────────────────────────────────

    if (is_file    ($path) &&
        is_readable($path)) {

      if (isset($file_types[$file_info->type]->kind) &&
                $file_types[$file_info->type]->kind == 'dynamic') {
        $file = new file($path);
        $data = token::apply($file->load());
        $etag = core::hash_get_etag($data);

      # send header '304 Not Modified' if the data has no changes
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) &&
                  $_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
          header('HTTP/1.1 304 Not Modified');
          console::log_store();
          exit();
        }

      # send default headers
        header('Accept-Ranges: none');
        header('Etag: '.$etag);
        if (!empty($file_types[$file_info->type]->headers)) {
          foreach ($file_types[$file_info->type]->headers as $c_key => $c_value) {
            header($c_key.': '.$c_value);
          }
        }

      # send result data
        $result = $data;
        if ($file_info->type === 'cssd' || $file_info->type === 'jsd') {
          if (console::visible_mode_get() === console::visible_for_everyone) {
            timer::tap('total');
            $result.= nl.'/*'.nl.console::text_get().nl.'*/'.nl;
          }
        }
        header('Content-Length: '.strlen($result));
        header('Cache-Control: private, no-cache');
        print $result;
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
        $ranges = core::server_get_http_range();
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
        if (!empty($file_types[$file_info->type]->headers)) {
          foreach ($file_types[$file_info->type]->headers as $c_key => $c_value) {
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

  if (!storage::get('sql')->is_installed()) {
    if (!preg_match('%^/install(/[a-z]{2,2}|)$%', url::get_current()->path_get())) {
      url::go('/install/en');
    }
  }

  ob_start();
  $result = '';
  foreach (event::start('on_module_start') as $c_results) {
    foreach ($c_results as $c_result) {
      $result.= str_replace(nl.nl, '', $c_result);
    }
  }
  if (console::visible_mode_get()) {
    timer::tap('total');
    $result = str_replace('</body>', console::markup_get()->render().'</body>', $result);
  }
  header('Cache-Control: private, no-cache');
  print $result;
  console::log_store();
  exit();

}