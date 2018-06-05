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
  public $max_lenght_name = 244;
  public $max_lenght_type = 10;
  public $allowed_types = [];
  public $allowed_chars = 'a-z0-9_\\.\\-';
  public $upload_dir = '';
  public $fixed_name;
  public $fixed_type;
  protected $pool_old = [];
  protected $pool_new = [];

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

  function pool_values_init_old($old_values = []) {
    $this->pool_old = [];
  # insert old values to the pool
    foreach ($old_values as $c_id => $c_path_relative) {
      $c_file = new file(dir_root.$c_path_relative);
      if ($c_file->is_exist()) {
        $c_info = new \stdClass;
        $c_info->name = $c_file->get_name();
        $c_info->type = $c_file->get_type();
        $c_info->file = $c_file->get_file();
        $c_info->mime = $c_file->get_mime();
        $c_info->size = $c_file->get_size();
        $c_info->old_path = $c_file->get_path();
        $c_info->error = 0;
        $this->pool_old[$c_id] = $c_info;
      }
    }
  # join deleted items from the cache with deleted items from the form
    $deleted           = $this->pool_validation_cache_get('old_to_delete');
    $deleted_from_form = $this->pool_manager_get_deleted_items('old');
    foreach ($this->pool_old as $c_id => $c_info) {
      if (isset($deleted_from_form[$c_id])) {
        $deleted[$c_id] = new \stdClass;
        $deleted[$c_id]->old_path = $c_info->old_path;
      }
    }
  # virtual delete of canceled values
    foreach ($this->pool_old as $c_id => $c_info) {
      if (isset($deleted[$c_id])) {
        unset($this->pool_old[$c_id]);
      }
    }
  # save the poll
    $this->pool_validation_cache_set('old_to_delete', $deleted);
  # rebuild (refresh) pool manager
    $this->pool_manager_rebuild();
  # disable the field if it has singular value
    $element = $this->child_select('element');
    if (!$element->attribute_select('multiple') && count($this->pool_old) > 0) {
      $element->attribute_insert('disabled', 'disabled');
    }
  }

  function pool_values_init_new($new_values = [], $is_valid) {
  # insert new values to the pool (from the cache)
    $this->pool_new = $this->pool_validation_cache_get('new');
  # insert new values to the pool (from the form)
    if ($is_valid && count($new_values)) {
      foreach ($new_values as $c_new_value) {
        $this->pool_new[] = $c_new_value;
      }
    }
  # move temporary files from php "tmp" directory to system "tmp" directory
    $this->pool_files_move_tmp_to_pre();
  # physically delete of canceled values
    $deleted = $this->pool_manager_get_deleted_items('new');
    foreach ($this->pool_new as $c_id => $c_info) {
      if (isset($deleted[$c_id])) {
        if (isset($this->pool_new[$c_id]->pre_path)) {
           unlink($this->pool_new[$c_id]->pre_path);
            unset($this->pool_new[$c_id]);
        }
      }
    }
  # save the poll
    $this->pool_validation_cache_set('new', $this->pool_new);
  # rebuild (refresh) pool manager
    $this->pool_manager_rebuild();
  }

  function pool_files_save() {
  # delete canceled old values
    $deleted = $this->pool_validation_cache_get('old_to_delete');
    foreach ($deleted as $c_id => $c_info) {
      if (isset($deleted[$c_id])) {
         unlink($deleted[$c_id]->old_path);
      }
    }
  # move new files to the directory "files"
    $this->pool_files_move_pre_to_new();
  # prepare return
    $return = [];
    $return_paths = [];
    foreach ($this->pool_old as $c_info) {$c_info->path = $c_info->old_path; $return[] = $c_info; $c_file = new file($c_info->path); $return_paths[] = $c_file->get_path_relative();}
    foreach ($this->pool_new as $c_info) {$c_info->path = $c_info->new_path; $return[] = $c_info; $c_file = new file($c_info->path); $return_paths[] = $c_file->get_path_relative();}
  # move pool_old to pool_new
    $this->pool_new = [];
    $this->pool_manager_set_deleted_items('old', []);
    $this->pool_validation_cache_set('old_to_delete', []);
    $this->pool_values_init_old($return_paths);
  # return result array
    return $return;
  }

  protected function pool_files_move_tmp_to_pre() {
    $form = $this->get_form();
    foreach ($this->pool_new as $c_id => $c_info) {
      if (isset($c_info->tmp_path)) {
        $src_file = new file($c_info->tmp_path);
        $dst_file = new file(temporary::directory.'validation/'.$form->validation_cache_get_date().'/'.$form->validation_id.'-'.$c_id);
        if ($src_file->move_uploaded(
            $dst_file->get_dirs(),
            $dst_file->get_file())) {
          $c_info->pre_path = $dst_file->get_path();
          unset($c_info->tmp_path);
        } else {
          message::insert(translation::get('Can not copy file from "%%_from" to "%%_to"!',             ['from' => $src_file->get_dirs(), 'to' => $dst_file->get_dirs_relative()]), 'error');
          console::add_log('file', 'copy', 'Can not copy file from "%%_from" to "%%_to"!', 'error', 0, ['from' => $src_file->get_dirs(), 'to' => $dst_file->get_dirs_relative()]);
          unset($this->pool_new[$c_id]);
        }
      }
    }
  }

  protected function pool_files_move_pre_to_new() {
    foreach ($this->pool_new as $c_id => $c_info) {
      if (isset($c_info->pre_path)) {
        $src_file = new file($c_info->pre_path);
        $dst_file = new file(dynamic::dir_files.$this->upload_dir.$c_info->file);
        if ($this->fixed_name) $dst_file->set_name(token::replace($this->fixed_name));
        if ($this->fixed_type) $dst_file->set_type(token::replace($this->fixed_type));
        if ($src_file->move(
            $dst_file->get_dirs(),
            $dst_file->get_file())) {
          $c_info->new_path = $dst_file->get_path();
          unset($c_info->pre_path);
        } else {
          message::insert(translation::get('Can not copy file from "%%_from" to "%%_to"!',             ['from' => $src_file->get_dirs(), 'to' => $dst_file->get_dirs_relative()]), 'error');
          console::add_log('file', 'copy', 'Can not copy file from "%%_from" to "%%_to"!', 'error', 0, ['from' => $src_file->get_dirs(), 'to' => $dst_file->get_dirs_relative()]);
        }
      }
    }
  }

  # ─────────────────────────────────────────────────────────────────────
  # pool validation cache
  # ─────────────────────────────────────────────────────────────────────

  protected function pool_validation_cache_get($type) {
    $name = $this->get_element_name();
    $form = $this->get_form();
    return isset($form->validation_data['pool'][$name][$type]) ?
                 $form->validation_data['pool'][$name][$type] : [];
  }

  protected function pool_validation_cache_set($type, $data) {
    $name = $this->get_element_name();
    $form = $this->get_form();
    $form->validation_data['pool'][$name][$type] = $data;
    if (count($form->validation_data['pool'][$name][$type]) == 0) unset($form->validation_data['pool'][$name][$type]);
    if (count($form->validation_data['pool'][$name])        == 0) unset($form->validation_data['pool'][$name]);
    if (count($form->validation_data['pool'])               == 0) unset($form->validation_data['pool']);
  }

  # ─────────────────────────────────────────────────────────────────────
  # pool manager
  # ─────────────────────────────────────────────────────────────────────

  protected function pool_manager_rebuild() {
    $this->child_delete('manager');
    $pool_manager = new group_checkboxes();
    $pool_manager->build();
    $this->child_insert($pool_manager, 'manager');
  # insert "delete" checkboxes for old and new files
    foreach ($this->pool_old as $c_id => $c_info) $this->pool_manager_insert_action($c_info, $c_id, 'old');
    foreach ($this->pool_new as $c_id => $c_info) $this->pool_manager_insert_action($c_info, $c_id, 'new');
  }

  protected function pool_manager_insert_action($info, $id, $type) {
    $name = $this->get_element_name();
    $pool_manager = $this->child_select('manager');
    $pool_manager->field_insert(
      translation::get('delete file: %%_name', ['name' => $info->file]), ['name' => 'manager_delete_'.$name.'_'.$type.'[]', 'value' => $id]
    );
  }

  protected function pool_manager_get_deleted_items($type) {
    $name = $this->get_element_name();
    return core::array_kmap(
      static::get_new_value_multiple('manager_delete_'.$name.'_'.$type)
    );
  }

  protected function pool_manager_set_deleted_items($type, $items) {
    $name = $this->get_element_name();
    static::set_new_value_multiple('manager_delete_'.$name.'_'.$type, $items);
  }

  ###########################
  ### static declarations ###
  ###########################

  static function sanitize($field, $form, $npath, $element, &$new_values) {
    foreach ($new_values as $c_value) {
      $hex_time = str_pad(dechex(time()),              8, '0', STR_PAD_LEFT);
      $hex_rand = str_pad(dechex(rand(0, 0xffffffff)), 8, '0', STR_PAD_LEFT);
      $c_value->name = core::sanitize_file_part($c_value->name, $field->allowed_chars, $field->max_lenght_name) ?: $hex_time.$hex_rand;
      $c_value->type = core::sanitize_file_part($c_value->type, $field->allowed_chars, $field->max_lenght_type);
      $c_value->file = $c_value->name.($c_value->type ?
                                   '.'.$c_value->type : '');
    }
  }

  static function validate($field, $form, $npath) {
    $element = $field->child_select('element');
    $name = $field->get_element_name();
    $type = $field->get_element_type();
    if ($name && $type) {
      if (static::is_disabled($field, $element)) return true;
      $new_values = static::get_new_files($name);
      static::sanitize($field, $form, $npath, $element, $new_values);
      $result = static::validate_upload  ($field, $form, $npath, $element, $new_values) &&
                static::validate_required($field, $form, $npath, $element, $new_values) &&
                static::validate_multiple($field, $form, $npath, $element, $new_values);
      $field->pool_values_init_new($new_values, $result);
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