<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {

  ##########################
  ### single entry point ###
  ##########################

  const nl = "\n";
  const tb = "\t";
  const br = "<br/>";

  ##############################################
  ### web servers compatibility improvements ###
  ##############################################

  # ────────────────────────────────────────────────────────────────────────
  #                 | HTTPS | REQUEST_SCHEME | REQUEST_SCHEME + improvements
  # ────────────────────────────────────────────────────────────────────────
  # apache v2.4     | -     | http           | http
  # apache v2.4 ssl | on    | https          | https
  # nginx  v1.1     | -     | http           | http
  # nginx  v1.1 ssl | on    | https          | https
  # iis    v7.5     | off   | -              | http
  # iis    v7.5 ssl | on    | -              | https
  # ────────────────────────────────────────────────────────────────────────

  if (!isset($_SERVER['REQUEST_SCHEME'])) {
    $_SERVER['REQUEST_SCHEME'] = isset($_SERVER['HTTPS']) &&
                                       $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
  }

  #############################
  ### load required classes ###
  #############################

  require_once('module_core/backend/class_ab--gl--factory.php');
  require_once('module_core/backend/class_ab--gl--timer.php');
  require_once('module_core/backend/class_ab--gl--console.php');
  require_once('module_core/backend/class_ab--gl--dynamic.php');
  require_once('module_core/backend/class_ab--gl--cache.php');
  require_once('module_core/backend/class--gl--file.php');
  spl_autoload_register('\effcore\factory::autoload');
  timer::tap('total');

  #######################
  ### return the file ###
  #######################

  # note:
  # ─────────────────────────────────────────────────────────────────────
  # 1. url /           | is page 'page_front'
  # 2. url /page       | is page 'page'
  # 3. url /file.type  | is file 'file.type' (p.s. any file defined by type)
  # ─────────────────────────────────────────────────────────────────────
  # 4. url /page/      | is wrong notation - redirect to /page and interpreted as a page 'page'
  # 5. url /file/      | is wrong notation - redirect to /file and interpreted as a page 'file'
  # 6. url /file.type/ | is wrong notation - redirect to /file.type
  # ─────────────────────────────────────────────────────────────────────

  if (             url::get_current()->get_path() != '/' &&
            substr(url::get_current()->get_path(), -1) == '/') {
     url::go(rtrim(url::get_current()->get_path(), '/')); # p.s. trimming for single redirect
  }

  $type = url::get_current()->get_type();
  if ($type) {
    $file_types = file::get_types();
  # case for protected file
    if (!empty($file_types[$type]->protected)) {
      factory::send_header_and_exit('access_denided', '',
        translation::get('file of this type is protected by: %%_name', ['name' => 'file_types._data']).br.br.
        translation::get('go to <a href="/">front page</a>')
      );
    }
    $path = dir_root.ltrim(url::get_current()->get_path(), '/');
    if (is_file($path) && is_readable($path)) {
    # case for file with tokens
      if (!empty($file_types[$type]->use_tokens)) {
        $file = new file($path);
        $data = token::replace($file->load());
        $etag = base64_encode(md5($data, true));
      # send header "304 Not Modified" to the output buffer if HTTP_IF_NONE_MATCH header is received
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) &&
                  $_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
          header('HTTP/1.1 304 Not Modified');
          console::store_log();
          exit();
        }
      # send headers and data to the output buffer
        header('Content-Length: '.strlen($data), true);
        header('Cache-Control: must-revalidate, private', true);
        header('Etag: '.$etag, true);
        if (!empty($file_types[$type]->headers)) {
          foreach ($file_types[$type]->headers as $c_key => $c_value) {
            header($c_key.': '.$c_value, true);
          }
        }
        print $data;
        console::store_log();
        exit();
    # case for any other file (and for large files too)
      } else {
        header('Content-Length: '.filesize($path), true);
        if (!empty($file_types[$type]->headers)) {
          foreach ($file_types[$type]->headers as $c_key => $c_value) {
            header($c_key.': '.$c_value, true);
          }
        }
        if ($file = fopen($path, 'rb')) {
          while (!feof($file)) {
            print fread($file, 1024);
          }
          fclose($file);
        }
        console::store_log();
        exit();
      }
    }
  }

  #######################
  ### return the page ###
  #######################

  ob_start();
  $output = '';
  foreach (event::start('on_module_start') as $c_results) {
    foreach ($c_results as $c_result) {
      $output.= str_replace(nl.nl, '', $c_result);
    }
  }
  header('Content-Length: '.strlen($output), true);
  print $output;
  console::store_log();
  exit();

}