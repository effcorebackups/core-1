<?php

######################################################################
### Copyright © 20NN—20NN Author/Rightholder. All rights reserved. ###
######################################################################

namespace effcore\modules\profile_classic;

use effcore\modules\core\Events_Module_update as Core_Events_Module_update;

abstract class Events_Module_update {

    static function on_update_files($event, $bundle_id) {
        return Core_Events_Module_update::on_update_files__git($event, $bundle_id);
    }

    static function on_repo_restore($event, $bundle_id) {
        return Core_Events_Module_update::on_repo_restore__git($event, $bundle_id);
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function on_update_data_1000($update) {
        return true;
    }

}
