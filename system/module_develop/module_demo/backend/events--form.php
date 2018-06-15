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
    $items['#select']->option_insert('Option 5 (inserted + disabled from code)', 'option_5', ['disabled' => 'disabled'], 'group_1');
    $items['#select']->option_insert('Option 6 (inserted from code)', 'option_6', [], 'group_1');
    $items['#select']->optgroup_insert('group_2', 'Group 2 (inserted from code)');
    $items['#select']->option_insert('Option 7 (inserted from code)', 'option_7', [], 'group_2');
    $items['#select']->option_insert('Option 8 (inserted from code)', 'option_8', [], 'group_2');
    $items['#select']->option_insert('Option 9 (inserted from code)', 'option_9', [], 'group_2');
    $items['form_elements/radiobuttons_all/palette']->value_set('modernblue');
    $items['#file']->pool_values_init_old(
      data::select('files_demo') ?: []
    );
  }

  static function on_validate_demo($form, $items) {
  }

  static function on_submit_demo($form, $items, &$values) {
    $def_value_checkboxes = [0 => '', 1 => 'checkboxes_2', 2 => '', 3 => 'checkboxes_4'];
    $def_value_email = 'test1@example.com,test2@example.com';
    $def_value_select = ['option_1' => 'Option 1 (selected)'];
    if ($items['#text']->value_get() != 'text in input')             message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['#text']->title)]));            # effcore\field_text
    if ($items['#password']->value_get() != 'text in password')      message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['#password']->title)]));        # effcore\field_password
    if ($items['#search']->value_get() != 'text in search')          message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['#search']->title)]));          # effcore\field_search
    if ($items['#url']->value_get() != 'http://example.com')         message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['#url']->title)]));             # effcore\field_url
    if ($items['#phone']->value_get() != '+000112334455')            message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['#phone']->title)]));           # effcore\field_phone
    if ($items['#email']->value_get() != $def_value_email)           message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['#email']->title)]));           # effcore\field_email
    if ($items['#number']->value_get() != '0')                       message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['#number']->title)]));          # effcore\field_number
    if ($items['#range']->value_get() != '0')                        message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['#range']->title)]));           # effcore\field_range
    if ($items['#date']->value_get() != '2020-01-01')                message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['#date']->title)]));            # effcore\field_date
    if ($items['#time']->value_get() != '01:23:45')                  message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['#time']->title)]));            # effcore\field_time
    if ($items['#timezone']->values_get() != [424 => 'UTC'])         message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['#timezone']->title)]));        # effcore\field_timezone
    if ($items['#color']->value_get() != '#ffffff')                  message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['#color']->title)]));           # effcore\field_color
    if ($items['#textarea']->value_get() != 'text in textarea')      message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['#textarea']->title)]));        # effcore\field_textarea
    if ($items['#select']->values_get() != $def_value_select)        message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['#select']->title)]));          # effcore\field_select
    if ($items['#checkbox']->value_get() != 'checkbox')              message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['#checkbox']->title)]));        # effcore\field_checkbox
    if ($items['#checkboxes'][0]->value_get() != '')                 message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['#checkboxes'][0]->title)]));   # effcore\field_checkbox
    if ($items['#checkboxes'][1]->value_get() != 'checkboxes_2')     message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['#checkboxes'][1]->title)]));   # effcore\field_checkbox
    if ($items['#checkboxes'][2]->value_get() != '')                 message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['#checkboxes'][2]->title)]));   # effcore\field_checkbox
    if ($items['#checkboxes'][3]->value_get() != 'checkboxes_4')     message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['#checkboxes'][3]->title)]));   # effcore\field_checkbox
    if ($items['#radiobutton']->value_get() != '')                   message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['#radiobutton']->title)]));     # effcore\field_radiobutton
    if ($items['#radiobuttons'][0]->value_get() != '')               message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['#radiobuttons'][0]->title)])); # effcore\field_radiobutton
    if ($items['#radiobuttons'][1]->value_get() != 'radiobuttons_2') message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['#radiobuttons'][1]->title)])); # effcore\field_radiobutton
    if ($items['#radiobuttons'][2]->value_get() != '')               message::insert(translation::get('Field "%%_title" has a changed value.', ['title' => translation::get($items['#radiobuttons'][2]->title)])); # effcore\field_radiobutton
    if ($items['form_elements/checkboxes_all/checkboxes']->values_get() != $def_value_checkboxes) message::insert(translation::get('Group "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/checkboxes_all/checkboxes']->title)]));     # effcore\group_checkboxes
    if ($items['form_elements/radiobuttons_all/radiobuttons']->value_get() != 'radiobuttons_2')   message::insert(translation::get('Group "%%_title" has a changed value.', ['title' => translation::get($items['form_elements/radiobuttons_all/radiobuttons']->title)])); # effcore\group_radiobuttons
  # save the files
    $paths = [];
    foreach ($items['#file']->pool_files_save() as $c_info) {
      $paths[] = (new file($c_info->path))->path_relative_get();
    }
    if (count($paths)) data::update('files_demo', $paths);
    else               data::delete('files_demo');
  # show the message
    message::insert(
      translation::get('Call %%_name', ['name' => '\\'.__METHOD__])
    );
  }

}}