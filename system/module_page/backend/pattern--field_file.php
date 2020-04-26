<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_file extends field {

  # note:
  # ══════════════════════════════════════════════════════════════════════════════════════════
  # 1. if one file from new uploaded set of files has an error, all set will be rejected
  # 2. removing process does not depend from other even if a new uploaded set has an error
  # 3. the new files which marked as 'removed' will be removed in 'on_validate'
  # 4. the old files which marked as 'removed' will be removed in 'on_submit'
  # ──────────────────────────────────────────────────────────────────────────────────────────

  # SUBMIT PROCESS #1                             . SUBMIT PROCESS #2
  # ............................................................................................................................
  # on_init                                       .
  #                 ╔════════ form ════════╗      .      ╔═ pool_fin ═╗       ╔════════ form ════════╗
  #                 ║ ┌── upload field ──┐ ║      .  ┌──▶║ old file 1 ║       ║ ┌── upload field ──┐ ║
  #                 ║ │------------------│ ║      .  │   ╚════════════╝       ║ │------------------│ ║
  #             ┌─────│   + new file 1   │ ║      .  │          │        ┌──────│   + new file 2   │ ║
  #             │   ║ └──────────────────┘ ║      .  │          │        │    ║ └──────────────────┘ ║
  #             │   ╚══════════════════════╝      .  │          └────────│─────▶ ▣ delete old file 1 ────┐
  #             │                                 .  │                   │    ╚══════════════════════╝   │
  #             │                                 .  │          ┌────────┘                               │
  # ............│....................................│..........│........................................│......................
  # on_validate │                                 .  │          │                                        │
  #             ▼                                 .  │          ▼                                        ▼
  #      ╔═ pool_pre ═╗                           .  │   ╔═ pool_pre ═╗                      ╔═ pool_fin_to_delete ═╗
  #      ║ new file 1 ║                        ┌─────┘   ║ new file 2 ║                      ║       old file 1     ║
  #      ╚════════════╝                        │  .      ╚════════════╝                      ╚══════════════════════╝
  #             │                              │  .             │                                        │
  #             │   ╔════════ form ════════╗   │  .             │             ╔════════ form ════════╗   │
  #             │   ║ ┌── upload field ──┐ ║   │  .             │             ║ ┌── upload field ──┐ ║   │
  #             │   ║ │------------------│ ║   │  .             │             ║ │------------------│ ║   │
  #             │   ║ └──────────────────┘ ║   │  .             │             ║ └──────────────────┘ ║   │    ┌ ─ ─ ─ ─ ─ ─ ─ ─
  #             ├────◇ □ delete new file 1 ║   │  .             └──────────────▶ ▣ delete new file 2 ────│───▶  delete process │
  #             │   ╚══════════════════════╝   │  .                           ╚══════════════════════╝   │    └ ─ ─ ─ ─ ─ ─ ─ ─
  #             │                              │  .                                                      │
  # ............│..............................│.........................................................│......................
  # on_submit   │                              │  .                                                      │
  #             ▼                              │  .                                                      ▼
  #      ╔══ storage ══╗                       │  .                                             ┌ ─ ─ ─ ─ ─ ─ ─ ─
  #      ║    file 1   ║───────────────────────┘  .                                               delete process │
  #      ╚═════════════╝                          .                                             └ ─ ─ ─ ─ ─ ─ ─ ─

  public $title = 'File';
  public $attributes = ['data-type' => 'file'];
  public $element_attributes = [
    'type' => 'file',
    'name' => 'file'];
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
  public $has_validate_phase_3 = true;
# ─────────────────────────────────────────────────────────────────────
  public $pool_fin = [];
  public $pool_pre = [];
  public $pool_result = null;

  function value_get() {
    $values = $this->values_get();
    if (isset($values[0]))
       return $values[0];
  }

  function value_set($value) {
    $this->pool_values_init_fin($value ? [$value] : []);
  }

  function values_get() {
    if    ($this->pool_result == null) $this->pool_values_save();
    return $this->pool_result ?? [];
  }

  function values_set($values) {
    $this->pool_values_init_fin($values ?: []);
  }

  function file_size_max_get() {
    $bytes_1 = core::is_abbreviated_bytes($this->max_file_size) ?
               core::abbreviated_to_bytes($this->max_file_size) : $this->max_file_size;
    $bytes_2 = core::is_abbreviated_bytes(ini_get('upload_max_filesize')) ?
               core::abbreviated_to_bytes(ini_get('upload_max_filesize')) : ini_get('upload_max_filesize');
    return min($bytes_1, $bytes_2);
  }

  function render_description() {
                                                            $result[] = $this->render_description_file_size_max     ();
    if ($this->min_files_number != $this->max_files_number) $result[] = $this->render_description_file_min_number   ();
    if ($this->min_files_number != $this->max_files_number) $result[] = $this->render_description_file_max_number   ();
    if ($this->min_files_number == $this->max_files_number) $result[] = $this->render_description_file_mid_number   ();
    if ($this->allowed_types                              ) $result[] = $this->render_description_allowed_types     ();
    if ($this->allowed_characters_title                   ) $result[] = $this->render_description_allowed_characters();
    if ($this->description) $result[] = new markup('p', [], $this->description);
    if (count($result)) {
      if ($this->description_state == 'hidden'                      ) return '';
      if ($this->description_state == 'opened' || $this->has_error()) return                        (new markup($this->description_tag_name, ['id' => $this->id_get() ? 'description-'.$this->id_get() : null], $result))->render();
      if ($this->description_state == 'closed'                      ) return $this->render_opener().(new markup($this->description_tag_name, ['id' => $this->id_get() ? 'description-'.$this->id_get() : null], $result))->render();
      return '';
    }
  }

  function render_description_file_size_max     () {return new markup('p', ['data-id' => 'file-size-max'          ], new text('Maximum file size: %%_value',                                        ['value'      => locale::format_bytes($this->file_size_max_get())]));}
  function render_description_file_min_number   () {return new markup('p', ['data-id' => 'file-min-number'        ], new text('Field can contain a minimum of %%_number file%%_plural{number,s}.',  ['number'     =>               $this->min_files_number        ]));   }
  function render_description_file_max_number   () {return new markup('p', ['data-id' => 'file-max-number'        ], new text('Field can contain a maximum of %%_number file%%_plural{number,s}.',  ['number'     =>               $this->max_files_number        ]));   }
  function render_description_file_mid_number   () {return new markup('p', ['data-id' => 'file-mid-number'        ], new text('Field should contain %%_number file%%_plural{number,s}.',            ['number'     =>               $this->min_files_number        ]));   }
  function render_description_allowed_types     () {return new markup('p', ['data-id' => 'file-allowed-types'     ], new text('File can only be of the next types: %%_types',                       ['types'      => implode(', ', $this->allowed_types          )]));   }
  function render_description_allowed_characters() {return new markup('p', ['data-id' => 'file-allowed-characters'], new text('File name can contain only the next characters: %%_characters',      ['characters' =>               $this->allowed_characters_title]));   }

  ############
  ### pool ###
  ############

  function pool_values_init_fin($fin_items = []) {
    $this->pool_fin = [];
  # insert 'fin' items into the pool
    foreach ($fin_items as $c_id => $c_path_relative) {
      $c_item = new file_uploaded;
      if ($c_item->init_from_fin($c_path_relative)) {
        $this->pool_fin[$c_id] = $c_item;
      }
    }
  # add the next deleted items into the cache
    $deleted_from_cform = $this->pool_manager_get_deleted_items('fin');
    $deleted_from_cache = $this->pool_cache_get('fin_to_delete');
    foreach ($this->pool_fin as $c_id => $c_item) {
      if (isset($deleted_from_cform[$c_id]))
                $deleted_from_cache[$c_id] = $c_item;
    }
  # deferred deletion of 'fin' items which marked as 'deleted'
    foreach ($this->pool_fin as $c_id => $c_item) {
      if (isset($deleted_from_cache[$c_id])) {
        unset($this->pool_fin[$c_id]);
      }
    }
  # save the poll and update pool manager
    $this->pool_cache_set('fin_to_delete', $deleted_from_cache);
    $this->pool_manager_rebuild();
  }

  # ─────────────────────────────────────────────────────────────────────

  function pool_values_init_pre_from_cache() { #1
    $this->pool_pre = $this->pool_cache_get('pre');
  # immediate deletion of 'pre' items which marked as 'deleted'
    $deleted_from_cform = $this->pool_manager_get_deleted_items('pre');
    foreach ($this->pool_pre as $c_id => $c_item) {
      if (isset($deleted_from_cform[$c_id])) {
        $result = $c_item->delete_pre();
        if ($result) {
          unset($this->pool_pre[$c_id]);
        }
      }
    }
  # save the poll and update pool manager
    $this->pool_cache_set('pre', $this->pool_pre);
    $this->pool_manager_rebuild();
  }

  # ─────────────────────────────────────────────────────────────────────

  function pool_values_init_new_from_form($new_items = []) { #2
    foreach ($new_items as $c_item) {
      $this->pool_pre[] = $c_item;
      $c_item_id = core::array_key_last($this->pool_pre); # p.s. even after deleting the array element, the next key will be 'last used key +1'
      $result = $c_item->move_tmp_to_pre(temporary::directory.'validation/'.$this->cform->validation_cache_date_get().'/'.$this->cform->validation_id.'-'.$this->name_get().'-'.$c_item_id);
      if (!$result) {
        unset($this->pool_pre[$c_item_id]);
      }
    }
  # save the poll and update pool manager
    $this->pool_cache_set('pre', $this->pool_pre);
    $this->pool_manager_rebuild();
  }

  # ─────────────────────────────────────────────────────────────────────

  function pool_values_save() {
  # delete the 'fin' deleted items
    $deleted_from_cache = $this->pool_cache_get('fin_to_delete');
    foreach ($deleted_from_cache as $c_id => $c_item) {
      if (isset($deleted_from_cache[$c_id])) {
        if (!$c_item->delete_fin()) {
          return;
        }
      }
    }
  # move 'pre' items into the directory 'files'
    foreach ($this->pool_pre as $c_id => $c_item) {
      if (!$c_item->move_pre_to_fin(dynamic::dir_files.
             $this->upload_dir.$c_item->file,
             $this->fixed_name,
             $this->fixed_type)) {
        return;
      }
    }
  # prepare return
    $result_paths = [];
    foreach ($this->pool_fin as $c_item) $result_paths[] = (new file($c_item->fin_path))->path_get_relative();
    foreach ($this->pool_pre as $c_item) $result_paths[] = (new file($c_item->fin_path))->path_get_relative();
  # move pool_pre to pool_fin and return result
    $this->pool_pre =                                      [];
    $this->pool_manager_set_deleted_items('fin',           []);
    $this->pool_cache_set                ('fin_to_delete', []);
    $this->pool_values_init_fin($result_paths);
    $this->pool_result =        $result_paths;
    return true;
  }

  # ─────────────────────────────────────────────────────────────────────
  # pool cache
  # ─────────────────────────────────────────────────────────────────────

  protected function pool_cache_get($id) {
    return $this->cform->validation_cache_get(
      $this->name_get().'_files_pool_'.$id
    ) ?: [];
  }

  protected function pool_cache_set($id, $data) {
    $this->cform->validation_cache_set(
      $this->name_get().'_files_pool_'.$id, $data
    );
  }

  # ─────────────────────────────────────────────────────────────────────
  # pool manager
  # ─────────────────────────────────────────────────────────────────────

  protected function pool_manager_rebuild() {
    $this->child_delete('manager');
    $pool_manager = new group_checkboxes;
    $pool_manager->build();
    $this->child_insert($pool_manager, 'manager');
  # insert 'delete' checkboxes for the 'fin' and the 'pre' items
    foreach ($this->pool_fin as $c_id => $c_item) $this->pool_manager_insert_action($c_item, $c_id, 'fin');
    foreach ($this->pool_pre as $c_id => $c_item) $this->pool_manager_insert_action($c_item, $c_id, 'pre');
  }

  protected function pool_manager_insert_action($item, $id, $type) {
    $name = $this->name_get();
    $element_attributes['name'    ] = $name.'_delete_'.$type.'[]';
    $element_attributes['value'   ] = $id;
    $element_attributes['disabled'] = $this->disabled_get();
    $pool_manager = $this->child_select('manager');
    $pool_manager->field_insert(
      new text('delete file "%%_file"', ['file' => $item->file]), null, $element_attributes
    );
  }

  protected function pool_manager_get_deleted_items($type) {
    if ($this->disabled_get() == false) {
      $name = $this->name_get();
      return core::array_kmap(
        static::request_values_get($name.'_delete_'.$type)
      );
    }
  }

  protected function pool_manager_set_deleted_items($type, $items) {
    if ($this->disabled_get() == false) {
      $name = $this->name_get();
      static::request_values_set($name.'_delete_'.$type, $items);
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static function sanitize($field, $form, $element, &$new_values) {
    foreach ($new_values as $c_value) {
      $c_value->sanitize_tmp(
        $field->allowed_characters,
        $field->max_length_name,
        $field->max_length_type
      );
    }
  }

  static function on_validate($field, $form, $npath) {
    $element = $field->child_select('element');
    $name = $field->name_get();
    $type = $field->type_get();
    if ($name && $type) {
      if ($field->disabled_get()) return true;
      $field->pool_values_init_pre_from_cache();
      $new_values = static::request_files_get($name);
      static::sanitize($field, $form, $element, $new_values);
      $result = static::validate_multiple($field, $form, $element, $new_values) &&
                static::validate_upload  ($field, $form, $element, $new_values);
      if ($result) $field->pool_values_init_new_from_form($new_values);
      return $result;
    }
  }

  static function on_validate_and_return_value($field, $form, $npath) {
    $element = $field->child_select('element');
    $name = $field->name_get();
    $type = $field->type_get();
    if ($name && $type) {
      if ($field->disabled_get()) return [];
      $new_values = static::request_files_get($name);
      static::sanitize($field, $form, $element, $new_values);
      $result = static::validate_multiple($field, $form, $element, $new_values) &&
                static::validate_upload  ($field, $form, $element, $new_values);
      if ($result) return $new_values;
      else         return [];
    }
  }

  static function on_validate_phase_3($field, $form, $npath) {
  # try to copy the files and raise an error if it fails (e.g. directory permissions)
    if ($field->has_validate_phase_3 && $field->pool_result == null && !$form->has_error()) {
      if (!$field->pool_values_save()) {
        $field->error_set();
        return;
      }
    }
    return true;
  }

  static function validate_upload($field, $form, $element, &$new_values) {
    if ($field->min_files_number > count($field->pool_fin) + count($field->pool_pre) + count($new_values)) {$field->error_set(new text_multiline([                                            'Field should contain a minimum of %%_number file%%_plural{number,s}.', 'You have already uploaded %%_current_number file%%_plural{current_number,s}.'], ['number' => $field->min_files_number, 'current_number' => count($field->pool_fin) + count($field->pool_pre)] )); return;}
    if ($field->max_files_number < count($field->pool_fin) + count($field->pool_pre) + count($new_values)) {$field->error_set(new text_multiline(['You are trying to upload too much files!', 'Field should contain a maximum of %%_number file%%_plural{number,s}.', 'You have already uploaded %%_current_number file%%_plural{current_number,s}.'], ['number' => $field->max_files_number, 'current_number' => count($field->pool_fin) + count($field->pool_pre)] )); return;}
  # validate each item
    $max_size = $field->file_size_max_get();
    foreach ($new_values as $c_new_value) {
      if (count($field->allowed_types) &&
         !isset($field->allowed_types[$c_new_value->type])) {
        $field->error_set(
          'Field "%%_title" does not support uploading this file type!', ['title' => translation::apply($field->title)]
        );
        return;
      }
      switch ($c_new_value->error) {
        case UPLOAD_ERR_INI_SIZE  : $field->error_set('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::apply($field->title), 'error' => translation::apply('the size of uploaded file more than %%_size', ['size' => locale::format_bytes($max_size)])]); return;
        case UPLOAD_ERR_FORM_SIZE : $field->error_set('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::apply($field->title), 'error' => translation::apply('the size of uploaded file more than MAX_FILE_SIZE (MAX_FILE_SIZE is not supported)')]);       return;
        case UPLOAD_ERR_PARTIAL   : $field->error_set('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::apply($field->title), 'error' => translation::apply('the file was only partially uploaded')]);                                                     return;
        case UPLOAD_ERR_NO_TMP_DIR: $field->error_set('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::apply($field->title), 'error' => translation::apply('missing a temporary directory')]);                                                            return;
        case UPLOAD_ERR_CANT_WRITE: $field->error_set('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::apply($field->title), 'error' => translation::apply('failed to write file to disk')]);                                                             return;
        case UPLOAD_ERR_EXTENSION : $field->error_set('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::apply($field->title), 'error' => translation::apply('a PHP extension stopped the file upload')]);                                                  return;
      }
      if ($c_new_value->error !== UPLOAD_ERR_OK) {$field->error_set('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::apply($field->title), 'error' => $c_new_value->error                                                                                           ]); return;}
      if ($c_new_value->size === 0)              {$field->error_set('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::apply($field->title), 'error' => translation::apply('file is empty')                                                                           ]); return;}
      if ($c_new_value->size > $max_size)        {$field->error_set('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => translation::apply($field->title), 'error' => translation::apply('the size of uploaded file more than %%_size', ['size' => locale::format_bytes($max_size)])]); return;}
    }
    return true;
  }

  static function validate_multiple($field, $form, $element, &$new_values) {
    if (!$field->multiple_get() && count($new_values) > 1) {
      $field->error_set(
        'Field "%%_title" does not support multiple select!', ['title' => translation::apply($field->title)]
      );
    } else {
      return true;
    }
  }

}}