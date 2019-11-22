<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\access;
          use \effcore\core;
          use \effcore\instance;
          use \effcore\user;
          abstract class events_page_user_edit {

  static function on_build_before($event, $page) {
    $user = (new instance('user', [
      'nickname' => $page->args_get('nickname')
    ]))->select();
    if ($user) {
      if ($user->id == user::get_current()->id ||                       # owner
          access::check((object)['roles' => ['admins' => 'admins']])) { # admin
        $page->args_set('entity_name', 'user'   );
        $page->args_set('instance_id', $user->id);
      } else core::send_header_and_exit('access_forbidden');
    }   else core::send_header_and_exit('page_not_found'  );
  }

}}