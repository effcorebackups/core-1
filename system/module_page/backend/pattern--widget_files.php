<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_files extends widget_items {

  public $title = 'Files';
  public $item_title = 'File';
  public $attributes = ['data-type' => 'items-files'];
  public $name_complex = 'widget_files';
# ─────────────────────────────────────────────────────────────────────
  public $upload_dir = '';
  public $fixed_name = 'file-multiple-%%_item_id_context';
  public $fixed_type;
  public $max_file_size = '5K';
  public $types_allowed = [
    'txt' => 'txt'
  ];

  function value_get_complex($is_relative = true) {
    $this->on_values_save();
    $items = $this->items_get();
    foreach ($items as $c_item) {
      if (empty($c_item->object->tmp_path) === true)           unset($c_item->object->tmp_path);
      if (empty($c_item->object->pre_path) === true)           unset($c_item->object->pre_path);
      if (empty($c_item->object->fin_path) !== true && $is_relative) $c_item->object->fin_path = (new file($c_item->object->fin_path))->path_get_relative();
    }
    return $items;
  }

  function value_set_complex($value, $once = false, $is_absolute = true) {
    if (is_array($value))
      foreach ($value as $c_item)
        if (empty($c_item->object->fin_path) !== true && $is_absolute)
          $c_item->object->fin_path = (new file($c_item->object->fin_path))->path_get_absolute();
    $this->items_set($value, $once);
  }

  # ─────────────────────────────────────────────────────────────────────

  function widget_manage_get($item, $c_row_id) {
    $widget = parent::widget_manage_get($item, $c_row_id);
  # info markup
    $file = new file($item->object->get_current_path());
    $id_markup = $item->object->get_current_state() === 'pre' ?
      new text_multiline(['new item', '…'], [], '') :
      new text($file->file_get());
    $info_markup = new markup('x-info',  [], [
        'title' => new markup('x-title', [], $item->object->file),
        'id'    => new markup('x-id',    [], $id_markup )]);
  # grouping of previous elements in widget 'manage'
    $widget->child_insert($info_markup, 'info');
    return $widget;
  }

  function widget_insert_get() {
    $widget = new markup('x-widget', [
      'data-type' => 'insert']);
  # control for upload new file
    $field_file = new field_file;
    $field_file->title = 'File';
    $field_file->max_file_size    = $this->max_file_size;
    $field_file->types_allowed    = $this->types_allowed;
    $field_file->cform            = $this->cform;
    $field_file->min_files_number = null;
    $field_file->max_files_number = null;
    $field_file->has_on_validate  = false;
    $field_file->build();
    $field_file->multiple_set();
    $field_file->name_set($this->name_get_complex().'__file[]');
  # button for insertion of the new item
    $button = new button(null, ['data-style' => 'narrow-insert', 'title' => new text('insert')]);
    $button->break_on_validate = true;
    $button->build();
    $button->value_set($this->name_get_complex().'__insert');
    $button->_type = 'insert';
  # relate new controls with the widget
    $this->controls['#file'  ] = $field_file;
    $this->controls['~insert'] = $button;
    $widget->child_insert($field_file, 'file');
    $widget->child_insert($button, 'button');
    return $widget;
  }

  # ─────────────────────────────────────────────────────────────────────

  function on_values_save() {
    $items = $this->items_get();
    foreach ($items as $c_row_id => $c_item) {
      switch ($c_item->object->get_current_state()) {
        case 'pre': # moving of 'pre' items into the directory 'files'
          token::insert('item_id_context', 'text', $c_row_id);
          $c_result = $c_item->object->move_pre_to_fin(dynamic::dir_files.
            $this->upload_dir.$c_item->object->file,
            $this->fixed_name,
            $this->fixed_type, true);
          if ($c_result) {
            message::insert(new text(
              'Item of type "%%_type" with ID = "%%_id" has been saved.', [
              'type' => (new text($this->item_title))->render(),
              'id'   => $c_item->object->file
            ]));
          }
          break;
        case 'fin': # deletion of 'fin' items which marked as 'deleted'
          if (!empty($c_item->is_deleted)) {
            $c_id_for_message = $c_item->object->file;
            $c_result = $c_item->object->delete_fin();
            unset($items[$c_row_id]);
            if ($c_result) {
              message::insert(new text_multiline([
                'Item of type "%%_type" with ID = "%%_id" was deleted physically.'], [
                'type' => (new text($this->item_title))->render(),
                'id'   => $c_id_for_message
              ]));
            }
          }
          break;
        case null: # cache cleaning for lost files
          unset($items[$c_row_id]);
          break;
      }
    }
    $this->items_set($items);
    $this->is_builded = false;
    $this->build();
  }

  function on_values_validate($form, $npath, $button) {
    return field_file::on_manual_validate_and_return_value($this->controls['#file'], $form, $npath);
  }

  function on_button_click_insert($form, $npath, $button) {
    $values = $this->on_values_validate($form, $npath, $button);
    if (!$this->controls['#file']->has_error() && count($values) === 0) {$this->controls['#file']->error_set('Field "%%_title" cannot be blank!', ['title' => (new text($this->controls['#file']->title))->render() ]); return;}
    if (!$this->controls['#file']->has_error() && count($values) !== 0) {
      $items = $this->items_get();
      foreach ($values as $c_value) {
        $min_weight = 0;
        foreach ($items as $c_row_id => $c_item)
          $min_weight = min($min_weight, $c_item->weight);
        $c_new_item = new \stdClass;
        $c_new_item->is_deleted = false;
        $c_new_item->weight = count($items) ? $min_weight - 5 : 0;
        $c_new_item->object = $c_value;
        $items[] = $c_new_item;
        $c_new_row_id = core::array_key_last($items);
        $c_pre_path = temporary::directory.'validation/'.$form->validation_cache_date_get().'/'.$form->validation_id.'-'.$this->name_get_complex().'-'.$c_new_row_id.'.'.$c_value->type;
        if ($c_value->move_tmp_to_pre($c_pre_path)) {
          $this->items_set($items);
          message::insert(new text(
            'Item of type "%%_type" with ID = "%%_id" was inserted.', [
            'type' => (new text($this->item_title))->render(),
            'id'   => $c_new_item->object->file]));
        } else {
          $form->error_set();
          return;
        }
      }
      message::insert('Do not forget to save the changes!');
      return true;
    }
  }

  function on_button_click_delete($form, $npath, $button) {
    $items = $this->items_get();
    $id_for_message = $items[$button->_id]->object->file;
    switch ($items[$button->_id]->object->get_current_state()) {
      case 'pre':
        if ($items[$button->_id]->object->delete_pre()) {
          unset($items[$button->_id]);
          $this->items_set($items);
          message::insert(new text_multiline([
            'Item of type "%%_type" with ID = "%%_id" was deleted physically.',
            'Do not forget to save the changes!'], [
            'type' => (new text($this->item_title))->render(),
            'id'   => $id_for_message ]));
          return true;
        } return;
      case 'fin':
        $items[$button->_id]->is_deleted = true;
        $this->items_set($items);
        message::insert(new text_multiline([
          'Item of type "%%_type" with ID = "%%_id" was deleted.',
          'Do not forget to save the changes!'], [
          'type' => (new text($this->item_title))->render(),
          'id'   => $id_for_message ]));
        return true;
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static function complex_value_to_markup($complex) {
    $decorator = new decorator('ul');
    $decorator->id = 'widget_files-items';
    if ($complex) {
      core::array_sort_by_weight($complex);
      foreach ($complex as $c_row_id => $c_item) {
        $decorator->data[$c_row_id] = [
          'path' => ['title' => 'Path', 'value' => $c_item->object->get_current_path(true)],
          'type' => ['title' => 'Type', 'value' => $c_item->object->mime],
          'size' => ['title' => 'Size', 'value' => $c_item->object->size]
        ];
      }
    }
    return $decorator;
  }

}}