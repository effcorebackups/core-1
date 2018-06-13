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

  static function on_validate_demo($form, $items, &$values) {
  }

  static function on_submit_demo($form, $items, &$values) {
    $checkboxes_def_value = [0 => '', 1 => 'checkboxes_2', 2 => '', 3 => 'checkboxes_4'];
    if ($items['form_elements/text']->value_get() != 'text in input')                             message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/text']->title)]));                            # effcore\field_text
    if ($items['form_elements/password']->value_get() != 'text in password')                      message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/password']->title)]));                        # effcore\field_password
    if ($items['form_elements/search']->value_get() != 'text in search')                          message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/search']->title)]));                          # effcore\field_search
    if ($items['form_elements/url']->value_get() != 'http://example.com')                         message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/url']->title)]));                             # effcore\field_url
    if ($items['form_elements/phone']->value_get() != '+000112334455')                            message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/phone']->title)]));                           # effcore\field_phone
    if ($items['form_elements/email']->value_get() != 'test1@example.com,test2@example.com')      message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/email']->title)]));                           # effcore\field_email
    if ($items['form_elements/number']->value_get() != '0')                                       message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/number']->title)]));                          # effcore\field_number
    if ($items['form_elements/range']->value_get() != '0')                                        message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/range']->title)]));                           # effcore\field_range
    if ($items['form_elements/date']->value_get() != '2020-01-01')                                message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/date']->title)]));                            # effcore\field_date
    if ($items['form_elements/time']->value_get() != '01:23:45')                                  message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/time']->title)]));                            # effcore\field_time
    if ($items['form_elements/timezone']->values_get() != [424 => 'UTC'])                         message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/timezone']->title)]));                        # effcore\field_timezone
    if ($items['form_elements/color']->value_get() != '#ffffff')                                  message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/color']->title)]));                           # effcore\field_color
    if ($items['form_elements/textarea']->value_get() != 'text in textarea')                      message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/textarea']->title)]));                        # effcore\field_textarea
    if ($items['form_elements/select']->values_get() != ['option_1' => 'Option 1 (selected)'])    message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/select']->title)]));                          # effcore\field_select
    if ($items['form_elements/checkboxes_all/checkbox']->value_get() != 'checkbox')               message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/checkboxes_all/checkbox']->title)]));         # effcore\field_checkbox
    if ($items['form_elements/checkboxes_all/checkboxes/0']->value_get() != '')                   message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/checkboxes_all/checkboxes/0']->title)]));     # effcore\field_checkbox
    if ($items['form_elements/checkboxes_all/checkboxes/1']->value_get() != 'checkboxes_2')       message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/checkboxes_all/checkboxes/1']->title)]));     # effcore\field_checkbox
    if ($items['form_elements/checkboxes_all/checkboxes/2']->value_get() != '')                   message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/checkboxes_all/checkboxes/2']->title)]));     # effcore\field_checkbox
    if ($items['form_elements/checkboxes_all/checkboxes/3']->value_get() != 'checkboxes_4')       message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/checkboxes_all/checkboxes/3']->title)]));     # effcore\field_checkbox
    if ($items['form_elements/checkboxes_all/checkboxes']->values_get() != $checkboxes_def_value) message::insert(translation::get('Group "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/checkboxes_all/checkboxes']->title)]));       # effcore\group_checkboxes
    if ($items['form_elements/radiobuttons_all/radiobutton']->value_get() != '')                  message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/radiobuttons_all/radiobutton']->title)]));    # effcore\field_radiobutton
    if ($items['form_elements/radiobuttons_all/radiobuttons/0']->value_get() != '')               message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/radiobuttons_all/radiobuttons/0']->title)])); # effcore\field_radiobutton
    if ($items['form_elements/radiobuttons_all/radiobuttons/1']->value_get() != 'radiobuttons_2') message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/radiobuttons_all/radiobuttons/1']->title)])); # effcore\field_radiobutton
    if ($items['form_elements/radiobuttons_all/radiobuttons/2']->value_get() != '')               message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/radiobuttons_all/radiobuttons/2']->title)])); # effcore\field_radiobutton
    if ($items['form_elements/radiobuttons_all/radiobuttons']->value_get() != 'radiobuttons_2')   message::insert(translation::get('Group "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/radiobuttons_all/radiobuttons']->title)]));   # effcore\group_radiobuttons
  # save the files
    $paths = [];
    foreach ($items['form_elements/file']->pool_files_save() as $c_info) {
      $c_file = new file($c_info->path);
      $paths[] = $c_file->path_relative_get();
    }
    if (count($paths)) data::update('files_demo', $paths);
    else               data::delete('files_demo');
  # show the message
    message::insert(
      translation::get('Call %%_name', ['name' => '\\'.__METHOD__])
    );
  }

}}