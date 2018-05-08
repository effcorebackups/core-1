<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_number extends field_text {

  public $title = 'Number';
  public $attributes = ['x-type' => 'number'];
  public $element_attributes_default = [
    'type'     => 'number',
    'name'     => 'number',
    'required' => 'required',
    'min'      => form::input_min_number,
    'max'      => form::input_max_number,
    'step'     => 1,
    'value'    => 0
  ];

  ###########################
  ### static declarations ###
  ###########################

  static function validate($field, $form, $dpath) {
    $element = $field->child_select('element');
    $name = $field->get_element_name();
    $type = $field->get_element_type();
    if ($name && $type) {
      if (static::is_disabled($field, $element)) return true;
      if (static::is_readonly($field, $element)) return true;
      $cur_index = static::get_cur_index($name);
      $new_value = static::get_new_value($name, $cur_index);
      $result = static::validate_required ($field, $form, $dpath, $element, $new_value) &&
                static::validate_minlength($field, $form, $dpath, $element, $new_value) &&
                static::validate_maxlength($field, $form, $dpath, $element, $new_value) &&
                static::validate_number   ($field, $form, $dpath, $element, $new_value);
      $element->attribute_insert('value', $new_value);
      return $result;
    }
  }

  # number validation matrix - [number('...') => is_valid(0|1|2), ...]
  # ─────────────────────────────────────────────────────────────────────
  # ''   => 0, '-'   => 0 | '0'   => 1, '-0'   => 0 | '1'   => 1, '-1'   => 1 | '01'   => 0, '-01'   => 0 | '10'   => 1, '-10'   => 1
  # '.'  => 0, '-.'  => 0 | '0.'  => 0, '-0.'  => 0 | '1.'  => 0, '-1.'  => 0 | '01.'  => 0, '-01.'  => 0 | '10.'  => 0, '-10.'  => 0
  # '.0' => 0, '-.0' => 0 | '0.0' => 1, '-0.0' => 2 | '1.0' => 1, '-1.0' => 1 | '01.0' => 0, '-01.0' => 0 | '10.0' => 1, '-10.0' => 1
  # ─────────────────────────────────────────────────────────────────────

  static function validate_number($field, $form, $dpath, $element, &$new_value) {
    if (strlen($new_value) && !preg_match(
        '%^(?<integer>[-]?[1-9][0-9]*|0)$|'.
         '^(?<float_s>[-]?[0-9][.][0-9]{1,3})$|'.
         '^(?<float_l>[-]?[1-9][0-9]+[.][0-9]{1,3})$%S', $new_value)) {
      $form->add_error($dpath.'/element',
        translation::get('Field "%%_title" contains incorrect value!', ['title' => translation::get($field->title)]).br.
        translation::get('Field value is not a valid number.')
      );
    } else {
      return true;
    }
  }

}}