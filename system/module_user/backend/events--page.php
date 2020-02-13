<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\frontend;
          abstract class events_page {

  static function on_tree_build_before($event, $tree) {
    if (!frontend::select('tree_system')) frontend::insert('tree_system', null, 'styles', ['path' => 'frontend/tree.cssd', 'attributes' => ['rel' => 'stylesheet', 'media' => 'all']], 'tree_style', 'menu');
    if (!frontend::select('tree_user'  )) frontend::insert('tree_user',   null, 'styles', ['path' => 'frontend/tree.cssd', 'attributes' => ['rel' => 'stylesheet', 'media' => 'all']], 'tree_style', 'user');
  }

  static function on_tree_build_after($event, $tree) {
    if ($tree->visualization_mode == 'decorated-rearrangeable') {
      frontend::insert('tree_rearrangeable', null, 'scripts', ['path'  => 'frontend/tree-rearrangeable.js', 'attributes' => ['defer' => true]], 'tree_script_rearrangeable', 'menu');
    }
  }

}}