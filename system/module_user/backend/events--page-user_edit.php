<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\core;
          use \effcore\instance;
          use \effcore\user;
          abstract class events_page_user_edit {

  static function on_build_before($event, $page) {
    $user = (new instance('user', [
      'nick' => $page->args_get('nickname')
    ]))->select();
    if ($user) {
      if ($user->nick == user::get_current()->nick ||             # owner
                   isset(user::get_current()->roles['admins'])) { # admin
        $page->args_set('entity_name', 'user'   );
        $page->args_set('instance_id', $user->id);
      } else core::send_header_and_exit('access_forbidden');
    }   else core::send_header_and_exit('page_not_found'  );
  }

}}