<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\demo;

use effcore\Message;
use effcore\modules\core\Events_Module_update as Core_Events_Module_update;
use effcore\Text;

abstract class Events_Module_update {

    # ──────────────────────────────────────────────────────────────────────────────
    # to activate the files update through the repository:
    # ══════════════════════════════════════════════════════════════════════════════
    # - create "bundle.data" with repository information
    # - bind the new bundle with its modules - set "id_bundle" in each "module.data"
    # - create "events.data" if it does not exist
    # - in "events.data" register a new event "on_update_files"
    # - create "Events_Module_update.php" in the main bundle module
    # - in "Events_Module_update.php" create a new event handler "on_update_files"
    # ──────────────────────────────────────────────────────────────────────────────

    static function on_update_files($event, $bundle_id) {
        return Core_Events_Module_update::on_update_files__git($event, $bundle_id); # ← unregistered event (for demonstration only)
    }

    static function on_repo_restore($event, $bundle_id) {
        return Core_Events_Module_update::on_repo_restore__git($event, $bundle_id); # ← unregistered event (for demonstration only)
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function on_update_data_before($event, $update) {
        Message::insert(new Text('Call "%%_call" for #%%_number', ['call' => $event->handler, 'number' => $update->number]));
    }

    static function on_update_data_after($event, $update) {
        Message::insert(new Text('Call "%%_call" for #%%_number', ['call' => $event->handler, 'number' => $update->number]));
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function on_update_data_1000($update) {
        Message::insert(new Text('Call "%%_call"', ['call' => $update->handler]));
        return true;
    }

    static function on_update_data_1001($update) {
        Message::insert(new Text('Call "%%_call"', ['call' => $update->handler]));
        return true;
    }

    static function on_update_data_1002($update) {
        Message::insert(new Text('Call "%%_call"', ['call' => $update->handler]));
        return true;
    }

}
