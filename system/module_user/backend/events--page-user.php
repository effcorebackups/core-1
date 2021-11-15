<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use const \effcore\br;
          use \effcore\access;
          use \effcore\instance;
          use \effcore\response;
          use \effcore\role;
          use \effcore\text_multiline;
          use \effcore\user;
          abstract class events_page_user {

  static function on_check_access_and_existence($event, $page) {
    $user = (new instance('user', [
      'nickname' => $page->args_get('nickname')
    ]))->select();
    if ($user) {
      if ($user->id === user::get_current()->id ||                      # owner
          access::check((object)['roles' => ['admins' => 'admins']])) { # admin
      } else response::send_header_and_exit('access_forbidden');
    }   else response::send_header_and_exit('page_not_found', null, new text_multiline(['wrong user nickname', 'go to <a href="/">front page</a>'], [], br.br));
  }

  static function on_show_user_roles($c_row_id, $c_row, $c_instance, $settings = []) {
    $roles_with_title = [];
    $roles = role::get_all();
    $roles_by_user = user::related_roles_select($c_instance->id);
    foreach ($roles_by_user as $c_id_user_role)
      $roles_with_title[$c_id_user_role] =
                 $roles[$c_id_user_role]->title ??
                        $c_id_user_role;
    return new text_multiline(
      $roles_with_title, [], ', '
    );
  }

}}