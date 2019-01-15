<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\block;
          use \effcore\markup_simple;
          use \effcore\markup;
          use \effcore\module;
          use \effcore\storage;
          use \effcore\user;
          abstract class events_page {

  static function on_show_block_menu_user($page) {
    // $user = user::current_get();
    // $src = $user->avatar_path;
  }

}}