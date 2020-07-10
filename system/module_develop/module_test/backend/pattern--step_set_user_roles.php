<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class step_set_user_roles {

  public $nickname;
  public $roles = [];
  public $is_reset = false;

  function run(&$test, &$c_scenario, &$c_step, &$c_results) {
    $nickname = token::apply($this->nickname);
    $user = (new instance('user', [
      'nickname' => $nickname
    ]))->select();
    if ($user) {
      if (true           ) $reports[] = new text('changing roles for user with nickname = "%%_nickname"', ['nickname' => $nickname]);
      if (true           ) $reports[] = new text('found user ID = "%%_id_user" by nickname = "%%_nickname"', ['id_user' => $user->id, 'nickname' => $nickname]);
      if ($this->is_reset) $reports[] = new text('there will be try to delete all roles');
      if (true           ) $reports[] = new text('there will be try to insert roles: %%_roles', ['roles' => implode(', ', $this->roles) ?: 'n/a']);
      $old_id_roles =      user::relation_role_select    ($user->id);
      if ($this->is_reset) user::relation_role_delete_all($user->id);
                           user::relation_role_insert    ($user->id, $this->roles, 'test');
      $new_id_roles =      user::relation_role_select    ($user->id);
      $reports[] = new text('roles before insertion: %%_roles', ['roles' => implode(', ', $old_id_roles) ?: 'n/a']);
      $reports[] = new text('roles after insertion: %%_roles',  ['roles' => implode(', ', $new_id_roles) ?: 'n/a']);
      $c_results['reports'][] = $reports;
    }
  }

}}