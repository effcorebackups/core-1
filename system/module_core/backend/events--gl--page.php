<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class events_page {

  static function on_show_block_menu_user($page) {
    $user = user::current_get();
    if (empty($user->id)) {
      return new markup('x-block', ['class' => ['menu-user']], [
        storage::get('files')->select('trees/user/user_anonymous'),
        new markup_simple('img', [
          'class' => ['avatar' => 'avatar'],
          'alt' => 'avatar',
          'src' => '/'.module::get('page')->get_path().'frontend/images/avatar-anonymous._vector'
        ])
      ]);
    } else {
      return new markup('x-block', ['class' => ['menu-user']], [
        storage::get('files')->select('trees/user/user_logged_in'),
        new markup('a', ['href' => '/user/'.$user->id],
          new markup_simple('img', [
            'class' => ['avatar' => 'avatar'],
            'alt' => 'avatar',
            'src' => $user->avatar_path_relative ?
                 '/'.$user->avatar_path_relative :
                 '/'.module::get('page')->get_path().'frontend/images/avatar-logged_in._vector'
          ])
        )
      ]);
    }
  }

  static function on_show_block_title($page) {
    return new markup('h1', ['id' => 'title'],
      token::replace(translation::get($page->title))
    );
  }

}}