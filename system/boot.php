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

  require_once('core/backend/class_ab--gl--factory.php');
  require_once('core/backend/class_ab--gl--timer.php');
  require_once('core/backend/class_ab--gl--console.php');
  require_once('core/backend/class_ab--gl--dynamic.php');
  require_once('core/backend/class_ab--gl--cache.php');
  require_once('core/backend/class--gl--file.php');
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

  if (            url::get_current()->path != '/' &&
           substr(url::get_current()->path, -1) == '/') {
    url::go(rtrim(url::get_current()->path, '/'));
  }

  $file_types = file::get_file_types();
  $extension = url::get_current()->get_extension();
  if ($extension) {
  # case for protected files
    if (!empty($file_types[$extension]->protected)) {
      factory::send_header_and_exit('access_denided', '',
        translation::get('file of this type is protected by: %%_name', ['name' => 'file_types._d']).br.br.
        translation::get('go to <a href="/">front page</a>')
      );
    }
  # case for media files
    $path = dir_root.ltrim(url::get_current()->path, '/');
    if (is_file($path) && is_readable($path)) {
      $file = new file($path);
      $data = $file->load();
    # replace tokens
      if (!empty($file_types[$extension]->use_tokens)) {
        $data = token::replace($data);
      }
    # if get header HTTP_IF_NONE_MATCH
      $etag = base64_encode(md5($data, true));
      if (isset($_SERVER['HTTP_IF_NONE_MATCH']) &&
                $_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
        header('HTTP/1.1 304 Not Modified');
        exit();
      }
    # send headers
      header('Cache-Control: must-revalidate, private', true);
      header('Etag: '.$etag, true);
      if (!empty($file_types[$extension]->headers)) {
        foreach ($file_types[$extension]->headers as $c_key => $c_value) {
          header($c_key.': '.$c_value, true);
        }
      }
      print $data;
      exit();
    }
  }

  #######################
  ### return the page ###
  #######################

  ob_start();
  foreach (event::start('on_module_start') as $c_results) {
    foreach ($c_results as $c_result) {
      print str_replace(nl.nl, '', $c_result);
    }
  }

}