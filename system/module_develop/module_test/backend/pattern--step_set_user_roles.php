<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class step_set_user_roles {

  public $nickname;
  public $roles = [];
  public $is_reset = false;

  function run(&$test, $dpath, &$c_results) {
    $nickname = token::apply($this->nickname);
    $user = (new instance('user', [
      'nickname' => $nickname
    ]))->select();
    if ($user) {
      if (true           ) $c_results['reports'][$dpath]['dpath'] = '### dpath: '.$dpath;
      if (true           ) $c_results['reports'][$dpath][] = new text('changing roles for user with nickname = "%%_nickname"', ['nickname' => $nickname]);
      if (true           ) $c_results['reports'][$dpath][] = new text('found user ID = "%%_id_user" by nickname = "%%_nickname"', ['id_user' => $user->id, 'nickname' => $nickname]);
      if ($this->is_reset) $c_results['reports'][$dpath][] = new text('there will be try to delete all roles');
      if (true           ) $c_results['reports'][$dpath][] = new text('there will be try to insert roles: %%_roles', ['roles' => implode(', ', $this->roles) ?: 'n/a']);
      $old_roles     =     user::related_roles_select($user->id);
      if ($this->is_reset) user::related_roles_delete($user->id);
                           user::related_roles_insert($user->id, $this->roles, 'test');
      $new_roles     =     user::related_roles_select($user->id);
      $c_results['reports'][$dpath][] = new text('roles before insertion: %%_roles', ['roles' => implode(', ', $old_roles) ?: 'n/a']);
      $c_results['reports'][$dpath][] = new text('roles after insertion: %%_roles',  ['roles' => implode(', ', $new_roles) ?: 'n/a']);
    }
  }

}}