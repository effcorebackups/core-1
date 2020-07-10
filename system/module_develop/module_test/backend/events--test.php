<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\test {
          use \effcore\instance;
          use \effcore\text;
          use \effcore\user;
          abstract class events_test {

  static function set_roles(&$test, &$c_scenario, &$c_step, &$c_results, $nickname, $roles = [], $is_reset = false) {
    $user = (new instance('user', [
      'nickname' => $nickname
    ]))->select();
    if ($user) {
      $reports[] = new text('changing roles for user with nickname = "%%_nickname"', ['nickname' => $nickname]);
      $reports[] = new text('found user ID = "%%_id_user" by nickname = "%%_nickname"', ['id_user' => $user->id, 'nickname' => $nickname]);
      $reports[] = new text('there will be try to insert roles: %%_roles', ['roles' => implode(', ', $roles) ?: 'n/a']);
      $old_id_roles = user::relation_role_select($user->id);
                      user::relation_role_insert($user->id, $roles, 'test');
      $new_id_roles = user::relation_role_select($user->id);
      $reports[] = new text('roles before insertion: %%_roles', ['roles' => implode(', ', $old_id_roles) ?: 'n/a']);
      $reports[] = new text('roles after insertion: %%_roles',  ['roles' => implode(', ', $new_id_roles) ?: 'n/a']);
      $c_results['reports'][] = $reports;
      return true;
    }
  }

}}