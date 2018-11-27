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
    $user = user::current_get();
    if (empty($user->nick)) {
      $src = '/'.module::get('user')->path.'frontend/images/avatar-anonymous.svgd';
      $block_menu = new block('', ['class' => ['menu-user' => 'menu-user']], [
        storage::get('files')->select('trees/user/user_anonymous'),
        new markup_simple('img', ['class' => ['avatar' => 'avatar'], 'alt' => 'avatar', 'src' => $src])
      ]);
    } else {
      $src = $user->avatar_path ?
         '/'.$user->avatar_path : '/'.module::get('user')->path.'frontend/images/avatar-logged_in.svgd';
      $block_menu = new block('', ['class' => ['menu-user' => 'menu-user']], [
        storage::get('files')->select('trees/user/user_logged_in'),
        new markup('a', ['href' => '/user/'.$user->nick],
          new markup_simple('img', ['class' => ['avatar' => 'avatar'], 'alt' => 'avatar', 'src' => $src])
        )
      ]);
    }
    $block_menu->content_tag_name = null;
    return $block_menu;
  }

}}