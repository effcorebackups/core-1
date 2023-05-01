<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_text_object extends control implements control_complex {

  public $tag_name = 'x-group';
  public $name_complex = 'text_object';
  public $field_text_title = null;
  public $field_text_value = '';
  public $field_text_maxlength = 255;
  public $field_text_required = false;
  public $field_is_apply_translation_checked = false;
  public $field_is_apply_tokens_checked = false;
  public $attributes = [
    'data-type' => 'text_object',
    'role'      => 'group'
  ];

  function build() {
    if (!$this->is_builded) {
      $this->child_insert(static::widget_manage_get($this), 'manage');
      $this->is_builded = true;
    }
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  function value_get($options = []) { # return: object:text | serialize(object:text)
    $result = new text(
      $this->controls['#text']->value_get(), [],
      $this->controls['#is_apply_translation']->checked_get(),
      $this->controls['#is_apply_tokens'     ]->checked_get()
    );
    if (!empty($options['return_serialized']))
         return serialize($result);
    else return           $result;
  }

  function value_set($value) {
    $this->value_set_initial($value);
    if (core::data_is_serialized($value)) $value = unserialize($value);
    if ($value === null) $value = new text('', [], false, false);
    if ($value ===  '' ) $value = new text('', [], false, false);
    if ($value instanceof text) {
      $this->controls['#text'                ]->  value_set($value->text);
      $this->controls['#is_apply_translation']->checked_set($value->is_apply_translation);
      $this->controls['#is_apply_tokens'     ]->checked_set($value->is_apply_tokens);
    }
  }

  function name_get_complex() {
    return $this->name_complex;
  }

  function disabled_get() {
    return false;
  }

  ###########################
  ### static declarations ###
  ###########################

  static function widget_manage_get($widget) {
    $result = new node;
  # control for text value
    $field_text = new field_text;
    $field_text->attributes['data-role'] = 'title';
    $field_text->title = $widget->field_text_title;
    $field_text->build();
    $field_text->name_set($widget->name_get_complex().'__text');
    $field_text->value_set($widget->field_text_value);
    $field_text->maxlength_set($widget->field_text_maxlength);
    $field_text->required_set($widget->field_text_required);
  # control for translation status
    $field_is_apply_translation = new field_checkbox('Tr.');
    $field_is_apply_translation->attributes['data-role'] = 'is-apply-translation';
    $field_is_apply_translation->attribute_insert('title', new text('Is apply translation'), 'element_attributes');
    $field_is_apply_translation->build();
    $field_is_apply_translation->name_set($widget->name_get_complex().'__is_apply_translation');
    $field_is_apply_translation->checked_set($widget->field_is_apply_translation_checked);
  # control for tokens status
    $field_is_apply_tokens = new field_checkbox('To.');
    $field_is_apply_tokens->attributes['data-role'] = 'is-apply-tokens';
    $field_is_apply_tokens->attribute_insert('title', new text('Is apply tokens'), 'element_attributes');
    $field_is_apply_tokens->build();
    $field_is_apply_tokens->name_set($widget->name_get_complex().'__is_apply_tokens');
    $field_is_apply_tokens->checked_set($widget->field_is_apply_tokens_checked);
  # relate new controls with the widget
    $widget->controls['#text'                ] = $field_text;
    $widget->controls['#is_apply_translation'] = $field_is_apply_translation;
    $widget->controls['#is_apply_tokens'     ] = $field_is_apply_tokens;
    $result->child_insert($field_text,                 'field_text');
    $result->child_insert($field_is_apply_translation, 'field_is_apply_translation');
    $result->child_insert($field_is_apply_tokens,      'field_is_apply_tokens');
    return $result;
  }

}}