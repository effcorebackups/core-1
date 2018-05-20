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
  public $allowed_types = [];
  public $pool_old = [];
  public $pool_new = [];

  function build() {
    parent::build();
    $this->description = translation::get('Maximal file size: %%_value.', [
      'value' => locale::format_human_bytes($this->get_max_file_size())
    ]);
  }

  function get_max_file_size() {
    $bytes_1 = core::is_human_bytes($this->max_file_size) ?
               core::human_to_bytes($this->max_file_size) : $this->max_file_size;
    $bytes_2 = core::is_human_bytes(ini_get('upload_max_filesize')) ?
               core::human_to_bytes(ini_get('upload_max_filesize')) : ini_get('upload_max_filesize');
    return min($bytes_1, $bytes_2);
  }

  ############
  ### pool ###
  ############

  function pool_init($old_values = []) {
    foreach ($old_values as $c_path_relative) {
      if ($c_path_relative) {
        $c_file = new file(dir_root.$c_path_relative);
        $c_info = new \stdClass;
        $c_info->name = $c_file->get_name();
        $c_info->type = $c_file->get_type();
        $c_info->file = $c_file->get_file();
        $c_info->mime = $c_file->get_mime();
        $c_info->size = $c_file->get_size();
        $c_info->old_path = $c_file->get_path();
        $c_info->error = 0;
        $this->pool_old[] = $c_info;
      }
    }
    $this->pool_manager_init();
  }

  function pool_rebuild_after_validate($new_values, $is_valid) {
    $form = $this->get_form();
    $this->pool_new = $this->pool_validation_cache_select();
  # remove new values from the pool
    foreach ($this->pool_manager_get_removed_items('new') as $c_id) {
      if (isset($this->pool_new[$c_id]->pre_path))
         unlink($this->pool_new[$c_id]->pre_path);
      unset($this->pool_new[$c_id]);
    }
  # add new values to the pool
    if ($is_valid && count($new_values)) {
      foreach ($new_values as $c_new_value) {
        $this->pool_new[] = $c_new_value;
      }
    }
  # pre-save the new files
    foreach ($this->pool_new as $c_id => $c_info) {
      if (isset($c_info->tmp_path)) {
        $c_tmp_file = new file($c_info->tmp_path);
        $c_pre_file = new file(temporary::directory.$form->validation_id.'-'.$c_id);
        if ($c_tmp_file->move_uploaded($c_pre_file->get_dirs(), $c_pre_file->get_file())) {
          $c_info->pre_path = $c_pre_file->get_path();
          unset($c_info->tmp_path);
        } else {
          message::insert(translation::get('Can not copy file from "%%_from" to "%%_to"!', ['from' => $c_tmp_file->get_dirs(), 'to' => $c_pre_file->get_dirs()]), 'error');
          console::add_log('file', 'copy', 'Can not copy file from "%%_from" to "%%_to"!', 'error', 0, ['from' => $c_tmp_file->get_path(), 'to' => $c_pre_file->get_path()]);
          unset($this->pool_new[$c_id]);
        }
      }
    }
  # save the pool
    $this->pool_validation_cache_update($this->pool_new);
    $this->pool_manager_rebuild();
  }

  function pool_files_save() {
    $return = [];
    $this->pool_new = $this->pool_validation_cache_select();
    foreach ($this->pool_new as $c_info) {
    # @todo: add increment number for duplicates
      $c_pre_file = new file($c_info->pre_path);
      $c_new_file = new file(dynamic::directory_files.$this->upload_dir.$c_info->file);
      if ($this->fixed_name) $c_new_file->set_name(token::replace($this->fixed_name));
      if ($this->fixed_type) $c_new_file->set_type(token::replace($this->fixed_type));
      if ($c_pre_file->move($c_new_file->get_dirs(), $c_new_file->get_file())) {
        $c_info->new_path = $c_new_file->get_path();
        unset($c_info->pre_path);
        $return[] = $c_info;
      } else {
        message::insert(translation::get('Can not copy file from "%%_from" to "%%_to"!', ['from' => $c_pre_file->get_dirs(), 'to' => $c_new_file->get_dirs()]), 'error');
        console::add_log('file', 'copy', 'Can not copy file from "%%_from" to "%%_to"!', 'error', 0, ['from' => $c_pre_file->get_path(), 'to' => $c_new_file->get_path()]);
      }
    }
    $this->pool_manager_rebuild();
    return $return;
  }

  # ─────────────────────────────────────────────────────────────────────
  # pool validation cache
  # ─────────────────────────────────────────────────────────────────────

  function pool_validation_cache_select() {
    $name = $this->get_element_name();
    $form = $this->get_form();
    return isset($form->validation_data['pool'][$name]) ?
                 $form->validation_data['pool'][$name] : [];
  }

  function pool_validation_cache_update($cache) {
    $name = $this->get_element_name();
    $form = $this->get_form();
    $form->validation_data['pool'][$name] = $cache;
    if (count($form->validation_data['pool'][$name]) == 0) unset($form->validation_data['pool'][$name]);
    if (count($form->validation_data['pool'])        == 0) unset($form->validation_data['pool']);
  }

  # ─────────────────────────────────────────────────────────────────────
  # pool manager
  # ─────────────────────────────────────────────────────────────────────

  function pool_manager_init() {
    $pool_manager = new group_checkboxes();
    $pool_manager->build();
    $this->child_insert($pool_manager, 'manager');
  # insert "remove" checkboxes for old and new files
    foreach ($this->pool_old as $c_id => $c_info) $this->pool_manager_insert_action($c_info, $c_id, 'old');
    foreach ($this->pool_new as $c_id => $c_info) $this->pool_manager_insert_action($c_info, $c_id, 'new');
  }

  function pool_manager_rebuild() {
    $this->child_delete('manager');
    $this->pool_manager_init();
  }

  function pool_manager_insert_action($info, $id, $type) {
    $name = $this->get_element_name();
    $pool_manager = $this->child_select('manager');
    $pool_manager->field_insert(
      translation::get('delete file: %%_name', ['name' => $info->file]), ['name' => 'manager_delete_'.$name.'_'.$type.'[]', 'value' => $id]
    );
  }

  function pool_manager_get_removed_items($type) {
    $name = $this->get_element_name();
    return static::get_new_value_multiple('manager_delete_'.$name.'_'.$type);
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
      $field->pool_rebuild_after_validate($new_values, $result);
      return $result;
    }
  }

  static function validate_upload($field, $form, $npath, $element, &$new_values) {
    $max_size = $field->get_max_file_size();
    foreach ($new_values as $c_new_value) {
      if (count($field->allowed_types) &&
         !isset($field->allowed_types[$c_new_value->type])) {
        $form->add_error($npath.'/element',
          translation::get('Field "%%_title" does not support loading this file type!', ['title' => translation::get($field->title)])
        );
        return;
      }
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