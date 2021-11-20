<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_attributes extends widget_items {

  public $title = 'Attributes';
  public $item_title = 'Attribute';
  public $attributes = ['data-type' => 'items-attributes'];
  public $name_complex = 'widget_attributes';
  public $state = 'closed';
  public $attribute_name_maxlength = 0xff;
  public $attribute_value_maxlength = 0xffff;

  ###########################
  ### static declarations ###
  ###########################

  static function complex_value_to_attributes($complex) {
    if ($complex) {
      core::array_sort_by_weight($complex);
      $attributes = [];
      foreach ($complex as $c_item)
        $attributes[$c_item->name] = new text(
                 $c_item->value, [],
          !empty($c_item->is_apply_translation),
          !empty($c_item->is_apply_tokens));
      return $attributes;
    }
  }

  static function complex_value_to_markup($complex) {
    if ($complex) {
      return core::data_to_attributes(
        static::complex_value_to_attributes($complex)
      );
    }
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  static function widget_manage_get($widget, $item, $c_row_id) {
    $result = parent::widget_manage_get($widget, $item, $c_row_id);
  # control for attribute name
    $field_name = new field_text('Name', null, [], +400);
    $field_name->attributes['data-style'] = 'inline';
    $field_name->description_state = 'hidden';
    $field_name->cform = $widget->cform;
    $field_name->build();
    $field_name->name_set($widget->name_get_complex().'__name__'.$c_row_id);
    $field_name->maxlength_set($widget->attribute_name_maxlength);
    $field_name->value_set($item->name);
  # control for attribute value
    $field_value = new field_text('Val.', null, [], +380);
    $field_value->attributes['data-style'] = 'inline';
    $field_value->description_state = 'hidden';
    $field_value->cform = $widget->cform;
    $field_value->build();
    $field_value->name_set($widget->name_get_complex().'__value__'.$c_row_id);
    $field_value->maxlength_set($widget->attribute_value_maxlength);
    $field_value->value_set($item->value);
    $field_value->required_set(false);
  # control for translation status
    $field_is_apply_translation = new field_checkbox('Tr.', null, [], +360);
    $field_is_apply_translation->attributes['data-style'] = 'inline';
    $field_is_apply_translation->attribute_insert('title', new text('apply translation'), 'element_attributes');
    $field_is_apply_translation->cform = $widget->cform;
    $field_is_apply_translation->build();
    $field_is_apply_translation->name_set($widget->name_get_complex().'__is_apply_translation__'.$c_row_id);
    $field_is_apply_translation->checked_set(!empty($item->is_apply_translation));
  # control for tokens status
    $field_is_apply_tokens = new field_checkbox('To.', null, [], +340);
    $field_is_apply_tokens->attributes['data-style'] = 'inline';
    $field_is_apply_tokens->attribute_insert('title', new text('apply tokens'), 'element_attributes');
    $field_is_apply_tokens->cform = $widget->cform;
    $field_is_apply_tokens->build();
    $field_is_apply_tokens->name_set($widget->name_get_complex().'__is_apply_tokens__'.$c_row_id);
    $field_is_apply_tokens->checked_set(!empty($item->is_apply_tokens));
  # relate new controls with the widget
    $widget->controls['#name__'.                $c_row_id] = $field_name;
    $widget->controls['#value__'.               $c_row_id] = $field_value;
    $widget->controls['#is_apply_translation__'.$c_row_id] = $field_is_apply_translation;
    $widget->controls['#is_apply_tokens__'.     $c_row_id] = $field_is_apply_tokens;
    $result->child_insert($field_name,                 'name');
    $result->child_insert($field_value,                'value');
    $result->child_insert($field_is_apply_translation, 'is_apply_translation');
    $result->child_insert($field_is_apply_tokens,      'is_apply_tokens');
    return $result;
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  static function on_button_click_insert($widget, $form, $npath, $button) {
    $min_weight = 0;
    $items = $widget->items_get();
    foreach ($items as $c_row_id => $c_item)
      $min_weight = min($min_weight, $c_item->weight);
    $new_item = new \stdClass;
    $new_item->weight               = count($items) ? $min_weight - 5 : 0;
    $new_item->name                 = '';
    $new_item->value                = '';
    $new_item->is_apply_translation = false;
    $new_item->is_apply_tokens      = false;
    $items[] = $new_item;
    $widget->items_set($items);
    message::insert(new text_multiline([
      'Item of type "%%_type" was inserted.',
      'Do not forget to save the changes!'], [
      'type' => (new text($widget->item_title))->render() ]));
    return true;
  }

  static function on_request_value_set($widget, $form, $npath) {
    $items = $widget->items_get();
    foreach ($items as $c_row_id => $c_item) {
      $c_item->weight               = (int)$widget->controls['#weight__'.               $c_row_id]->value_get();
      $c_item->name                 =      $widget->controls['#name__'.                 $c_row_id]->value_get();
      $c_item->value                =      $widget->controls['#value__'.                $c_row_id]->value_get();
      $c_item->is_apply_translation =      $widget->controls['#is_apply_translation__'. $c_row_id]->checked_get();
      $c_item->is_apply_tokens      =      $widget->controls['#is_apply_tokens__'.      $c_row_id]->checked_get(); }
    $widget->items_set($items);
  }

}}