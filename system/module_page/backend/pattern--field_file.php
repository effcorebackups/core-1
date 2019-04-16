<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_file extends field {

  # note:
  # ══════════════════════════════════════════════════════════════════════════════════════════
  # 1. if one file from new uploaded set of files has an error, all set will be rejected
  # 2. removing process is undepended from other even if a new uploaded set has an error
  # 3. the new files which marked as 'removed' will be removed in 'on_validate'
  # 4. the old files which marked as 'removed' will be removed in 'on_submit'
  # ──────────────────────────────────────────────────────────────────────────────────────────

  # on_init         ╔════════ form ════════╗       ╔═ pool_old ═╗   ╔════════ form ════════╗
  #                 ║ ┌── upload field ──┐ ║   ┌──▶║ old file 1 ║   ║ ┌── upload field ──┐ ║
  #                 ║ │------------------│ ║   │   ╚════════════╝   ║ │------------------│ ║
  #             ┌─────│   + new file 1   │ ║   │          │     ┌─────│   + new file 2   │ ║
  #             │   ║ └──────────────────┘ ║   │          │     │   ║ └──────────────────┘ ║
  #             │   ╚══════════════════════╝   │          └─────│────▶ ▣ delete old file 1──────────┐
  #             │                              │                │   ╚══════════════════════╝        │
  #             │                              │          ┌─────┘                                   │
  # ............│..............................│..........│.........................................│.........
  # on_validate │                              │          │                                         │
  #             ▼                              │          ▼                                         ▼
  #      ╔═ pool_new ═╗                        │   ╔═ pool_new ═╗                          ╔═ old_to_delete ═╗
  #      ║ new file 1 ║                        │   ║ new file 2 ║                          ║    old file 1   ║
  #      ╚════════════╝                        │   ╚════════════╝                          ╚═════════════════╝
  #             │                              │          │                                         │
  #             │   ╔════════ form ════════╗   │          │         ╔════════ form ════════╗        │
  #             │   ║ ┌── upload field ──┐ ║   │          │         ║ ┌── upload field ──┐ ║        │
  #             │   ║ │------------------│ ║   │          │         ║ │------------------│ ║        │
  #             │   ║ └──────────────────┘ ║   │          │         ║ └──────────────────┘ ║        │
  #             ├────◇ □ delete new file 1 ║   │          ├──────────◇ □ delete new file 2 ║        │
  #             │   ╚══════════════════════╝   │          │         ╚══════════════════════╝        │
  #             │                              │          │                                         │
  # ............│..............................│..........│.........................................│........
  # on_submit   │                              │          │                                         │
  #             ▼                              │          ▼                                         ▼
  #      ╔══ storage ══╗                       │   ╔══ storage ══╗                         ┌ ─ ─ ─ ─ ─ ─ ─ ─
  #      ║    file 1   ║───────────────────────┘   ║    file 2   ║                           delete process │
  #      ╚═════════════╝                           ╚═════════════╝                         └ ─ ─ ─ ─ ─ ─ ─ ─

  public $title = 'File';
  public $attributes = ['data-type' => 'file'];
  public $element_attributes = [
    'type' => 'file',
    'name' => 'file'
  ];
# ─────────────────────────────────────────────────────────────────────
  public $max_file_size = '10K';
  public $min_files_number = 0;
  public $max_files_number = 1;
  public $max_length_name = 227; # = 255 - strlen('-TimestmpRandom_v.') - max_length_type
  public $max_length_type =  10;
  public $allowed_types = [];
  public $allowed_characters = 'a-zA-Z0-9_\\-\\.';
  public $allowed_characters_title = '"a-z", "A-Z", "0-9", "_", "-", "."';
  public $upload_dir = '';
  public $fixed_name;
  public $fixed_type;
# ─────────────────────────────────────────────────────────────────────
  public $pool_old = [];
  public $pool_new = [];

  function file_size_max_get() {
    $bytes_1 = core::is_human_bytes($this->max_file_size) ?
               core::human_to_bytes($this->max_file_size) : $this->max_file_size;
    $bytes_2 = core::is_human_bytes(ini_get('upload_max_filesize')) ?
               core::human_to_bytes(ini_get('upload_max_filesize')) : ini_get('upload_max_filesize');
    return min($bytes_1, $bytes_2);
  }

  function render_description() {
                                                            $result[] = $this->render_description_file_size_max();
    if ($this->min_files_number != $this->max_files_number) $result[] = $this->render_description_file_min_number();
    if ($this->min_files_number != $this->max_files_number) $result[] = $this->render_description_file_max_number();
    if ($this->min_files_number == $this->max_files_number) $result[] = $this->render_description_file_mid_number();
    if ($this->allowed_types                              ) $result[] = $this->render_description_allowed_types();
    if ($this->allowed_characters_title                   ) $result[] = $this->render_description_allowed_characters();
    if ($this->description) $result[] = new markup('p', [], $this->description);
    if (count($result)) {
      if ($this->description_state == 'hidden'                      ) return '';
      if ($this->description_state == 'opened' || $this->has_error()) return                        (new markup($this->description_tag_name, ['id' => $this->id_get() ? 'description-'.$this->id_get() : null], $result))->render();
      if ($this->description_state == 'closed'                      ) return $this->render_opener().(new markup($this->description_tag_name, ['id' => $this->id_get() ? 'description-'.$this->id_get() : null], $result))->render();
      return '';
    }
  }

  function render_description_file_size_max     () {return new markup('p', ['class' => ['file-size-max'           => 'file-size-max'          ]], new text('Maximum file size: %%_value.',                                       ['value'      => locale::format_human_bytes($this->file_size_max_get())]));}
  function render_description_file_min_number   () {return new markup('p', ['class' => ['file-min-number'         => 'file-min-number'        ]], new text('Field can contain a minimum of %%_number file%%_plural{number,s}.',  ['number'     =>               $this->min_files_number        ]));         }
  function render_description_file_max_number   () {return new markup('p', ['class' => ['file-max-number'         => 'file-max-number'        ]], new text('Field can contain a maximum of %%_number file%%_plural{number,s}.',  ['number'     =>               $this->max_files_number        ]));         }
  function render_description_file_mid_number   () {return new markup('p', ['class' => ['file-mid-number'         => 'file-mid-number'        ]], new text('Field must contain %%_number file%%_plural{number,s}.',              ['number'     =>               $this->min_files_number        ]));         }
  function render_description_allowed_types     () {return new markup('p', ['class' => ['file-allowed-types'      => 'file-allowed-types'     ]], new text('File can only be of the next types: %%_types.',                      ['types'      => implode(', ', $this->allowed_types          )]));         }
  function render_description_allowed_characters() {return new markup('p', ['class' => ['file-allowed-characters' => 'file-allowed-characters']], new text('File name can contain only the next characters: %%_characters.',     ['characters' =>               $this->allowed_characters_title]));         }

  ############
  ### pool ###
  ############

  function pool_values_init_old_from_storage($old_values = []) {
    $this->pool_old = [];
  # insert old items to the pool
    foreach ($old_values as $c_id => $c_path_relative) {
      $c_file = new file(dir_root.$c_path_relative);
      if ($c_file->is_exist()) {
        $c_info = new \stdClass;
        $c_info->name = $c_file->name_get();
        $c_info->type = $c_file->type_get();
        $c_info->file = $c_file->file_get();
        $c_info->mime = $c_file->mime_get();
        $c_info->size = $c_file->size_get();
        $c_info->old_path = $c_file->path_get();
        $c_info->error = 0;
        $this->pool_old[$c_id] = $c_info;
      }
    }
  # join deleted items from the cache with deleted items from the form
    $deleted           = $this->pool_validation_cache_get('old_to_delete');
    $deleted_from_form = $this->pool_manager_deleted_items_get('old');
    foreach ($this->pool_old as $c_id => $c_info) {
      if (isset($deleted_from_form[$c_id])) {
        $deleted[$c_id] = new \stdClass;
        $deleted[$c_id]->old_path = $c_info->old_path;
      }
    }
  # virtual delete the items which marked as 'deleted'
    foreach ($this->pool_old as $c_id => $c_info) {
      if (isset($deleted[$c_id])) {
        unset($this->pool_old[$c_id]);
      }
    }
  # save the poll
    $this->pool_validation_cache_set('old_to_delete', $deleted);
  # update pool manager
    $this->pool_manager_rebuild();
  }

  function pool_values_init_new_from_cache() {
    $this->pool_new = $this->pool_validation_cache_get('new');
  # physically delete the items which marked as 'deleted'
    $deleted_from_form = $this->pool_manager_deleted_items_get('new');
    foreach ($this->pool_new as $c_id => $c_info) {
      if (isset($deleted_from_form[$c_id])) {
        if (isset($this->pool_new[$c_id]->pre_path)) {
          @unlink($this->pool_new[$c_id]->pre_path);
            unset($this->pool_new[$c_id]);
        }
      }
    }
  # save the poll
    $this->pool_validation_cache_set('new', $this->pool_new);
  # update pool manager
    $this->pool_manager_rebuild();
  }

  function pool_values_init_new_from_form($new_values = []) {
    foreach ($new_values as $c_new_value) {
      $this->pool_new[] = $c_new_value;
    }
  # move temporary items from php 'tmp' directory to system 'tmp' directory
    $this->pool_files_move_tmp_to_pre();
  # save the poll
    $this->pool_validation_cache_set('new', $this->pool_new);
  # update pool manager
    $this->pool_manager_rebuild();
  }

  # ─────────────────────────────────────────────────────────────────────
  # moving the files in different situations
  # ─────────────────────────────────────────────────────────────────────

  function pool_files_save() {
  # delete the old deleted items
    $deleted = $this->pool_validation_cache_get('old_to_delete');
    foreach ($deleted as $c_id => $c_info) {
      if (isset($deleted[$c_id])) {
        @unlink($deleted[$c_id]->old_path);
      }
    }
  # move new items to the directory 'files'
    $this->pool_files_move_pre_to_new();
  # prepare return
    $result = [];
    $result_paths = [];
    foreach ($this->pool_old as $c_info) {$c_info->path = $c_info->old_path; $result[] = $c_info; $c_file = new file($c_info->path); $result_paths[] = $c_file->path_relative_get();}
    foreach ($this->pool_new as $c_info) {$c_info->path = $c_info->new_path; $result[] = $c_info; $c_file = new file($c_info->path); $result_paths[] = $c_file->path_relative_get();}
  # move pool_old to pool_new
    $this->pool_new = [];
    $this->pool_manager_deleted_items_set('old', []);
    $this->pool_validation_cache_set('old_to_delete', []);
    $this->pool_values_init_old_from_storage($result_paths);
  # return result array
    return $result;
  }

  protected function pool_files_move_tmp_to_pre() {
    foreach ($this->pool_new as $c_id => $c_info) {
      if (isset($c_info->tmp_path)) {
        $src_file = new file($c_info->tmp_path);
        $dst_file = new file(temporary::directory.'validation/'.$this->cform->validation_cache_get_date().'/'.$this->cform->validation_id.'-'.$c_id);
        if ($src_file->move_uploaded($dst_file->dirs_get(), $dst_file->file_get())) {
          $c_info->pre_path = $dst_file->path_get();
          unset($c_info->tmp_path);
        } else {
          message::insert(new text_multiline(['Can not copy file from "%%_from" to "%%_to"!', 'Check directory permissions.'], ['from' => $src_file->dirs_get(), 'to' => $dst_file->dirs_relative_get()]), 'error');
          console::log_insert('file', 'copy', 'Can not copy file from "%%_from" to "%%_to"!', 'error', 0,                      ['from' => $src_file->dirs_get(), 'to' => $dst_file->dirs_relative_get()]);
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
        if ($this->fixed_name) $dst_file->name_set(token::replace($this->fixed_name));
        if ($this->fixed_type) $dst_file->type_set(token::replace($this->fixed_type));
        if ($dst_file->is_exist()) {
          $dst_file->name_set(
            $dst_file->name_get().'-'.core::random_part_get()
          );
        }
        if ($src_file->move($dst_file->dirs_get(), $dst_file->file_get())) {
          $c_info->new_path = $dst_file->path_get();
          unset($c_info->pre_path);
        } else {
          message::insert(new text_multiline(['Can not copy file from "%%_from" to "%%_to"!', 'Check directory permissions.'], ['from' => $src_file->dirs_get(), 'to' => $dst_file->dirs_relative_get()]), 'error');
          console::log_insert('file', 'copy', 'Can not copy file from "%%_from" to "%%_to"!', 'error', 0,                      ['from' => $src_file->dirs_get(), 'to' => $dst_file->dirs_relative_get()]);
        }
      }
    }
  }

  # ─────────────────────────────────────────────────────────────────────
  # pool validation cache
  # ─────────────────────────────────────────────────────────────────────

  protected function pool_validation_cache_get($type) {
    $name = $this->name_get();
    return $this->cform->validation_data['pool'][$name][$type] ?? [];
  }

  protected function pool_validation_cache_set($type, $data) {
    $name = $this->name_get();
    $this->cform->validation_data['pool'][$name][$type] = $data;
    if (count($this->cform->validation_data['pool'][$name][$type]) == 0) unset($this->cform->validation_data['pool'][$name][$type]);
    if (count($this->cform->validation_data['pool'][$name])        == 0) unset($this->cform->validation_data['pool'][$name]);
    if (count($this->cform->validation_data['pool'])               == 0) unset($this->cform->validation_data['pool']);
  }

  # ─────────────────────────────────────────────────────────────────────
  # pool manager
  # ─────────────────────────────────────────────────────────────────────

  protected function pool_manager_rebuild() {
    $this->child_delete('manager');
    $pool_manager = new group_checkboxes();
    $pool_manager->build();
    $this->child_insert($pool_manager, 'manager');
  # insert 'delete' checkboxes for the old and the new items
    foreach ($this->pool_old as $c_id => $c_info) $this->pool_manager_insert_action($c_info, $c_id, 'old');
    foreach ($this->pool_new as $c_id => $c_info) $this->pool_manager_insert_action($c_info, $c_id, 'new');
  }

  protected function pool_manager_insert_action($info, $id, $type) {
    $name = $this->name_get();
    $pool_manager = $this->child_select('manager');
    $pool_manager->field_insert(
      translation::get('delete file "%%_name"', ['name' => $info->file]), null, ['name' => 'manager_delete_'.$name.'_'.$type.'[]', 'value' => $id]
    );
  }

  protected function pool_manager_deleted_items_get($type) {
    $name = $this->name_get();
    return core::array_kmap(
      static::request_values_get('manager_delete_'.$name.'_'.$type)
    );
  }

  protected function pool_manager_deleted_items_set($type, $items) {
    $name = $this->name_get();
    static::request_values_set('manager_delete_'.$name.'_'.$type, $items);
  }

  ###########################
  ### static declarations ###
  ###########################

  static function sanitize($field, $form, $element, &$new_values) {
    foreach ($new_values as $c_value) {
      $c_value->name = core::sanitize_file_part($c_value->name, $field->allowed_characters, $field->max_length_name);
      $c_value->type = core::sanitize_file_part($c_value->type, $field->allowed_characters, $field->max_length_type);
      if (!strlen($c_value->name)) $c_value->name = core::random_part_get();
      if (!strlen($c_value->type)) $c_value->type = 'unknown';
      $c_value->file = $c_value->name.'.'.$c_value->type;
    # special case for IIS and Apache
      if ($c_value->file == 'web.config' ||
          $c_value->file == '.htaccess') {
        $c_value->name = core::random_part_get();
        $c_value->type = 'unknown';
        $c_value->file = $c_value->name.'.'.$c_value->type;
      }
    }
  }

  static function validate($field, $form, $npath) {
    $element = $field->child_select('element');
    $name = $field->name_get();
    $type = $field->type_get();
    if ($name && $type) {
      if ($field->disabled_get()) return true;
      $field->pool_values_init_new_from_cache();
      $new_values = static::request_files_get($name);
      static::sanitize($field, $form, $element, $new_values);
      $result = static::validate_multiple($field, $form, $element, $new_values) &&
                static::validate_upload  ($field, $form, $element, $new_values);
      if ($result) $field->pool_values_init_new_from_form($new_values);
      return $result;
    }
  }

  static function validate_upload($field, $form, $element, &$new_values) {
  # validate max_files_number
    if (count($field->pool_old) +
        count($field->pool_new) +
        count($new_values) < $field->min_files_number) {
      $field->error_set(new text_multiline([
        'You are trying to upload too few files!',
        'You must upload at least %%_number file%%_plural{number,s}.',
        'You have already uploaded %%_current_number file%%_plural{number,s}.'], ['number' => $field->min_files_number, 'current_number' => count($field->pool_old) + count($field->pool_new)]
      ));
      return;
    }
  # validate max_files_number
    if (count($field->pool_old) +
        count($field->pool_new) +
        count($new_values) > $field->max_files_number) {
      $field->error_set(new text_multiline([
        'You are trying to upload too much files!',
        'Maximum allowed only %%_number file%%_plural{number,s}.',
        'You have already uploaded %%_current_number file%%_plural{number,s}.'], ['number' => $field->max_files_number, 'current_number' => count($field->pool_old) + count($field->pool_new)]
      ));
      return;
    }
  # validate each item
    $max_size = $field->file_size_max_get();
    foreach ($new_values as $c_new_value) {
      if (count($field->allowed_types) &&
         !isset($field->allowed_types[$c_new_value->type])) {
        $field->error_set(
          'Field "%%_title" does not support uploading this file type!', ['title' => translation::get($field->title)]
        );
        return;
      }
      switch ($c_new_value->error) {
        case UPLOAD_ERR_INI_SIZE  : $field->error_set('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::get($field->title), 'error' => translation::get('the size of uploaded file more than %%_size', ['size' => locale::format_human_bytes($max_size)])]); return;
        case UPLOAD_ERR_FORM_SIZE : $field->error_set('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::get($field->title), 'error' => translation::get('the size of uploaded file more than MAX_FILE_SIZE (MAX_FILE_SIZE is not supported)')]);             return;
        case UPLOAD_ERR_PARTIAL   : $field->error_set('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::get($field->title), 'error' => translation::get('the uploaded file was only partially uploaded')]);                                                  return;
        case UPLOAD_ERR_NO_TMP_DIR: $field->error_set('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::get($field->title), 'error' => translation::get('missing a temporary directory')]);                                                                  return;
        case UPLOAD_ERR_CANT_WRITE: $field->error_set('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::get($field->title), 'error' => translation::get('failed to write file to disk')]);                                                                   return;
        case UPLOAD_ERR_EXTENSION : $field->error_set('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::get($field->title), 'error' => translation::get('a php extension stopped the file upload')]);                                                        return;
      }
      if ($c_new_value->error !== UPLOAD_ERR_OK) {$field->error_set('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::get($field->title), 'error' => $c_new_value->error]); return;}
      if ($c_new_value->size === 0)              {$field->error_set('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::get($field->title), 'error' => translation::get('file is empty')]); return;}
      if ($c_new_value->size > $max_size)        {$field->error_set('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::get($field->title), 'error' => translation::get('the size of uploaded file more than %%_size', ['size' => locale::format_human_bytes($max_size)])]); return;}
    }
    return true;
  }

  static function validate_multiple($field, $form, $element, &$new_values) {
    if (!$field->multiple_get() && count($new_values) > 1) {
      $field->error_set(
        'Field "%%_title" does not support multiple select!', ['title' => translation::get($field->title)]
      );
    } else {
      return true;
    }
  }

}}