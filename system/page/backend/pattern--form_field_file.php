<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class form_field_file extends form_field {

  public $max_file_size;
  public $upload_subdir = '';
  public $fixed_name;
  public $fixed_type;

  function build() {
    $this->pool_manager_build();
    $this->description = translation::get('Maximal file size: %%_value.', [
      'value' => locale::format_human_bytes($this->get_max_file_size())
    ]);
  }

  function get_max_file_size() {
    $bytes_1 = factory::is_human_bytes($this->max_file_size) ?
               factory::human_to_bytes($this->max_file_size) : $this->max_file_size;
    $bytes_2 = factory::is_human_bytes(ini_get('upload_max_filesize')) ?
               factory::human_to_bytes(ini_get('upload_max_filesize')) : ini_get('upload_max_filesize');
    return min($bytes_1, $bytes_2);
  }

  ############
  ### pool ###
  ############

  function pool_build(&$new_values) {
    $validation_id = form::validation_id_get();
    $pool = temporary::select('files-'.$validation_id) ?: [];
    $name = $this->get_element_name();
    if (!isset($pool[$name]))
               $pool[$name] = [];
    $pool_count_0 = count($pool[$name]);
  # move uploaded files to "dynamic/tmp" directory and adding to the pool
    foreach ($new_values as $c_new_value) {
      if (is_uploaded_file($c_new_value->tmp_name)) {
        $c_file = new file($c_new_value->tmp_name);
        $c_hash = $c_file->get_hash();
        if ($c_file->move_uploaded(dir_dynamic.'tmp/', $c_hash)) {
          $c_new_value->tmp_name = $c_file->get_path();
          $c_new_value->name = factory::filter_file_name($c_new_value->name);
          $c_new_value->type = factory::filter_mime_type($c_new_value->type);
          $pool[$name][$c_hash] = $c_new_value;
        }
      }
    }
  # deleting selected files
    $delete_items = isset($_POST['manager_delete_'.$name]) ? factory::array_values_map_to_keys(
                          $_POST['manager_delete_'.$name]) : [];
    foreach ($pool[$name] as $c_hash => $c_file_info) {
      if (isset($delete_items[$c_hash])) {
        unlink($pool[$name][$c_hash]->tmp_name);
         unset($pool[$name][$c_hash]);
      }
    }
  # save the pool
    if (count($pool[$name]) ||
       (count($pool[$name]) == 0 && $pool_count_0 > 0)) {
      temporary::update('files-'.$validation_id, $pool);
    }
  # organize the pool manager
    foreach ($pool[$name] as $c_hash => $c_file_info) {
      $this->pool_manager_insert_action($c_file_info, $c_hash);
    }
  # reflect pool to new_values
    $new_values = $pool[$name];
  }

  ####################
  ### pool manager ###
  ####################

  function pool_manager_build() {
    $this->child_insert(new form_checkboxes(), 'manager');
    $this->child_select('manager')->build();
  }

  function pool_manager_insert_action($info, $hash) {
    $element_name = $this->child_select('element')->attribute_select('name');
    $this->child_select('manager')->input_insert(
      translation::get('delete file: %%_name', ['name' => $info->name]), ['name' => 'manager_delete_'.$element_name, 'value' => $hash]
    );
  }

  function pool_manager_clean() {
    $this->pool_manager_build();
  }

}}