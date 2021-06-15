<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\frontend;
          abstract class events_page {

  static function on_tree_build_after($event, $tree) {
    if ($tree->module_id === 'user') {
      if (!frontend::select('tree_user__user'))
           frontend::insert('tree_user__user', null, 'styles', ['path' => 'frontend/tree-user.cssd', 'attributes' => ['rel' => 'stylesheet', 'media' => 'all'], 'weight' => -100], 'tree_style', 'user');
    }
  }

}}