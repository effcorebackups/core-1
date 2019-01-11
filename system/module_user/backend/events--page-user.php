<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\block;
          use \effcore\core;
          use \effcore\instance;
          use \effcore\locale;
          use \effcore\selection;
          use \effcore\session;
          use \effcore\table;
          use \effcore\user;
          abstract class events_page_user {

  static function on_show_block_user_info($page) {
    $user = (new instance('user', [
      'nick' => $page->args_get('nick')
    ]))->select();
    if ($user) {
      if ($user->nick == user::current_get()->nick ||             # owner
                   isset(user::current_get()->roles['admins'])) { # admin

        $selection = selection::get('user');
        $selection->title = '';
        $selection->conditions = ['nick' => $user->nick];
        return new block('', ['class' => ['user-info' => 'user-info']],
          $selection
        );

      # $user_roles = user::id_roles_get($user->nick);
      # if ($user->nick == user::current_get()->nick) $values['current_session_expired'] = locale::timestmp_format(session::id_expired_extract(session::id_get()));
      # $values['roles'] = $user_roles ? implode(', ', $user_roles) : '-';
      # $values['avatar_path'] = $values['avatar_path'] ?: '-';
      # $tbody = core::array_rotate([
      #   array_keys  ($values),
      #   array_values($values)
      # ]);

      } else core::send_header_and_exit('access_forbidden');
    }   else core::send_header_and_exit('page_not_found');
  }

}}