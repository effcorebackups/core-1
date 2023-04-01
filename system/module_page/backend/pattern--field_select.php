<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_select extends field {

  public $title = 'Selection list';
  public $attributes = ['data-type' => 'select'];
  public $element_class = '\\effcore\\markup';
  public $element_tag_name = 'select';
  public $element_attributes = [
    'name'     => 'select',
    'required' => true];
# ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
  public $items    = [];
  public $selected = [];
  public $disabled = [];

  function build() {
    if (!$this->is_builded) {
      parent::build();
      $this->child_select('element')->children_delete();
      foreach ($this->items as $c_id => $c_item) {
        if (is_object($c_item) &&
               !empty($c_item->title) &&
               !empty($c_item->items)) {
          if (!$this->optgroup_select($c_id))
               $this->optgroup_insert($c_id, $c_item->title);
          foreach ($c_item->items as $g_id => $g_item)
               $this->option_insert($g_item, $g_id, [], $c_id);
        } else $this->option_insert($c_item,            $c_id); }
      $this->is_builded = true;
    }
  }

  function items_set($items = []) {
    $this->items = $items;
  }

  function items_get() {
    return $this->items;
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  function optgroup_select($id) {
    return $this->child_select('element')->child_select($id);
  }

  function optgroup_insert($id, $title, $attributes = []) {
    $this->child_select('element')->child_insert(
      new markup('optgroup', $attributes + ['label' => new text($title)]), $id
    );
  }

  function option_insert($title, $value, $attributes = [], $optgroup_id = null) {
    $option = new markup('option', $attributes, ['content' => $title]);
    $option->attribute_insert('value', $value === 'not_selected' ? '' : $value);
    if (isset($this->selected[$value])) $option->attribute_insert('selected', true);
    if (isset($this->disabled[$value])) $option->attribute_insert('disabled', true);
    if (!$optgroup_id)
         $this->child_select('element')->child_insert(                            $option, $value);
    else $this->child_select('element')->child_select($optgroup_id)->child_insert($option, $value);
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  function value_get($options = []) { # return: null | number | string | array | serialize(array)
    $result = [];
    $element = $this->child_select('element');
    foreach ($element->children_select_recursive() as $c_child) {
      if ($c_child instanceof node        &&
          $c_child->tag_name === 'option' &&
          $c_child->attribute_select('selected') === true) {
        $result[$c_child->attribute_select('value')] =
                $c_child->child_select('content')->text_select();
      }
    }
    if ($this->multiple_get() !== true) return key($result);
    if ($this->multiple_get() === true) {
      if (!empty($options['return_serialized']))
           return serialize($result);
      else return           $result;
    }
  }

  function values_get_allowed() {
    $result = [];
    $element = $this->child_select('element');
    foreach ($element->children_select_recursive() as $c_child) {
      if ($c_child instanceof node &&
          $c_child->tag_name === 'option') {
        if ($c_child->attribute_select('disabled') !== true) {
          $result[$c_child->attribute_select('value')] =
                  $c_child->child_select('content')->text_select();
        }
      }
    }
    return $result;
  }

  function value_set($value) {
    $this->value_set_initial($value);
    if (core::data_is_serialized($value)) $value = unserialize($value);
    if (is_null  ($value)) $value = [];
    if (is_int   ($value)) $value = [core::format_number($value)];
    if (is_float ($value)) $value = [core::format_number($value, core::fpart_max_len)];
    if (is_string($value)) $value = [$value];
    if (is_array ($value)) {
      $element = $this->child_select('element');
      $element_children = $element->children_select_recursive();
      foreach ($element_children as $c_child) if ($c_child instanceof node && $c_child->tag_name === 'option') $c_child->attribute_delete('selected');
      foreach ($element_children as $c_child) if ($c_child instanceof node && $c_child->tag_name === 'option') {
        if ($c_child->attribute_select('disabled') !== true) {
          if (core::array_search__any_array_item_is_equal_value($c_child->attribute_select('value'), $value)) {
            $c_child->attribute_insert('selected', true);
            if ($this->multiple_get() === false) {
              return;
            }
          }
        }
      }
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static function on_request_value_set($field, $form, $npath) {
    $name = $field->name_get();
    $type = $field->type_get();
    if ($name && $type) {
      if ($field->disabled_get()) return true;
      $values_allowed = $field->values_get_allowed();
      $new_values = request::values_get($name, $form->source_get());
      $new_values = array_unique(array_intersect($new_values, array_keys($values_allowed))); # filter fake values
      $field->value_set($new_values);
    }
  }

  static function on_validate($field, $form, $npath) {
    $element = $field->child_select('element');
    $name = $field->name_get();
    $type = $field->type_get();
    if ($name && $type) {
      if ($field->disabled_get()) return true;
      $values_allowed = $field->values_get_allowed();
      $new_values = request::values_get($name, $form->source_get());
      $new_values = array_unique(array_intersect($new_values, array_keys($values_allowed))); # filter fake values
      $result = static::validate_required($field, $form, $element, $new_values) &&
                static::validate_multiple($field, $form, $element, $new_values);
      $field->value_set($new_values);
      return $result;
    }
  }

  static function validate_required($field, $form, $element, &$new_values) {
    if ($field->required_get() && empty(array_filter($new_values, 'strlen'))) {
      $field->error_set(
        'Field "%%_title" should be selected!', ['title' => (new text($field->title))->render() ]
      );
    } else {
      return true;
    }
  }

  static function validate_multiple($field, $form, $element, &$new_values) {
    if (!$field->multiple_get() && count($new_values) > 1) {
      $new_values = array_slice($new_values, -1);
      $field->error_set(
        'Field "%%_title" does not support multiple select!', ['title' => (new text($field->title))->render() ]
      );
    } else {
      return true;
    }
  }

}}