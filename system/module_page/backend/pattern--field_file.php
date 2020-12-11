<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
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

  # SUBMIT PROCESS #1                                       . SUBMIT PROCESS #2
  # ......................................................................................................................................
  # on_init                                                 .
  #                           ╔════════ form ════════╗      .      ╔═ pool fin ═╗       ╔════════ form ════════╗
  #                           ║ ┌── upload field ──┐ ║      .  ┌──▶║ old file 1 ║       ║ ┌── upload field ──┐ ║
  #                           ║ │------------------│ ║      .  │   ╚════════════╝       ║ │------------------│ ║
  #                       ┌─────│   + new file 1   │ ║      .  │          │        ┌──────│   + new file 2   │ ║
  #                       │   ║ └──────────────────┘ ║      .  │          │        │    ║ └──────────────────┘ ║
  #                       │   ╚══════════════════════╝      .  │          └────────│─────▶ ▣ delete old file 1 ────┐
  #                       │                                 .  │                   │    ╚══════════════════════╝   │
  #                       │                                 .  │          ┌────────┘                               │
  # ......................│....................................│..........│........................................│......................
  # on_validate           │                                 .  │          │                                        │
  #                       ▼                                 .  │          ▼                                        ▼
  #                ╔═ pool pre ═╗                           .  │   ╔═ pool pre ═╗                      ╔═ pool fin_to_delete ═╗
  #                ║ new file 1 ║                        ┌─────┘   ║ new file 2 ║                      ║       old file 1     ║
  #                ╚════════════╝                        │  .      ╚════════════╝                      ╚══════════════════════╝
  #                       │                              │  .             │                                        │
  #                       │   ╔════════ form ════════╗   │  .             │             ╔════════ form ════════╗   │
  #                       │   ║ ┌── upload field ──┐ ║   │  .             │             ║ ┌── upload field ──┐ ║   │
  #                       │   ║ │------------------│ ║   │  .             │             ║ │------------------│ ║   │
  #                       │   ║ └──────────────────┘ ║   │  .             │             ║ └──────────────────┘ ║   │    ┌ ─ ─ ─ ─ ─ ─ ─ ─
  #                       ├────◇ □ delete new file 1 ║   │  .             └──────────────▶ ▣ delete new file 2 ────│───▶  delete process │
  #                       │   ╚══════════════════════╝   │  .                           ╚══════════════════════╝   │    └ ─ ─ ─ ─ ─ ─ ─ ─
  #                       │                              │  .                                                      │
  # ......................│..............................│.........................................................│......................
  # on_validate_phase_3   │                              │  .                                                      │
  #                       ▼                              │  .                                                      ▼
  #                ╔══ storage ══╗                       │  .                                             ┌ ─ ─ ─ ─ ─ ─ ─ ─
  #                ║    file 1   ║───────────────────────┘  .                                               delete process │
  #                ╚═════════════╝                          .                                             └ ─ ─ ─ ─ ─ ─ ─ ─

  public $title = 'File';
  public $attributes = ['data-type' => 'file'];
  public $element_attributes = [
    'type' => 'file',
    'name' => 'file'];
# ─────────────────────────────────────────────────────────────────────
  public $upload_dir = '';
  public $fixed_name;
  public $fixed_type;
  public $max_file_size = '5K';
  public $min_files_number = 0;
  public $max_files_number = 1;
  public $max_length_name = 227; # = 255 - strlen('-TimestmpRandom_v.') - max_length_type
  public $max_length_type =  10;
  public $allowed_characters = 'a-zA-Z0-9_\\-\\.';
  public $allowed_characters_title = '"a-z", "A-Z", "0-9", "_", "-", "."';
  public $allowed_types = ['txt' => 'txt'];
  public $has_on_validate = true;
