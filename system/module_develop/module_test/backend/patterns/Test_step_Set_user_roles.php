<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Test_step_Set_user_roles {

    public $nickname;
    public $roles = [];
    public $is_reset = false;

    function run(&$test, $dpath) {
        yield new Text_simple('');
        yield Test_message::send_dpath($dpath);

        $nickname = Token::apply($this->nickname);
        $user = (new Instance('user', [
            'nickname' => $nickname
        ]))->select();

        if ($user) {
            yield new Text('changing roles for user with nickname = "%%_nickname"', ['nickname' => $nickname]);
            yield new Text('found user ID = "%%_id_user" by nickname = "%%_nickname"', ['id_user' => $user->id, 'nickname' => $nickname]); if ($this->is_reset)
            yield new Text('there will be try to delete all roles');
            yield new Text('there will be try to append roles: %%_roles', ['roles' => implode(', ', $this->roles) ?: 'n/a']);

            $old_roles = User::related_roles_select($user->id); if ($this->is_reset)
                         User::related_roles_delete($user->id);
                         User::related_roles_insert($user->id, $this->roles, 'test');
            $new_roles = User::related_roles_select($user->id);

            yield new Text('roles before appending: %%_roles', ['roles' => implode(', ', $old_roles) ?: 'n/a']);
            yield new Text( 'roles after appending: %%_roles', ['roles' => implode(', ', $new_roles) ?: 'n/a']);
        } else {
            yield new Text('Unknown user!');
            yield Test::FAILED;
        }
    }

}
