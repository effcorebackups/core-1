<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\frontend;
          abstract class events_page {

  static function on_tree_build_after($event, $tree) {
    if ($tree->id == 'user_registered' && !frontend::select('tree_user')) frontend::insert('tree_user', null, 'styles', ['path' => 'frontend/tree-user.cssd', 'attributes' => ['rel' => 'stylesheet', 'media' => 'all']], 'tree_style', 'user');
    if ($tree->id == 'user_anonymous'  && !frontend::select('tree_user')) frontend::insert('tree_user', null, 'styles', ['path' => 'frontend/tree-user.cssd', 'attributes' => ['rel' => 'stylesheet', 'media' => 'all']], 'tree_style', 'user');
  }

}}