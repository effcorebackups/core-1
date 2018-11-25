<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\block;
          use \effcore\core;
          use \effcore\instance;
          use \effcore\locale;
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
      # get values
        $user_roles = user::id_roles_get($user->nick);
        $values = $user->values_get();
        $values['roles'] = $user_roles ? implode(', ', $user_roles) : '-';
        $values['created'] = locale::datetime_global_to_native($values['created']);
        $values['updated'] = locale::datetime_global_to_native($values['updated']);
        $values['password_hash'] = '*****';
        $values['is_embed'] = $values['is_embed'] ? 'Yes' : 'No';
        $values['avatar_path_relative'] = $values['avatar_path_relative'] ?: '-';
      # show table
        $thead = [['Parameter', 'Value']];
        $tbody = core::array_rotate([array_keys($values), array_values($values)]);
        return new block('', ['class' => ['user-info' => 'user-info']],
          new table([], $tbody, $thead)
        );
      } else core::send_header_and_exit('access_forbidden');
    }   else core::send_header_and_exit('page_not_found');
  }

}}