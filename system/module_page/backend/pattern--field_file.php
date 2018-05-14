<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_file extends field {

  public $title = 'File';
  public $attributes = ['x-type' => 'file'];
  public $element_attributes_default = [
    'type' => 'file',
    'name' => 'file'
  ];
# ─────────────────────────────────────────────────────────────────────
  public $max_file_size;
  public $upload_dir = '';
  public $fixed_name;
  public $fixed_type;

  function build() {
    parent::build();
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

  function pool_build(&$new_values, $validate_result) {
return;
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
        if ($c_file->move_uploaded(temporary::directory, $c_hash)) {
          $c_new_value->tmp_name = $c_file->get_path();
          $pool[$name][$c_hash] = $c_new_value;
        }
      }
    }
  # deleting selected files
    $delete_items = isset($_POST['manager_delete_'.$name]) ? factory::array_kmap((array)
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
    $this->child_insert(new group_checkboxes(), 'manager');
    $this->child_select('manager')->build();
  }

  function pool_manager_insert_action($info, $hash) {
    $name = $this->get_element_name();
    $this->child_select('manager')->field_insert(
      translation::get('delete file: %%_name', ['name' => $info->name]), ['name' => 'manager_delete_'.$name.'[]', 'value' => $hash]
    );
  }

  function pool_manager_clean() {
    $this->pool_manager_build();
  }

  ###########################
  ### static declarations ###
  ###########################

  static function validate($field, $form, $npath) {
    $element = $field->child_select('element');
    $name = $field->get_element_name();
    $type = $field->get_element_type();
    if ($name && $type) {
      if (static::is_disabled($field, $element)) return true;
      $new_values = static::get_new_files($name);
      $result = static::validate_upload  ($field, $form, $npath, $element, $new_values) &&
                static::validate_required($field, $form, $npath, $element, $new_values) &&
                static::validate_multiple($field, $form, $npath, $element, $new_values);
      $field->pool_build($new_values, $result);
      return $result;
    }
  }

  static function validate_upload($field, $form, $npath, $element, &$new_values) {
    $max_size = $field->get_max_file_size();
    foreach ($new_values as $c_new_value) {
      switch ($c_new_value->error) {
        case UPLOAD_ERR_INI_SIZE   : $form->add_error($npath.'/element', translation::get('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::get($field->title), 'error' => translation::get('the size of uploaded file more than %%_size', ['size' => locale::format_human_bytes($max_size)])])); return;
        case UPLOAD_ERR_FORM_SIZE  : $form->add_error($npath.'/element', translation::get('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::get($field->title), 'error' => translation::get('the size of uploaded file more than MAX_FILE_SIZE (MAX_FILE_SIZE is not supported)')]));             return;
        case UPLOAD_ERR_PARTIAL    : $form->add_error($npath.'/element', translation::get('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::get($field->title), 'error' => translation::get('the uploaded file was only partially uploaded')]));                                                  return;
        case UPLOAD_ERR_NO_TMP_DIR : $form->add_error($npath.'/element', translation::get('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::get($field->title), 'error' => translation::get('missing a temporary directory')]));                                                                  return;
        case UPLOAD_ERR_CANT_WRITE : $form->add_error($npath.'/element', translation::get('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::get($field->title), 'error' => translation::get('failed to write file to disk')]));                                                                   return;
        case UPLOAD_ERR_EXTENSION  : $form->add_error($npath.'/element', translation::get('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::get($field->title), 'error' => translation::get('a php extension stopped the file upload')]));                                                        return;
      }
      if ($c_new_value->error !== UPLOAD_ERR_OK) {$form->add_error($npath.'/element', translation::get('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::get($field->title), 'error' => $c_new_value->error])); return;}
      if ($c_new_value->size === 0)              {$form->add_error($npath.'/element', translation::get('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::get($field->title), 'error' => translation::get('file is empty')])); return;}
      if ($c_new_value->size > $max_size)        {$form->add_error($npath.'/element', translation::get('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::get($field->title), 'error' => translation::get('the size of uploaded file more than %%_size', ['size' => locale::format_human_bytes($max_size)])])); return;}
    }
    return true;
  }

  static function validate_required($field, $form, $npath, $element, &$new_values) {
    if ($element->attribute_select('required') && count($new_values) == 0) {
      $form->add_error($npath.'/element',
        translation::get('Field "%%_title" must be selected!', ['title' => translation::get($field->title)])
      );
    } else {
      return true;
    }
  }

  static function validate_multiple($field, $form, $npath, $element, &$new_values) {
    if (!$element->attribute_select('multiple') && count($new_values) > 1) {
      $form->add_error($npath.'/element',
        translation::get('Field "%%_title" does not support multiple select!', ['title' => translation::get($field->title)])
      );
    } else {
      return true;
    }
  }

}}