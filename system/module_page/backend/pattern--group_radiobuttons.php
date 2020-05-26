<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class group_radiobuttons extends control implements complex_control {

  public $tag_name = 'x-group';
  public $attributes = [
    'data-type' => 'radiobuttons',
    'role'      => 'radiogroup'];
  public $title_attributes = ['data-group-title' => true];
  public $name_prefix = null; # unused inherited property
# ─────────────────────────────────────────────────────────────────────
  public $field_class = '\\effcore\\field_radiobutton';
  public $field_tag_name = 'x-field';
  public $field_title_tag_name = 'label';
  public $field_title_position = 'bottom';
  public $element_attributes = [];
  public $required_any = false;
  public $values   = [];
  public $required = [];
  public $disabled = [];
  public $checked  = [];

  function __construct($values = null, $required = null, $disabled = null, $checked = null) {
    if ($values  ) $this->values   = $values;
    if ($required) $this->required = $required;
    if ($disabled) $this->disabled = $disabled;
    if ($checked ) $this->checked  = $checked;
    parent::__construct();
  }

  function build() {
    if (!$this->is_builded) {
      foreach ($this->values as $value => $title)
        $this->field_insert($title, null, ['value' => $value], $value);
      $this->is_builded = true;
    }
  }

  function field_insert($title = null, $description = null, $attributes = [], $new_id = null, $weight = 0) {
    $field                 = new $this->field_class;
    $field->title          = $title;
    $field->description    = $description;
    $field->weight         = $weight;
    $field->tag_name       = $this->field_tag_name;
    $field->title_tag_name = $this->field_title_tag_name;
    $field->title_position = $this->field_title_position;
    $field->build();
    $element = $field->child_select('element');
    foreach ($attributes + $this->attributes_select('element_attributes') as $c_key => $c_value)
      $element->attribute_insert                                            ($c_key,   $c_value);
    $value = $element->attribute_select('value');
    if (isset($this->required[$value])) $field->required_set();
    if (isset($this->checked [$value])) $field-> checked_set();
    if (isset($this->disabled[$value])) $field->disabled_set();
    return $this->child_insert($field, $new_id);
  }

  function name_get_complex($trim = true) {
  # try to find the name in 'element_attributes'
        $element_attributes_name = $this->attributes_select('element_attributes')['name'] ?? '';
        $element_attributes_name = $trim ? rtrim($element_attributes_name, '[]') : $element_attributes_name;
    if ($element_attributes_name) return
        $element_attributes_name;
  # search in first child (instance of field_class)
    else foreach ($this->children_select() as $c_child) {
      if ($c_child instanceof $this->field_class) {
        return $c_child->name_get($trim);
      }
    }
  }

  function value_get_complex()       {return $this->value_get();      }
  function value_set_complex($value) {       $this->value_set($value);}

  function value_get() {
    foreach ($this->children_select() as $c_child) {
      if ($c_child instanceof $this->field_class &&
          $c_child->checked_get() == true) {
        return $c_child->value_get();
      }
    }
    return '';
  }

  function value_set($value) {
    foreach ($this->children_select() as $c_child) if ($c_child instanceof $this->field_class) $c_child->checked_set(false);
    foreach ($this->children_select() as $c_child) if ($c_child instanceof $this->field_class) {
      if ($c_child->value_get() == $value) {
        $c_child->checked_set(true);
        return true;
      }
    }
  }

  function render_self() {
    if ($this->title && $this->title_is_visible == 0) return (new markup($this->title_tag_name, $this->title_attributes + ['data-mark-required' => $this->required_any ? 'true' : null, 'aria-hidden' => 'true'], $this->title))->render();
    if ($this->title && $this->title_is_visible != 0) return (new markup($this->title_tag_name, $this->title_attributes + ['data-mark-required' => $this->required_any ? 'true' : null                         ], $this->title))->render();
  }

  ###########################
  ### static declarations ###
  ###########################

  static function on_validate(&$group, $form, $npath) {
    return static::validate_required_any($group, $form, $npath);
  }

  static function validate_required_any(&$group, $form, $npath) {
    if ($group->required_any && $group->value_get() == null) {
      $group->error_set_in_container();
      $form->error_set(
        'Group "%%_title" should contain at least one selected item!', ['title' => translation::apply($group->title)]
      );
    } else {
      return true;
    }
  }

}}