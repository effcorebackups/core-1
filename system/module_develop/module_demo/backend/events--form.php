<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use \effcore\dynamic;
          use \effcore\file;
          use \effcore\message;
          use \effcore\translation;
          abstract class events_form extends \effcore\events_form {

  ##################
  ### form: demo ###
  ##################

  static function on_init_demo($form, $fields, &$values) {
    $fields['form_elements/select_macro']->option_insert('Option 2.5 (inserted + disabled from init)', 'option_2_5', ['disabled' => 'disabled'], 'group_2_1');
    $fields['form_elements/select_macro']->option_insert('Option 2.6 (inserted from init)', 'option_2_6', [], 'group_2_1');
    $fields['form_elements/select_macro']->optgroup_insert('group_2_2', 'Group 2.2 (inserted from init)');
    $fields['form_elements/select_macro']->option_insert('Option 2.7 (inserted from init)', 'option_2_7', [], 'group_2_2');
    $fields['form_elements/select_macro']->option_insert('Option 2.8 (inserted from init)', 'option_2_8', [], 'group_2_2');
    $fields['form_elements/select_macro']->option_insert('Option 2.9 (inserted from init)', 'option_2_9', [], 'group_2_2');
    $fields['form_elements/file']->pool_values_init_old(
      dynamic::select('files_demo') ?: []
    );
  }

  static function on_validate_demo($form, $fields, &$values) {
  }

  static function on_submit_demo($form, $fields, &$values) {
    $paths = [];
    foreach ($fields['form_elements/file']->pool_files_save() as $c_info) {
      $c_file = new file($c_info->path);
      $paths[] = $c_file->get_path_relative();
    }
    if (count($paths)) dynamic::update('files_demo', $paths);
    else               dynamic::delete('files_demo');
    message::insert(translation::get('Call %%_name', ['name' => '\\'.__METHOD__]));
  }

}}