<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use \effcore\message;
          use \effcore\modules\core\events_module_update as core_events_module_update;
          use \effcore\text;
          abstract class events_module_update {

  # ──────────────────────────────────────────────────────────────────────────────
  # to activate the files update through the repository:
  # ══════════════════════════════════════════════════════════════════════════════
  # - create "bundle.data" with repository information
  # - bind the new bundle with its modules - set "id_bundle" in each "module.data"
  # - create "events.data" if it does not exist
  # - in "events.data" register a new event "on_update_files"
  # - create "events--module_update.php" in the main bundle module
  # - in "events--module_update.php" create a new event handler "on_update_files"
  # ──────────────────────────────────────────────────────────────────────────────

  static function on_update_files($event, $bundle_id) {
    return core_events_module_update::on_update_files__git($event, $bundle_id); # ← unregistered event (for demonstration only)
  }

  static function on_repo_restore($event, $bundle_id) {
    return core_events_module_update::on_repo_restore__git($event, $bundle_id); # ← unregistered event (for demonstration only)
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  static function on_update_data_before($event, $update) {
    message::insert(new text('Call "%%_call" for #%%_number', ['call' => $event->handler, 'number' => $update->number]));
  }

  static function on_update_data_after($event, $update) {
    message::insert(new text('Call "%%_call" for #%%_number', ['call' => $event->handler, 'number' => $update->number]));
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  static function on_update_data_1000($update) {
    message::insert(new text('Call "%%_call"', ['call' => $update->handler]));
    return true;
  }

  static function on_update_data_1001($update) {
    message::insert(new text('Call "%%_call"', ['call' => $update->handler]));
    return true;
  }

  static function on_update_data_1002($update) {
    message::insert(new text('Call "%%_call"', ['call' => $update->handler]));
    return true;
  }

}}