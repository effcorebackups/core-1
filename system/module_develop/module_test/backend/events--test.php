<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\test {
          use \effcore\instance;
          use \effcore\text;
          abstract class events_test {

  static function set_roles(&$test, &$c_scenario, &$c_step, &$c_results, $nickname, $roles = [], $is_delete_old_roles = false) {
    $user = (new instance('user', [
      'nickname' => $nickname
    ]))->select();
    if ($user) {
      $reports[] = new text('changing roles for user with nickname = "%%_nickname"', ['nickname' => $nickname]);
      $reports[] = new text('found user ID = "%%_id_user" by nickname = "%%_nickname"', ['id_user' => $user->id, 'nickname' => $nickname]);
      $c_results['reports'][] = $reports;
      return true;
    }
  }

}}