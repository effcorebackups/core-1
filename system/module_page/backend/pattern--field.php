<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field extends container {

  public $tag_name = 'x-field';
  public $title_tag_name = 'label';
# ─────────────────────────────────────────────────────────────────────
  public $element_tag_name = 'input';
  public $element_class = '\\effcore\\markup_simple';
  public $element_attributes_default = [];
  public $element_attributes = [];

  function __construct($title = null, $description = null, $attributes = [], $weight = 0) {
    parent::__construct(null, $title, $description, $attributes, [], $weight);
  }

  function build() {
    if (!$this->child_select('element')) {
      $element = new $this->element_class($this->element_tag_name);
      $this->child_insert($element, 'element');
      $attributes = $this->attribute_select_all('element_attributes') +
                    $this->attribute_select_all('element_attributes_default');
      foreach ($attributes as $c_name => $c_value) {
        if ($c_value === null) $element->attribute_delete($c_name);
        if ($c_value !== null) $element->attribute_insert($c_name, $c_value);
      }
    }
  }

  function get_element_name($trim = true) {
    $element = $this->child_select('element');
    return $trim ? rtrim($element->attribute_select('name'), '[]') :
                         $element->attribute_select('name');
  }

  function get_element_type($full = true) {
    $element = $this->child_select('element');
    switch ($element->tag_name) {
      case 'input'   : return 'input'.($full ? ':'.$element->attribute_select('type') : '');
      case 'textarea': return 'textarea';
      case 'select'  : return 'select';
    }
  }

  function render() {
    $element = $this->child_select('element');
    if ($element instanceof node_simple && $element->attribute_select('disabled')) $this->attribute_insert('class', ['disabled' => 'disabled']);
    if ($element instanceof node_simple && $element->attribute_select('required')) $this->attribute_insert('class', ['required' => 'required']);
    return parent::render();
  }

  function render_self() {
    $element = $this->child_select('element');
    if ($this->title) {
      $required_mark = $this->attribute_select('required') || ($element instanceof node_simple && $element->attribute_select('required')) ? $this->render_required_mark() : '';
      return (new markup($this->title_tag_name, [], [
        $this->title, $required_mark
      ]))->render();
    }
  }

  function render_description() {
    $return = [];
    $element = $this->child_select('element');
    if ($element instanceof node_simple && $element->attribute_select('minlength'))       $return[] = new markup('p', ['class' => ['minlength' => 'minlength']], translation::get('Field must contain a minimum of %%_num characters.', ['num' => $element->attribute_select('minlength')]));
    if ($element instanceof node_simple && $element->attribute_select('maxlength'))       $return[] = new markup('p', ['class' => ['maxlength' => 'maxlength']], translation::get('Field must contain a maximum of %%_num characters.', ['num' => $element->attribute_select('maxlength')]));
    if ($element instanceof node_simple && $element->attribute_select('min'))             $return[] = new markup('p', ['class' => ['min' => 'min']],             translation::get('Minimal field value: %%_value.', ['value' => $element->attribute_select('min')]));
    if ($element instanceof node_simple && $element->attribute_select('max'))             $return[] = new markup('p', ['class' => ['max' => 'max']],             translation::get('Maximal field value: %%_value.', ['value' => $element->attribute_select('max')]));
    if ($element instanceof node_simple && $element->attribute_select('type') == 'range') $return[] = new markup('p', ['class' => ['cur' => 'cur']],             translation::get('Current field value: %%_value.', ['value' => (new markup('x-value', [], $element->attribute_select('value')))->render()]));
    if ($this->description)                                                               $return[] = new markup('p', [], $this->description);
    if (count($return)) {
      return (new markup($this->description_tag_name, [], $return))->render();
    }
  }

  ##################
  ### validation ###
  ##################

  function validate($form, $dpath) {
    $element = $this->child_select('element');
    $name = $this->get_element_name();
    $type = $this->get_element_type();
    if ($name && $type) {
      switch ($type) {
        case 'input:checkbox': return;
        case 'input:color'   : return;
        case 'input:date'    : return;
        case 'input:email'   : return;
        case 'input:file'    : return;
        case 'input:number'  : return;
        case 'input:password': return;
        case 'input:radio'   : return;
        case 'input:range'   : return;
        case 'input:search'  : return;
        case 'input:tel'     : return;
        case 'input:text'    : return;
        case 'input:time'    : return;
        case 'input:url'     : return;
        case 'select'        : return;
        case 'textarea'      : return;
      }
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $indexes = [];

  static function get_cur_index($name) {
    return !isset(static::$indexes[$name]) ?
                 (static::$indexes[$name] = 0) :
                ++static::$indexes[$name];
  }

  static function get_new_value($name, $index = 0) {
    return !isset($_POST[$name]) ? '' :
       (is_string($_POST[$name]) ? $_POST[$name] : 
        (is_array($_POST[$name]) &&
            isset($_POST[$name][$index]) ?
                  $_POST[$name][$index] : ''));
  }

  static function validate_is_disabled($field, $element) {
    return $element->attribute_select('disabled') ? true : false;
  }

  static function validate_is_readonly($field, $element) {
    return $element->attribute_select('readonly') ? true : false;
  }

}}