# ─────────────────────────────────────────────────────────────────────
  public $result;
  public $pool_fin = [];
  public $pool_pre = [];

  function build() {
    parent::build();
    if ($this->child_select('element')) {
      $accept_types = [];
      foreach ($this->allowed_types as $c_type)
                 $accept_types[] = '.'.$c_type;
      $this->child_select('element')->attribute_insert('accept', implode(',', $accept_types));
    }
  }

  function value_get() {
    $values = $this->values_get();
    if (isset($values[0]))
       return $values[0];
  }

  function value_set($value) {
    $this->on_values_fin_update($value ? [$value] : []);
    $this->pool_manager_rebuild();
  }

  function values_get() {
    return $this->result ?? [];
  }

  function values_set($values) {
    $this->on_values_fin_update($values ?: []);
    $this->pool_manager_rebuild();
  }

  function file_size_max_get() {
    $system = core::is_abbreviated_bytes($this->max_file_size) ?
              core::abbreviated_to_bytes($this->max_file_size) : (int)$this->max_file_size;
    $php__1 = core::upload_max_filesize_bytes_get();
    $php__2 = core::      post_max_size_bytes_get();
    return min($system, $php__1, $php__2);
  }

  function file_size_max_has_php_restriction() {
    $system = core::is_abbreviated_bytes($this->max_file_size) ?
              core::abbreviated_to_bytes($this->max_file_size) : (int)$this->max_file_size;
    $php__1 = core::upload_max_filesize_bytes_get();
    $php__2 = core::      post_max_size_bytes_get();
    return $system > $php__1 ||
           $system > $php__2;
  }

  function render_description() {
    $this->render_prepare_description();
    if ($this->min_files_number !== null && $this->min_files_number !== $this->max_files_number) $this->description[] = $this->render_description_file_min_number();
    if ($this->max_files_number !== null && $this->min_files_number !== $this->max_files_number) $this->description[] = $this->render_description_file_max_number();
    if ($this->min_files_number !== null && $this->min_files_number === $this->max_files_number) $this->description[] = $this->render_description_file_mid_number();
                                         $this->description[] = $this->render_description_file_size_max();
    if ($this->allowed_types           ) $this->description[] = $this->render_description_file_allowed_types();
    if ($this->allowed_characters_title) $this->description[] = $this->render_description_file_name_allowed_characters();
    return parent::render_description();
  }

  function render_description_file_size_max               () {return new markup('p', ['data-id' => 'file-size-max'          ], new text($this->file_size_max_has_php_restriction() ? 'File can have a maximum size: %%_value (PHP restriction)' : 'File can have a maximum size: %%_value', ['value' => locale::format_bytes($this->file_size_max_get())]));}
  function render_description_file_min_number             () {return new markup('p', ['data-id' => 'file-min-number'        ], new text('Field can contain a minimum of %%_number file%%_plural{number,s}.',  ['number'     =>               $this->min_files_number        ]));}
  function render_description_file_max_number             () {return new markup('p', ['data-id' => 'file-max-number'        ], new text('Field can contain a maximum of %%_number file%%_plural{number,s}.',  ['number'     =>               $this->max_files_number        ]));}
  function render_description_file_mid_number             () {return new markup('p', ['data-id' => 'file-mid-number'        ], new text('Field can contain only %%_number file%%_plural{number,s}.',          ['number'     =>               $this->min_files_number        ]));}
  function render_description_file_allowed_types          () {return new markup('p', ['data-id' => 'file-allowed-types'     ], new text('File can only be of the next types: %%_types',                       ['types'      => implode(', ', $this->allowed_types          )]));}
  function render_description_file_name_allowed_characters() {return new markup('p', ['data-id' => 'file-allowed-characters'], new text('File name can contain only the next characters: %%_characters',      ['characters' =>               $this->allowed_characters_title]));}

  ############
  ### pool ###
  ############

  function on_pool_values_save() {

//  # deletion of 'fin' items which marked as 'deleted'
//    $deleted_from_cache = $this->items_get('fin_to_delete');
//    foreach ($deleted_from_cache as $c_id => $c_item) {
//      if (!$c_item->delete_fin()) {
//        return;
//      }
//    }

    $this->on_values_pre_move_to_fin();
  # prepare return
    $this->result = [];
    foreach ($this->items_get('fin') as $c_item) {
      $this->result[] = (new file($c_item->get_current_path()))->path_get_relative();
    }
  # update controls
    $this->pool_manager_rebuild();

//  # moving of 'pool_pre' values to the 'pool_fin' and return result
//    $this->pool_manager_set_deleted_items('fin',           []);
//    $this->items_set                     ('fin_to_delete', []);
    return true;
  }

  # ─────────────────────────────────────────────────────────────────────

  function on_values_pre_move_to_fin() {
    $items_pre = $this->items_get('pre');
    $items_fin = $this->items_get('fin');
    foreach ($items_pre as $c_id => $c_item) {
      if ($c_item->move_pre_to_fin(dynamic::dir_files.$this->upload_dir.$c_item->file, $this->fixed_name, $this->fixed_type)) {
              $items_fin[] = $c_item;
        unset($items_pre[$c_id]);
        $this->items_set('pre', $items_pre);
        $this->items_set('fin', $items_fin);
        message::insert(new text(
          'Item of type "%%_type" with ID = "%%_id" has been saved.', [
          'type' => (new text('Picture'))->render(),
          'id'   => $c_id]));
      } else {
        $this->error_set();
        return;
      }
    }
  }

  function on_values_pre_insert($values = []) {
    $items_pre = $this->items_get('pre');
    foreach ($values as $c_new_item) {
      $items_pre[] = $c_new_item;
      $c_new_item_id = core::array_key_last($items_pre);
      if ($c_new_item->move_tmp_to_pre(temporary::directory.'validation/'.$this->cform->validation_cache_date_get().'/'.$this->cform->validation_id.'-'.$this->name_get().'-'.$c_new_item_id.'.'.$c_new_item->type)) {
        $this->items_set('pre', $items_pre);
        message::insert(new text(
          'Item of type "%%_type" with ID = "%%_id" was inserted.', [
          'type' => (new text('Picture'))->render(),
          'id'   => $c_new_item_id]));
      } else {
        $this->error_set();
        return;
      }
    }
  }

  function on_values_pre_delete() {
    $deleted_ids = $this->pool_manager_get_deleted_items('pre');
    $items_pre = $this->items_get('pre');
    foreach ($items_pre as $c_id => $c_item) {
      if (isset($deleted_ids[$c_id])) {
        if ($c_item->delete_pre()) {
          unset($items_pre[$c_id]);
          $this->items_set('pre', $items_pre);
          message::insert(new text(
            'Item of type "%%_type" with ID = "%%_id" was deleted.', [
            'type' => (new text('Picture'))->render(),
            'id'   => $c_id]));
        } else {
          $this->error_set();
          return;
        }
      }
    }
  }

  # ─────────────────────────────────────────────────────────────────────

  function on_values_fin_update($values = []) {
    $fin_items = [];
    $deleted_cache = $this->items_get('fin_to_delete');
    foreach ($values as $c_id => $c_path_relative) {
      if (!isset($deleted_cache[$c_id])) {
        $c_item = new file_uploaded;
        if ($c_item->init_from_fin($c_path_relative)) {
          $fin_items[$c_id] = $c_item;
          $this->items_set('fin', $fin_items);
        }
      }
    }
  }

  function on_values_fin_delete() {
    $deleted_ids_cform = $this->pool_manager_get_deleted_items('fin');
    $deleted_cache = $this->items_get('fin_to_delete');
    $items_fin = $this->items_get('fin');
    foreach ($items_fin as $c_id => $c_item) {
      if (isset($deleted_ids_cform[$c_id])) {
        $deleted_cache[$c_id] = $c_item;
        unset($items_fin[$c_id]);
        $this->items_set('fin_to_delete', $deleted_cache);
        $this->items_set('fin', $items_fin);
      }
    }
  }

  # ─────────────────────────────────────────────────────────────────────
  # pool cache
  # ─────────────────────────────────────────────────────────────────────

  protected function items_get($id) {
    return $this->cform->validation_cache_get(
      $this->name_get().'_files_pool_'.$id
    ) ?: [];
  }

  protected function items_set($id, $data) {
    $this->cform->validation_cache_set(
      $this->name_get().'_files_pool_'.$id, $data
    );
  }

  # ─────────────────────────────────────────────────────────────────────
  # pool manager
  # ─────────────────────────────────────────────────────────────────────

  protected function pool_manager_rebuild() {
    $name = $this->name_get();
    $this->child_delete('manager_fin');
    $this->child_delete('manager_pre');
    $pool_manager_fin = new group_checkboxes;
    $pool_manager_pre = new group_checkboxes;
    $pool_manager_fin->attributes['data-scope'] = 'fin';
    $pool_manager_pre->attributes['data-scope'] = 'pre';
    $pool_manager_fin->element_attributes['name'] = $name.'_delete_fin[]';
    $pool_manager_pre->element_attributes['name'] = $name.'_delete_pre[]';
    $pool_manager_fin->build();
    $pool_manager_pre->build();
    $this->child_insert($pool_manager_fin, 'manager_fin');
    $this->child_insert($pool_manager_pre, 'manager_pre');
  # insert 'delete' checkboxes for the 'fin' and the 'pre' items
    foreach ($this->items_get('fin') as $c_id => $c_item) $this->pool_manager_action_insert($c_item, $c_id, 'fin');
    foreach ($this->items_get('pre') as $c_id => $c_item) $this->pool_manager_action_insert($c_item, $c_id, 'pre');
  }

  protected function pool_manager_action_insert($item, $id, $type) {
    $name = $this->name_get();
    $pool_manager_fin = $this->child_select('manager_fin');
    $pool_manager_pre = $this->child_select('manager_pre');
    if ($this->disabled_get() && $type === 'fin') $pool_manager_fin->disabled[$id] = $id;
    if ($this->disabled_get() && $type === 'pre') $pool_manager_pre->disabled[$id] = $id;
    if ($type === 'fin') $pool_manager_fin->field_insert($this->pool_manager_action_insert_get_field_text($item, $id, $type), null, $id);
    if ($type === 'pre') $pool_manager_pre->field_insert($this->pool_manager_action_insert_get_field_text($item, $id, $type), null, $id);
  }

  protected function pool_manager_action_insert_get_field_text($item, $id, $type) {
    return new text('delete file "%%_file"', ['file' => $item->file]);
  }

  protected function pool_manager_get_deleted_items($type) {
    if ($this->disabled_get() === false) {
      $name = $this->name_get();
      if ($type === 'fin') return core::array_kmap(static::request_values_get($name.'_delete_fin'));
      if ($type === 'pre') return core::array_kmap(static::request_values_get($name.'_delete_pre'));
    }
  }

  protected function pool_manager_set_deleted_items($type, $items) {
    if ($this->disabled_get() === false) {
      $name = $this->name_get();
      if ($type === 'fin') static::request_values_set($name.'_delete_fin', $items);
      if ($type === 'pre') static::request_values_set($name.'_delete_pre', $items);
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

  static function on_manual_validate_and_return_value($field, $form, $npath) {
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

  static function on_validate($field, $form, $npath) {
    if ($field->has_on_validate) {
      $element = $field->child_select('element');
      $name = $field->name_get();
      $type = $field->type_get();
      if ($name && $type) {
        if ($field->disabled_get()) return true;
        $new_values = static::request_files_get($name);
        static::sanitize($field, $form, $element, $new_values);
        $result = static::validate_multiple($field, $form, $element, $new_values) &&
                  static::validate_upload  ($field, $form, $element, $new_values);
        if ($result)
          $field->on_values_pre_insert($new_values);
          $field->on_values_pre_delete();
          $field->on_values_fin_delete();
          $field->pool_manager_rebuild();
        return $result;
      }
    }
  }

  static function on_validate_phase_3($field, $form, $npath) {
  # try to copy the files and raise an error if it fails (e.g. directory permissions)
    if ($field->has_on_validate && !$form->has_error() && $field->result === null) {
      if (!$field->on_pool_values_save()) {
        $field->error_set();
        return;
      }
    }
    return true;
  }

  static function validate_upload($field, $form, $element, &$new_values) {
    if ($field->min_files_number !== null && $field->min_files_number !== $field->max_files_number && $field->min_files_number > count($field->pool_fin) + count($field->pool_pre) + count($new_values)) {$field->error_set(new text_multiline(['Field "%%_title" contains an error!', 'You are trying to upload too few files!',  'Field can contain a minimum of %%_number file%%_plural{number,s}.', 'You have already uploaded %%_current_number file%%_plural{current_number,s}.'], ['title' => (new text($field->title))->render(), 'number' => $field->min_files_number, 'current_number' => count($field->pool_fin) + count($field->pool_pre)] )); return;}
    if ($field->max_files_number !== null && $field->min_files_number !== $field->max_files_number && $field->max_files_number < count($field->pool_fin) + count($field->pool_pre) + count($new_values)) {$field->error_set(new text_multiline(['Field "%%_title" contains an error!', 'You are trying to upload too much files!', 'Field can contain a maximum of %%_number file%%_plural{number,s}.', 'You have already uploaded %%_current_number file%%_plural{current_number,s}.'], ['title' => (new text($field->title))->render(), 'number' => $field->max_files_number, 'current_number' => count($field->pool_fin) + count($field->pool_pre)] )); return;}
    if ($field->min_files_number !== null && $field->min_files_number === $field->max_files_number && $field->min_files_number > count($field->pool_fin) + count($field->pool_pre) + count($new_values)) {$field->error_set(new text_multiline(['Field "%%_title" contains an error!', 'You are trying to upload too few files!',  'Field can contain only %%_number file%%_plural{number,s}.',         'You have already uploaded %%_current_number file%%_plural{current_number,s}.'], ['title' => (new text($field->title))->render(), 'number' => $field->min_files_number, 'current_number' => count($field->pool_fin) + count($field->pool_pre)] )); return;}
    if ($field->max_files_number !== null && $field->min_files_number === $field->max_files_number && $field->max_files_number < count($field->pool_fin) + count($field->pool_pre) + count($new_values)) {$field->error_set(new text_multiline(['Field "%%_title" contains an error!', 'You are trying to upload too much files!', 'Field can contain only %%_number file%%_plural{number,s}.',         'You have already uploaded %%_current_number file%%_plural{current_number,s}.'], ['title' => (new text($field->title))->render(), 'number' => $field->max_files_number, 'current_number' => count($field->pool_fin) + count($field->pool_pre)] )); return;}
  # validate each item
    $max_size = $field->file_size_max_get();
    foreach ($new_values as $c_new_value) {
      if (count($field->allowed_types) &&
         !isset($field->allowed_types[$c_new_value->type])) {
        $field->error_set(
          'Field "%%_title" does not support uploading a file of this type!', ['title' => (new text($field->title))->render() ]
        );
        return;
      }
      switch ($c_new_value->error) {
        case UPLOAD_ERR_INI_SIZE  : $field->error_set(new text_multiline(['Field "%%_title" contains an error!', 'Size of uploaded file greater than %%_size.'                                       ], ['title' => (new text($field->title))->render(), 'size' => locale::format_bytes($max_size) ])); return;
        case UPLOAD_ERR_FORM_SIZE : $field->error_set(new text_multiline(['Field "%%_title" contains an error!', 'Size of uploaded file greater than MAX_FILE_SIZE (MAX_FILE_SIZE is not supported).'], ['title' => (new text($field->title))->render()                                            ])); return;
        case UPLOAD_ERR_PARTIAL   : $field->error_set(new text_multiline(['Field "%%_title" contains an error!', 'File was only partially uploaded.'                                                 ], ['title' => (new text($field->title))->render()                                            ])); return;
        case UPLOAD_ERR_NO_TMP_DIR: $field->error_set(new text_multiline(['Field "%%_title" contains an error!', 'Missing temporary directory.'                                                      ], ['title' => (new text($field->title))->render()                                            ])); return;
        case UPLOAD_ERR_CANT_WRITE: $field->error_set(new text_multiline(['Field "%%_title" contains an error!', 'Failed to write file to disk.'                                                     ], ['title' => (new text($field->title))->render()                                            ])); return;
        case UPLOAD_ERR_EXTENSION : $field->error_set(new text_multiline(['Field "%%_title" contains an error!', 'PHP extension stopped uploading file.'                                             ], ['title' => (new text($field->title))->render()                                            ])); return;
      }
      if ($c_new_value->error !== UPLOAD_ERR_OK) {$field->error_set(new text_multiline(['Field "%%_title" contains an error!', 'Error: %%_error'                            ], ['title' => (new text($field->title))->render(),      'error' => $c_new_value->error       ])); return;}
      if ($c_new_value->size  === 0            ) {$field->error_set(new text_multiline(['Field "%%_title" contains an error!', 'File is empty.'                             ], ['title' => (new text($field->title))->render()                                            ])); return;}
      if ($c_new_value->size   >  $max_size    ) {$field->error_set(new text_multiline(['Field "%%_title" contains an error!', 'Size of uploaded file greater than %%_size.'], ['title' => (new text($field->title))->render(), 'size' => locale::format_bytes($max_size) ])); return;}
    }
    return true;
  }

  static function validate_multiple($field, $form, $element, &$new_values) {
    if (!$field->multiple_get() && count($new_values) > 1) {
      $field->error_set(
        'Field "%%_title" does not support multiple select!', ['title' => (new text($field->title))->render() ]
      );
    } else {
      return true;
    }
  }

}}