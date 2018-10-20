<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\block;
          use \effcore\core;
          use \effcore\entity;
          use \effcore\instance;
          use \effcore\locale;
          use \effcore\markup_simple;
          use \effcore\markup;
          use \effcore\module;
          use \effcore\storage;
          use \effcore\table;
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
      $src = $user->avatar_path_relative ?
         '/'.$user->avatar_path_relative : '/'.module::get('user')->path.'frontend/images/avatar-logged_in.svgd';
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

  static function on_show_block_user_info($page) {
    $user = (new instance('user', [
      'nick' => $page->args_get('nick')
    ]))->select();
    if ($user) {
      if ($user->nick == user::current_get()->nick ||             # owner
                   isset(user::current_get()->roles['admins'])) { # admin
      # get values
        $user_roles = user::id_roles_get($user->nick);
        $values = $user->values_get();
        $values['roles'] = $user_roles ? implode(', ', $user_roles) : '-';
        $values['created'] = locale::format_datetime($values['created']);
        $values['updated'] = locale::format_datetime($values['updated']);
        $values['password_hash'] = '*****';
        $values['is_embed'] = $values['is_embed'] ? 'Yes' : 'No';
        $values['avatar_path_relative'] = $values['avatar_path_relative'] ?: '-';
      # show table
        $thead = [['Parameter', 'Value']];
        $tbody = core::array_rotate([array_keys($values), array_values($values)]);
        return new block('', ['class' => ['user-info' => 'user-info']],
          new table([], $tbody, $thead)
        );
      } else {
        core::send_header_and_exit('access_denided');
      }
    } else {
      core::send_header_and_exit('page_not_found');
    }
  }

}}