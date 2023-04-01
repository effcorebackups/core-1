<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          abstract class events_examples {

  static function on_block_presets_dynamic_build($event, $id = null) {}
  static function on_breadcrumbs_build_before   ($event, $breadcrumbs) {}
  static function on_breadcrumbs_build_after    ($event, $breadcrumbs) {}
  static function on_cron_run                   ($event) {}
  static function on_decorator_build_before     ($event, $decorator) {}
  static function on_decorator_build_after      ($event, $decorator) {}
  static function on_email_send_before          ($event, $form, $items, &$to, &$subject, &$body, &$from, &$encoding) {}
  static function on_form_init                  ($event, $form, $items) {}
  static function on_form_validate              ($event, $form, $items) {}
  static function on_form_submit                ($event, $form, $items) {}
  static function on_instance_select_before     ($event, $instance) {}
  static function on_instance_insert_before     ($event, $instance) {}
  static function on_instance_update_before     ($event, $instance) {}
  static function on_instance_delete_before     ($event, $instance) {}
  static function on_instance_select_after      ($event, $instance, $result) {}
  static function on_instance_insert_after      ($event, $instance, $result) {}
  static function on_instance_update_after      ($event, $instance, $result) {}
  static function on_instance_delete_after      ($event, $instance, $result) {}
  static function on_module_enable              ($event) {}
  static function on_module_disable             ($event) {}
  static function on_module_install             ($event) {}
  static function on_module_uninstall           ($event) {}
  static function on_module_start               ($event) {}
  static function on_module_update_data_before  ($event, $update) {} # see: \effcore\modules\demo\events_module_update::on_update_data_before
  static function on_module_update_data_after   ($event, $update) {} # see: \effcore\modules\demo\events_module_update::on_update_data_after
  static function on_page_build_before          ($event, $page) {}
  static function on_page_build_after           ($event, $page) {}
  static function on_page_render_before         ($event, $page, $template) {}
  static function on_query_before               ($event, $storage, $query) {}                      # see: \effcore\modules\develop\events_storage::on_query_before
  static function on_query_after                ($event, $storage, $query, $statement, $errors) {} # see: \effcore\modules\develop\events_storage::on_query_after
  static function on_repo_restore               ($event, $bundle_id) {}                            # see: \effcore\modules\core\events_module_update::on_repo_restore
  static function on_selection_build_before     ($event, $selection) {}
  static function on_selection_build_after      ($event, $selection) {}
  static function on_session_insert_after       ($event, $id_user, $id_session, $params) {}
  static function on_session_delete_before      ($event, $id_user, $id_session) {}
  static function on_storage_init_before        ($event, $storage) {} # see: \effcore\modules\develop\events_storage::on_init_before
  static function on_storage_init_after         ($event, $storage) {} # see: \effcore\modules\develop\events_storage::on_init_after
  static function on_tab_build_before           ($event, $tab) {}
  static function on_tab_build_after            ($event, $tab) {}
  static function on_tree_build_before          ($event, $tree) {}
  static function on_tree_build_after           ($event, $tree) {}
  static function on_update_files               ($event, $bundle_id) {} # see: \effcore\modules\core\events_module_update::on_update_files

}}
