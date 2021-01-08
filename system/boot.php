<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {

  ##########################
  ### single entry point ###
  ##########################

  const a0 = "\0";
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

  # ─────────────────────────────────────────────────────────────────────
  # preventing invalid requests (for example: "http://домен/путь?запрос" instead "http://xn--d1acufc/%D0%BF%D1%83%D1%82%D1%8C?%D0%B7%D0%B0%D0%BF%D1%80%D0%BE%D1%81")
  # ─────────────────────────────────────────────────────────────────────

  $raw_url = core::server_get_request_scheme().'://'.
             core::server_get_host(false).
             core::server_get_request_uri();
  if (core::sanitize_url($raw_url) !== $raw_url || core::validate_url($raw_url, FILTER_FLAG_SCHEME_REQUIRED|FILTER_FLAG_HOST_REQUIRED|FILTER_FLAG_PATH_REQUIRED) === false || url::get_current()->has_error === true) {
    core::send_header_and_exit('bad_request');
  }

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

  #######################
  ### return the file ###
  #######################

  $file = url::get_current()->file_info_get();
  if ($file instanceof file && strlen($file->type)) {

    $file_types = file::types_get();

    # ─────────────────────────────────────────────────────────────────────
    # case for any system file ('.type', '.name.type'…) - show 'forbidden' even if it does not exist!
    # ─────────────────────────────────────────────────────────────────────

    if (($file->name !== '' && $file->name[0] === '.') ||
        ($file->type !== '' && $file->name === '')) {
      core::send_header_and_exit('access_forbidden', null, new text_multiline([
        'file of this type is protected',
        'go to <a href="/">front page</a>'
      ], [], br.br));
    }

    # ─────────────────────────────────────────────────────────────────────
    # case for protected file - show 'forbidden' even if it does not exist!
    # ─────────────────────────────────────────────────────────────────────

    if (isset($file_types[$file->type]->kind) &&
              $file_types[$file->type]->kind === 'protected') {
      core::send_header_and_exit('access_forbidden', null, new text_multiline([
        'file of this type is protected',
        'go to <a href="/">front page</a>'
      ], [], br.br));
    }

    # ─────────────────────────────────────────────────────────────────────
    # case for virtual file
    # ─────────────────────────────────────────────────────────────────────

    if (isset($file_types[$file->type]->kind) &&
              $file_types[$file->type]->kind === 'virtual') {
      $type = $file_types[$file->type];
      event::start('on_file_load', 'virtual', [&$type, &$file]);
      exit();
    }

    # ─────────────────────────────────────────────────────────────────────
    # protecting files from attacks
    # ─────────────────────────────────────────────────────────────────────

    $type = $file_types[$file->type] ?? (object)['type' => $file->type, 'module_id' => null];
    $real_path = core::validate_realpath($file->path_get());
    if ($real_path === false)               {event::start('on_file_load', 'not_found', [&$type, &$file, $real_path, 1]); exit();} # object does not really exist or object is inaccessible to the web server by rights
    if ($real_path !== $file->path_get())   {event::start('on_file_load', 'not_found', [&$type, &$file, $real_path, 2]); exit();} # resolved path is not the same as the original
    if (strpos($real_path, dir_root) !== 0) {event::start('on_file_load', 'not_found', [&$type, &$file, $real_path, 3]); exit();} # object is outside the web root
    if (!is_file    ($file->path_get()))    {event::start('on_file_load', 'not_found', [&$type, &$file, $real_path, 4]); exit();} # object exists, but it is not a file
    if (!is_readable($file->path_get())) core::send_header_and_exit('access_forbidden'); # object is inaccessible to the web server by rights

    # ─────────────────────────────────────────────────────────────────────
    # case for dynamic file
    # ─────────────────────────────────────────────────────────────────────

    if (isset($file_types[$file->type]->kind) &&
              $file_types[$file->type]->kind === 'dynamic') {
      $type = $file_types[$file->type];
      event::start('on_file_load', 'dynamic', [&$type, &$file]);
      exit();

    # ─────────────────────────────────────────────────────────────────────
    # case for static file
    # ─────────────────────────────────────────────────────────────────────

    } else {
      $type = $file_types[$file->type] ?? (object)['type' => $file->type, 'module_id' => null];
      event::start('on_file_load', 'static', [&$type, &$file]);
      exit();
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
  timer::tap('total');
  if (console::visible_mode_get()) {
    $result = str_replace('</body>', console::markup_get()->render().'</body>', $result);
  }
  if (module::is_enabled('test')) {
    header('X-Time-total: '.locale::format_msecond(
      timer::period_get('total', 0, 1)
    ));
  }
  header('Cache-Control: private, no-cache');
  print $result;
  exit();

}