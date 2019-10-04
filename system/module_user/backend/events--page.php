<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\frontend;
          abstract class events_page {

  static function on_tree_build_before($event, $tree) {
    if (!frontend::select('tree_user')) frontend::insert('tree_user', null, 'styles', ['file' => 'frontend/tree.cssd', 'attributes' => ['rel' => 'stylesheet', 'media' => 'all']], 'tree_style', 'user');
    if (!frontend::select('tree_main')) frontend::insert('tree_main', null, 'styles', ['file' => 'frontend/tree.cssd', 'attributes' => ['rel' => 'stylesheet', 'media' => 'all']], 'tree_style', 'menu');
  }

}}