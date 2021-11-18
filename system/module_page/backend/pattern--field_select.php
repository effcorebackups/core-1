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
  public $values   = [];
  public $selected = [];
  public $disabled = [];

  function build() {
    if (!$this->is_builded) {
      parent::build();
      $this->child_select('element')->children_delete();
      foreach ($this->values as $c_id => $c_data) {
        if (is_object($c_data) &&
               !empty($c_data->title) &&
               !empty($c_data->values)) {
          if (!$this->optgroup_select($c_id))
               $this->optgroup_insert($c_id, $c_data->title);
          foreach ($c_data->values as $g_id => $g_data)
               $this->option_insert($g_data, $g_id, [], $c_id);
        } else $this->option_insert($c_data,            $c_id); }
      $this->is_builded = true;
    }
  }

  function value_get() {
    $element = $this->child_select('element');
    foreach ($element->children_select_recursive() as $c_child) {
      if ($c_child instanceof node        &&
          $c_child->tag_name === 'option' &&
          $c_child->attribute_select('selected') === 'selected') {
        return $c_child->attribute_select('value');
      }
    }
  }

  function value_set($value) {
    $this->value_set_initial($value);
    $element = $this->child_select('element');
    foreach ($element->children_select_recursive() as $c_child) {
      if ($c_child instanceof node &&
          $c_child->tag_name === 'option') {
        if ((string)$c_child->attribute_select('value') === (string)$value) $c_child->attribute_insert('selected', 'selected');
        if ((string)$c_child->attribute_select('value') !== (string)$value) $c_child->attribute_delete('selected');
      }
    }
  }

  function values_get() {
    $result = [];
    $element = $this->child_select('element');
    foreach ($element->children_select_recursive() as $c_child) {
      if ($c_child instanceof node        &&
          $c_child->tag_name === 'option' &&
          $c_child->attribute_select('selected') === 'selected') {
        $result[$c_child->attribute_select('value')] = $c_child->child_select('content')->text_select();
      }
    }
    return $result;
  }

  function values_get_allowed() {
    $result = [];
    $element = $this->child_select('element');
    foreach ($element->children_select_recursive() as $c_child) {
      if ($c_child instanceof node &&
          $c_child->tag_name === 'option') {
        if ($c_child->attribute_select('disabled') !== 'disabled' &&
            $c_child->attribute_select('disabled') !== true) {
          $result[$c_child->attribute_select('value')] = $c_child->child_select('content')->text_select();
        }
      }
    }
    return $result;
  }

  function values_set($values, $clear = true) {
    $element = $this->child_select('element');
    foreach ($element->children_select_recursive() as $c_child) {
      if ($c_child instanceof node &&
          $c_child->tag_name === 'option') {
        if (core::array_search__any_array_item_is_equal_value($c_child->attribute_select('value'), $values))
                        $c_child->attribute_insert('selected', 'selected');
        elseif ($clear) $c_child->attribute_delete('selected');
      }
    }
  }

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
    if (isset($this->selected[$value])) $option->attribute_insert('selected', 'selected');
    if (isset($this->disabled[$value])) $option->attribute_insert('disabled',    true   );
    if (!$optgroup_id)
         $this->child_select('element')->child_insert(                            $option, $value);
    else $this->child_select('element')->child_select($optgroup_id)->child_insert($option, $value);
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
      $field->values_set($new_values);
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
      $field->values_set($new_values);
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