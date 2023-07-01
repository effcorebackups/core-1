<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Test_step_Set_user_roles {

    public $nickname;
    public $roles = [];
    public $is_reset = false;

    function run(&$test, $dpath, &$c_results) {
        $nickname = Token::apply($this->nickname);
        $user = (new Instance('user', [
            'nickname' => $nickname
        ]))->select();
        if ($user) {
            if (true           ) $c_results['reports'][$dpath]['dpath'] = '### dpath: '.$dpath;
            if (true           ) $c_results['reports'][$dpath][] = new Text('changing roles for user with nickname = "%%_nickname"', ['nickname' => $nickname]);
            if (true           ) $c_results['reports'][$dpath][] = new Text('found user ID = "%%_id_user" by nickname = "%%_nickname"', ['id_user' => $user->id, 'nickname' => $nickname]);
            if ($this->is_reset) $c_results['reports'][$dpath][] = new Text('there will be try to delete all roles');
            if (true           ) $c_results['reports'][$dpath][] = new Text('there will be try to insert roles: %%_roles', ['roles' => implode(', ', $this->roles) ?: 'n/a']);
            $old_roles     =     User::related_roles_select($user->id);
            if ($this->is_reset) User::related_roles_delete($user->id);
                                 User::related_roles_insert($user->id, $this->roles, 'test');
            $new_roles     =     User::related_roles_select($user->id);
            $c_results['reports'][$dpath][] = new Text('roles before insertion: %%_roles', ['roles' => implode(', ', $old_roles) ?: 'n/a']);
            $c_results['reports'][$dpath][] = new Text('roles after insertion: %%_roles',  ['roles' => implode(', ', $new_roles) ?: 'n/a']);
        }
    }

}
