<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_files extends widget_items {

  public $title = 'Files';
  public $item_title = 'File';
  public $attributes = ['data-type' => 'items-info-files'];
  public $name_complex = 'widget_files';
  public $upload_dir = '';
  public $fixed_name = 'file-%%_instance_id_context-%%_item_id_context';

  # ─────────────────────────────────────────────────────────────────────

  function value_get_complex() {
    $this->pool_values_save();
    return $this->items_get();
  }

  function pool_values_save() {
    $items = $this->items_get();
    foreach ($items as $c_id => $c_item) {
      if (!empty($c_item->object->pre_path)) {
        token::insert('item_id_context', '%%_item_id_context', 'text', $c_id);
        $c_item->object->move_pre_to_fin(dynamic::dir_files.
          $this->upload_dir.$c_item->object->file,
          $this->fixed_name);
      }
    }
  }

  # ─────────────────────────────────────────────────────────────────────

  function widget_manage_get($item, $c_row_id) {
    $widget = parent::widget_manage_get($item, $c_row_id);
  # info markup
    $info_markup = new markup('x-info',  [], [
        'title' => new markup('x-title', [], (new text_multiline([$item->object->file, $item->object->get_current_state()], [], ' | ')) ),
        'id'    => new markup('x-id',    [], (new file($item->object->get_current_path()))->name_get() )]);
  # grouping of previous elements in widget 'manage'
    $widget->child_insert($info_markup, 'info');
    return $widget;
  }

  function widget_insert_get() {
    $widget = new markup('x-widget', [
      'data-type' => 'insert']);
  # control for upload new file
    $field_file = new field_picture;
    $field_file->title = 'File';
    $field_file->build();
    $field_file->name_set($this->name_complex.'__file');
    $field_file->has_validate_phase_3 = false;
    $field_file->cform = $this->cform;
    $this->controls['#file'] = $field_file;
  # button for insertion of the new item
    $button = new button(null, ['data-style' => 'narrow-insert', 'title' => new text('insert')]);
    $button->break_on_validate = true;
    $button->build();
    $button->value_set($this->name_complex.'__insert');
    $button->_type = 'insert';
    $this->controls['~insert'] = $button;
  # grouping of previous elements in widget 'insert'
    $widget->child_insert($field_file, 'file');
    $widget->child_insert($button,     'button');
    return $widget;
  }

  # ─────────────────────────────────────────────────────────────────────

  function on_button_click_insert($form, $npath, $button) {
    $values = field_file::on_validate_and_return_value($this->controls['#file'], $form, $npath);
    if (count($values)) {
      $items = $this->items_get();
      $value = reset($values);
      $min_weight = 0;
      foreach ($items as $c_row_id => $c_item)
        $min_weight = min($min_weight, $c_item->weight);
      $new_item = new \stdClass;
      $new_item->weight = count($items) ? $min_weight - 5 : 0;
      $new_item->object = $value;
      $items[] = $new_item;
      $new_item_id = core::array_key_last($items);
      if ($value->move_tmp_to_pre(temporary::directory.'validation/'.$form->validation_cache_date_get().'/'.$form->validation_id.'-'.$this->name_complex.'-'.$new_item_id)) {
        $this->items_set($items);
        message::insert(new text_multiline([
          'Item of type "%%_type" was inserted.',
          'Do not forget to save the changes!'], [
          'type' => translation::apply($this->item_title)]));
        return true;
      } else {
        $form->error_set();
      }
    } elseif (!$this->controls['#file']->has_error()) {
      $this->controls['#file']->error_set(
        'Field "%%_title" can not be blank!', ['title' => translation::apply($this->controls['#file']->title)]
      );
    }
  }

}}