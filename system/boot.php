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

    if ($file_info->name === '' &&
        $file_info->type !== '') {
      core::send_header_and_exit('access_forbidden', null, new text_multiline([
        'file of this type is protected',
        'go to <a href="/">front page</a>'
      ], [], br.br));
    }

    # ─────────────────────────────────────────────────────────────────────
    # case for protected file - show 'forbidden' even if it does not exist!
    # ─────────────────────────────────────────────────────────────────────

    if (isset($file_types[$file_info->type]->kind) &&
              $file_types[$file_info->type]->kind === 'protected') {
      core::send_header_and_exit('access_forbidden', null, new text_multiline([
        'file of this type is protected',
        'go to <a href="/">front page</a>'
      ], [], br.br));
    }

    # ─────────────────────────────────────────────────────────────────────
    # case for virtual file
    # ─────────────────────────────────────────────────────────────────────

    if (isset($file_types[$file_info->type]->kind) &&
              $file_types[$file_info->type]->kind === 'virtual') {
      $path_url = ltrim(url::get_current()->path_get(), '/');
      event::start('on_file_load', 'virtual', [$file_types[$file_info->type], $file_info, $path_url]);
      exit();
    }

    # ─────────────────────────────────────────────────────────────────────
    # define real path (breake all './', '../', '~/' and etc)
    # ─────────────────────────────────────────────────────────────────────

    $path_url = ltrim(url::get_current()->path_get(), '/');
    $path = realpath(dir_root.$path_url);
    if ($path !== false && core::server_os_is_windows()) $path = str_replace('\\', '/', $path);
    if ($path === false || strpos($path, dir_root) !== 0) {
      core::send_header_and_exit('file_not_found');
    }

    # ─────────────────────────────────────────────────────────────────────
    # case for dynamic file
    # ─────────────────────────────────────────────────────────────────────

    if (is_file    ($path) &&
        is_readable($path)) {

      if (isset($file_types[$file_info->type]->kind) &&
                $file_types[$file_info->type]->kind === 'dynamic') {
        event::start('on_file_load', 'dynamic', [$file_types[$file_info->type], $file_info, $path]);
        exit();

    # ─────────────────────────────────────────────────────────────────────
    # case for static file
    # ─────────────────────────────────────────────────────────────────────

      } else {
        event::start('on_file_load', 'static', [$file_types[$file_info->type], $file_info, $path]);
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
  console::log_store();
  exit();

}