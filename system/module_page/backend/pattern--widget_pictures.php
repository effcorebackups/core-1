<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_pictures extends widget_fields {

  public $title = 'Pictures';
  public $item_title = 'Picture';
  public $attributes = ['data-type' => 'fields-info-pictures'];
  public $name_complex = 'widget_pictures';

  # ─────────────────────────────────────────────────────────────────────

  function widget_manage_get($item, $c_row_id) {
    $widget = parent::widget_manage_get($item, $c_row_id);
  # info markup
    $info_markup = new markup('x-info',  [], [
        'title' => new markup('x-title', [],           $item->object->file),
        'id'    => new markup('x-id',    [], (new file($item->object->pre_path))->name_get() ) ]);
  # grouping of previous elements in widget 'manage'
    $widget->child_insert($info_markup, 'info');
    return $widget;
  }

  function widget_insert_get() {
    $widget = new markup('x-widget', [
      'data-type' => 'insert']);
  # control for upload new picture
    $field_picture = new field_picture;
    $field_picture->build();
    $field_picture->name_set($this->name_complex.'__picture');
    $field_picture->cform = $this->cform;
    $this->controls['#picture'] = $field_picture;
  # button for insertion of the new item
    $button = new button(null, ['data-style' => 'narrow-insert', 'title' => new text('insert')]);
    $button->break_on_validate = true;
    $button->build();
    $button->value_set($this->name_complex.'__insert');
    $button->_type = 'insert';
    $this->controls['~insert'] = $button;
  # grouping of previous elements in widget 'insert'
    $widget->child_insert($field_picture, 'picture');
    $widget->child_insert($button,        'button');
    return $widget;
  }

  # ─────────────────────────────────────────────────────────────────────

  function on_button_click_insert($form, $npath, $button) {
    $values = field_file::on_validate_and_return_value($this->controls['#picture'], $form, $npath);
    if (count($values)) {
      $items = $this->items_get();
      $item_new_id = count($items) ? core::array_key_last($items) + 1 : 0;
      $value = reset($values);
      if ($value->move_tmp_to_pre(temporary::directory.'validation/'.$form->validation_cache_date_get().'/'.$form->validation_id.'-'.$this->name_complex.'-'.$item_new_id)) {
        $min_weight = 0;
        foreach ($items as $c_row_id => $c_item)
          $min_weight = min($min_weight, $c_item->weight);
        $new_item = new \stdClass;
        $new_item->weight = count($items) ? $min_weight - 5 : 0;
        $new_item->object = $value;
        $items[] = $new_item;
        $this->items_set($items);
        message::insert(new text_multiline([
          'Item of type "%%_type" was inserted.',
          'Do not forget to save the changes!'], [
          'type' => translation::get($this->item_title)]));
        return true;
      }
    } elseif (!$this->controls['#picture']->has_error()) {
      $this->controls['#picture']->error_set(
        'Field "%%_title" can not be blank!', ['title' => translation::get($this->controls['#picture']->title)]
      );
    }
  }

}}