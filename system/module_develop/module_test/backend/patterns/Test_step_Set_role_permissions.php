<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Test_step_Set_role_permissions {

    public $id_role;
    public $permissions = [];
    public $is_reset = false;

    function run(&$test, $dpath, &$c_results) {
        if (true           ) $c_results['reports'][$dpath]['dpath'] = '### dpath: '.$dpath;
        if (true           ) $c_results['reports'][$dpath][] = new Text('changing permissions for role = "%%_role"', ['role' => $this->id_role]);
        if ($this->is_reset) $c_results['reports'][$dpath][] = new Text('there will be try to delete all permissions');
        if (true           ) $c_results['reports'][$dpath][] = new Text('there will be try to insert permissions: %%_permissions', ['permissions' => implode(', ', $this->permissions) ?: 'n/a']);
        $old_permissions  =  Role::related_permissions_select($this->id_role);
        if ($this->is_reset) Role::related_permissions_delete($this->id_role);
                             Role::related_permissions_insert($this->id_role, $this->permissions, 'test');
        $new_permissions  =  Role::related_permissions_select($this->id_role);
        $c_results['reports'][$dpath][] = new Text('permissions before insertion: %%_permissions', ['permissions' => implode(', ', $old_permissions) ?: 'n/a']);
        $c_results['reports'][$dpath][] = new Text('permissions after insertion: %%_permissions',  ['permissions' => implode(', ', $new_permissions) ?: 'n/a']);
    }

}
