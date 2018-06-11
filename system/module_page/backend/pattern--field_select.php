<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_select extends field {

  public $attributes = ['x-type' => 'select'];
  public $element_class = '\\effcore\\markup';
  public $element_tag_name = 'select';
  public $element_attributes_default = [
    'name'     => 'select',
    'required' => 'required'
  ];
# ─────────────────────────────────────────────────────────────────────
  public $values = [];
  public $selected = [];
  public $disabled = [];

  function build() {
    parent::build();
    foreach ($this->values as $c_id => $c_data) {
      if (is_object($c_data) &&
             !empty($c_data->title) &&
             !empty($c_data->values)) {
        if (!$this->optgroup_select($c_id))
             $this->optgroup_insert($c_id, $c_data->title);
        foreach ($c_data->values as $g_id => $g_data) {
          $this->option_insert($g_data, $g_id, [], $c_id);
        }
      } else {
        $this->option_insert($c_data, $c_id);
      }
    }
  }

  function value_get() {
    $return = [];
    $element = $this->child_select('element');
    foreach ($element->children_select_recursive() as $c_item) {
      if ($c_item instanceof node       &&
          $c_item->tag_name == 'option' &&
          $c_item->attribute_select('selected') == 'selected') {
        $return[$c_item->attribute_select('value')] = $c_item->child_select('content')->text_select();
      }
    }
    return $return;
  }

  function optgroup_select($id) {
    return $this->child_select('element')->child_select($id);
  }

  function optgroup_insert($id, $title, $attr = []) {
    $this->child_select('element')->child_insert(
      new markup('optgroup', $attr + ['label' => $title]), $id
    );
  }

  function option_insert($title, $value, $attr = [], $optgroup_id = null) {
    $option = new markup('option', $attr, ['content' => $title]);
    $option->attribute_insert('value', $value === 'not_selected' ? '' : $value);
    if (isset($this->selected[$value])) $option->attribute_insert('selected', 'selected');
    if (isset($this->disabled[$value])) $option->attribute_insert('disabled', 'disabled');
    if (!$optgroup_id)
         $this->child_select('element')->child_insert($option, $value);
    else $this->child_select('element')->child_select($optgroup_id)->child_insert($option, $value);
  }

  ###########################
  ### static declarations ###
  ###########################

  static function validate($field, $form, $npath) {
    $element = $field->child_select('element');
    $name = $field->element_name_get();
    $type = $field->element_type_get();
    if ($name && $type) {
      if (static::is_disabled($field, $element)) return true;
      $allowed_values = static::allowed_values_get($element);
      $new_values = static::new_value_multiple_get($name);
      $new_values = array_unique(array_intersect($new_values, $allowed_values)); # filter fake values
      $result = static::validate_required($field, $form, $npath, $element, $new_values) &&
                static::validate_multiple($field, $form, $npath, $element, $new_values);
      foreach ($element->children_select_recursive() as $c_item) {
        if ($c_item instanceof node && $c_item->tag_name == 'option') {
          if (core::in_array_string_compare($c_item->attribute_select('value'), $new_values))
               $c_item->attribute_insert('selected', 'selected');
          else $c_item->attribute_delete('selected');
        }
      }
      return $result;
    }
  }

  static function allowed_values_get($element) {
    $return = [];
    foreach ($element->children_select_recursive() as $c_item) {
      if ($c_item instanceof node       &&
          $c_item->tag_name == 'option' &&
         !$c_item->attribute_select('disabled')) {
        $return[] = $c_item->attribute_select('value');
      }
    }
    return $return;
  }

  static function validate_required($field, $form, $npath, $element, &$new_values) {
    if ($element->attribute_select('required') && empty(array_filter($new_values, 'strlen'))) {
      $form->add_error($npath.'/element',
        translation::get('Field "%%_title" must be selected!', ['title' => translation::get($field->title)])
      );
    } else {
      return true;
    }
  }

  static function validate_multiple($field, $form, $npath, $element, &$new_values) {
    if (!$element->attribute_select('multiple') && count($new_values) > 1) {
      $new_values = array_slice($new_values, -1);
      $form->add_error($npath.'/element',
        translation::get('Field "%%_title" does not support multiple select!', ['title' => translation::get($field->title)])
      );
    } else {
      return true;
    }
  }

}}