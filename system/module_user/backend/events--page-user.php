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
          use \effcore\text_multiline;
          use \effcore\text;
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
        if ($user->nick == user::current_get()->nick) {
          $selection->field_markup_insert('session_expired', 'Session expired date',
            new text(locale::timestmp_format(session::id_expired_extract(session::id_get())))
          );
        }
        $user_roles = user::id_roles_get($user->nick);
        if ($user_roles) {
          $selection->field_markup_insert('roles', 'Roles',
            new text_multiline($user_roles)
          );          
        }
        return new block('', ['class' => ['user-info' => 'user-info']],
          $selection
        );
      } else core::send_header_and_exit('access_forbidden');
    }   else core::send_header_and_exit('page_not_found');
  }

}}