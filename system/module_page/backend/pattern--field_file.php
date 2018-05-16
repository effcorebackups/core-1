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

  function pool_build($form, $new_values, $npath, $is_valid) {
    $name = $this->get_element_name();
    $pool = isset($form->validation_data['pool'][$name]) ?
                  $form->validation_data['pool'][$name] : [];
  # remove old values from the pool
    $removed = static::get_new_value_multiple('manager_delete_'.$name);
    foreach ($removed as $c_id) {
      unset($pool[$c_id]);
    }
  # add new values to the pool
    if ($is_valid && count($new_values)) {
      $pool = array_merge($pool, $new_values);
    }
  # save the pool
    $form->validation_data['pool'][$name] = $pool;
    if (count($form->validation_data['pool'][$name]) == 0) unset($form->validation_data['pool'][$name]);
    if (count($form->validation_data['pool']) == 0)        unset($form->validation_data['pool']);
  # insert "remove" checkboxes for each file
    foreach ($pool as $c_id => $c_file) {
      $this->pool_manager_insert_action($c_file, $c_id);
    }
  }

  function pool_manager_build() {
    $pool_manager = new group_checkboxes();
    $pool_manager->build();
    $this->child_insert($pool_manager, 'manager');
  }

  function pool_manager_insert_action($info, $id) {
    $name         = $this->get_element_name();
    $pool_manager = $this->child_select('manager');
    $pool_manager->field_insert(
      translation::get('delete file: %%_name', ['name' => $info->name]), ['name' => 'manager_delete_'.$name.'[]', 'value' => $id]
    );
  }

  function pool_manager_clean() {
    $this->child_delete('manager');
    $this->pool_manager_build();
  }

  function pool_files_save($form) {
    $name = $this->get_element_name();
    $pool = isset($form->validation_data['pool'][$name]) ?
                  $form->validation_data['pool'][$name] : [];
    foreach ($pool as $c_info) {
      $c_tmp_file = new file($c_info->tmp_name);
      $c_new_file = new file(dynamic::directory_files.$this->upload_dir.$c_info->name);
      if ($this->fixed_name) $c_new_file->set_name(token::replace($this->fixed_name));
      if ($this->fixed_type) $c_new_file->set_type(token::replace($this->fixed_type));
      if ($c_tmp_file->move_uploaded($c_new_file->get_dirs(), $c_new_file->get_file())) {
        $c_info->new_path = $c_new_file->get_path();
      } else {
        message::insert(translation::get('Can not copy file from "%%_from" to "%%_to"!', ['from' => $c_tmp_file->get_dirs(), 'to' => $c_new_file->get_dirs()]), 'error');
        console::add_log('file', 'copy', 'Can not copy file from "%%_from" to "%%_to"!', 'error', 0, ['from' => $c_tmp_file->get_path(), 'to' => $c_new_file->get_path()]);
      }
    }
    $this->pool_manager_clean();
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
      $field->pool_build($form, $new_values, $npath, $result);
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