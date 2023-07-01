<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\user;

use effcore\Frontend;

abstract class Events_Page {

    static function on_tree_build_after($event, $tree) {
        if (strpos($tree->id, 'user') === 0) {
            if (!Frontend::select('tree_user__user'))
                 Frontend::insert('tree_user__user', null, 'styles', ['path' => 'frontend/tree-user.cssd', 'attributes' => ['rel' => 'stylesheet', 'media' => 'all'], 'weight' => -100], 'tree_style', 'user');
        }
    }

}
