<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Test_step_Set_role_permissions {

    public $id_role;
    public $permissions = [];
    public $is_reset = false;

    function run(&$test, $dpath) {
        yield new Text_simple('');
        yield Test_message::send_dpath($dpath);
        yield new Text('changing permissions for role = "%%_role"', ['role' => $this->id_role]); if ($this->is_reset)
        yield new Text('there will be try to delete all permissions');
        yield new Text('there will be try to append permissions: %%_permissions', ['permissions' => implode(', ', $this->permissions) ?: 'n/a']);

        $old_permissions = Role::related_permissions_select($this->id_role); if ($this->is_reset)
                           Role::related_permissions_delete($this->id_role);
                           Role::related_permissions_insert($this->id_role, $this->permissions, 'test');
        $new_permissions = Role::related_permissions_select($this->id_role);

        yield new Text('permissions before appending: %%_permissions', ['permissions' => implode(', ', $old_permissions) ?: 'n/a']);
        yield new Text( 'permissions after appending: %%_permissions', ['permissions' => implode(', ', $new_permissions) ?: 'n/a']);
    }

}
