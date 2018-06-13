<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use \effcore\data;
          use \effcore\file;
          use \effcore\message;
          use \effcore\translation;
          abstract class events_form extends \effcore\events_form {

  ##################
  ### form: demo ###
  ##################

  static function on_init_demo($form, $items) {
    $items['form_elements/select']->option_insert('Option 5 (inserted + disabled from code)', 'option_5', ['disabled' => 'disabled'], 'group_1');
    $items['form_elements/select']->option_insert('Option 6 (inserted from code)', 'option_6', [], 'group_1');
    $items['form_elements/select']->optgroup_insert('group_2', 'Group 2 (inserted from code)');
    $items['form_elements/select']->option_insert('Option 7 (inserted from code)', 'option_7', [], 'group_2');
    $items['form_elements/select']->option_insert('Option 8 (inserted from code)', 'option_8', [], 'group_2');
    $items['form_elements/select']->option_insert('Option 9 (inserted from code)', 'option_9', [], 'group_2');
    $items['form_elements/file']->pool_values_init_old(
      data::select('files_demo') ?: []
    );
  }

  static function on_validate_demo($form, $fields, &$values) {
  }

  static function on_submit_demo($form, $fields, &$values) {
    $paths = [];
    foreach ($fields['form_elements/file']->pool_files_save() as $c_info) {
      $c_file = new file($c_info->path);
      $paths[] = $c_file->path_relative_get();
    }
    if (count($paths)) data::update('files_demo', $paths);
    else               data::delete('files_demo');
    message::insert(
      translation::get('Call %%_name', ['name' => '\\'.__METHOD__])
    );
  }

}}