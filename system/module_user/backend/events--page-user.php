<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\access;
          use \effcore\core;
          use \effcore\instance;
          use \effcore\role;
          use \effcore\text_multiline;
          use \effcore\user;
          abstract class events_page_user {

  static function on_build_before($event, $page) {
    $user = (new instance('user', [
      'nickname' => $page->args_get('nickname')
    ]))->select();
    if ($user) {
      if ($user->id == user::get_current()->id ||                       # owner
          access::check((object)['roles' => ['admins' => 'admins']])) { # admin
      } else core::send_header_and_exit('access_forbidden');
    }   else core::send_header_and_exit('page_not_found');
  }

  static function on_show_user_roles($c_row, $c_instance) {
    $roles_with_title = [];
    $roles = role::get_all();
    $roles_by_user = user::get_roles_by_user($c_instance->id);
    foreach ($roles_by_user as $c_id_user_role)
      $roles_with_title[$c_id_user_role] =
                 $roles[$c_id_user_role]->title ?? $c_id_user_role;
    return new text_multiline(
      $roles_with_title, [], ', '
    );
  }

}}