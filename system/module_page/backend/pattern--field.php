<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

  # html5 elements and attributes support:
  # ┌────────────────╥───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┐
  # │   ╲  attribute ║ d │ r │ r │ m │ m │ m │ m │ s │ m │ c │ p │ v │
  # │    ╲           ║ i │ e │ e │ i │ a │ i │ a │ t │ u │ h │ a │ a │
  # │     ╲          ║ s │ a │ q │ n │ x │ n │ x │ e │ l │ e │ t │ l │
  # │      ╲         ║ a │ d │ u │ l │ l │   │   │ p │ t │ c │ t │ u │
  # │       ╲        ║ b │ o │ i │ e │ e │   │   │   │ i │ k │ e │ e │
  # │        ╲       ║ l │ n │ r │ n │ n │   │   │   │ p │ e │ r │   │
  # │         ╲      ║ e │ l │ e │ g │ g │   │   │   │ l │ d │ n │ [ │
  # │          ╲     ║ d │ y │ d │ t │ t │   │   │   │ e │   │   │ ] │
  # │ element   ╲    ║   │   │   │ h │ h │   │   │   │   │   │   │   │
  # ╞════════════════╬═══╪═══╪═══╪═══╪═══╪═══╪═══╪═══╪═══╪═══╪═══╪═══╡
  # │ input:text     ║ + │ + │ + │ x │ + │   │   │   │   │   │ + │ + │
  # │ input:color    ║ + │ x │ x │ x │ x │   │   │   │   │   │ x │ + │
  # │ input:email    ║ + │ + │ + │ x │ + │   │   │   │ + │   │ + │ + │
  # │ input:file     ║ + │   │ + │   │   │   │   │   │ + │   │   │ + │
  # │ input:password ║ + │ + │ + │ x │ + │   │   │   │   │   │ + │ + │
  # │ input:search   ║ + │ + │ + │ x │ + │   │   │   │   │   │ + │ + │
  # │ input:tel      ║ + │ + │ + │ x │ + │   │   │   │   │   │ + │ + │
  # │ input:url      ║ + │ + │ + │ x │ + │   │   │   │   │   │ + │ + │
  # │ input:date     ║ + │ + │ + │ x │ x │ + │ + │ - │   │   │ x │ + │
  # │ input:time     ║ + │ + │ + │ x │ x │ + │ + │ - │   │   │ x │ + │
  # │ input:number   ║ + │ + │ + │ x │ x │ + │ + │ + │   │   │ x │ + │
  # │ input:range    ║ + │ x │ x │ x │ x │ + │ + │ + │   │   │ x │ + │
  # │ textarea       ║ + │ + │ + │ x │ + │   │   │   │   │   │ x │ + │
  # │ select         ║ + │   │ + │   │   │   │   │   │ + │   │   │ + │
  # │ select:option  ║ + │   │   │   │   │   │   │   │   │   │   │   │
  # │ input:checkbox ║ + │   │ + │   │   │   │   │   │   │ + │   │ + │
  # │ input:radio    ║ + │   │ + │   │   │   │   │   │   │ + │   │ + │
  # └────────────────╨───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┘
  # note: x - is extended feature of the system
  # ┌──────────────────────╥────────────────────────────────────────────────┐
  # │ input:hidden         ║ protected from change the value from user side │
  # │ input:button         ║ not processed - use button:button instead      │
  # │ input:reset          ║ not processed - use button:reset instead       │
  # │ input:submit         ║ not processed - use button:submit instead      │
  # │ input:image          ║ not processed - use imgage instead             │
  # │ input:datetime       ║ not processed - use date + time instead        │
  # │ input:datetime-local ║ not processed - use date + time instead        │
  # │ input:week           ║ not processed                                  │
  # │ input:month          ║ not processed                                  │
  # └──────────────────────╨────────────────────────────────────────────────┘

namespace effcore {
          class field extends container {

  public $tag_name = 'x-field';
  public $title_tag_name = 'label';
# ─────────────────────────────────────────────────────────────────────
  public $element_tag_name = 'input';
  public $element_class = '\\effcore\\markup_simple';
  public $element_attributes_default = [];
  public $element_attributes = [];
  public $description_state = 'closed'; # opened | closed[checked] | hidden
# ─────────────────────────────────────────────────────────────────────
  static public $errors = [];

  function __construct($title = null, $description = null, $attributes = [], $weight = 0) {
    parent::__construct(null, $title, $description, $attributes, [], $weight);
  }

  function build() {
    if (!$this->child_select('element')) {
      $element = new $this->element_class($this->element_tag_name);
      $this->child_insert($element, 'element');
      $attributes = $this->attributes_select('element_attributes') +
                    $this->attributes_select('element_attributes_default');
      foreach ($attributes as $c_name => $c_value) {
        if ($c_value === null) $element->attribute_delete($c_name);
        if ($c_value !== null) $element->attribute_insert($c_name, $c_value);
      }
    }
  }

  # ─────────────────────────────────────────────────────────────────────
  # functionality for errors
  # ─────────────────────────────────────────────────────────────────────

  function error_set($message = null) {
    static::$errors[$this->npath][] = $message;
    if (count(static::$errors[$this->npath]) == 1) {
      $element = $this->child_select('element');
      $element->attribute_insert('class', ['error' => 'error']);
    }
  }

  function errors_count_get() {
    return count($this->errors_get());
  }

  function errors_get() {
    return isset(static::$errors[$this->npath]) ?
                 static::$errors[$this->npath] : [];
  }

  # ─────────────────────────────────────────────────────────────────────
  # element properties
  # ─────────────────────────────────────────────────────────────────────

  function element_name_get($trim = true) {
    $element = $this->child_select('element');
    return $trim ? rtrim($element->attribute_select('name'), '[]') :
                         $element->attribute_select('name');
  }

  function element_type_get($full = true) {
    $element = $this->child_select('element');
    switch ($element->tag_name) {
      case 'input'   : return 'input'.($full ? ':'.$element->attribute_select('type') : '');
      case 'textarea': return 'textarea';
      case 'select'  : return 'select';
    }
  }

  function element_required_get() {
    $element = $this->child_select('element');
    return $element->attribute_select('required') == 'required';
  }

  function element_required_set($is_required = true) {
    $element = $this->child_select('element');
    if ($is_required) $element->attribute_insert('required', 'required');
    else              $element->attribute_delete('required');
  }

  function element_checked_get() {
    $element = $this->child_select('element');
    return $element->attribute_select('checked') == 'checked';
  }

  function element_checked_set($is_checked = true) {
    $element = $this->child_select('element');
    if ($is_checked) $element->attribute_insert('checked', 'checked');
    else             $element->attribute_delete('checked');
  }

  function element_disabled_get() {
    $element = $this->child_select('element');
    return $element->attribute_select('disabled') == 'disabled';
  }

  function element_disabled_set($is_disabled = true) {
    $element = $this->child_select('element');
    if ($is_disabled) $element->attribute_insert('disabled', 'disabled');
    else              $element->attribute_delete('disabled');
  }

  # ─────────────────────────────────────────────────────────────────────
  # functionality for render
  # ─────────────────────────────────────────────────────────────────────

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
    if ($element instanceof node_simple) {
      if ($element->attribute_select('pattern') !== null)                                                                                          $return[] = new markup('p', ['class' => ['pattern'   => 'pattern']],   translation::get('Field value should match the regular expression %%_expression.', ['expression' => $element->attribute_select('pattern')]));
      if ($element->attribute_select('min') !== null)                                                                                              $return[] = new markup('p', ['class' => ['min'       => 'min']],       translation::get('Minimal field value: %%_value.', ['value' => $element->attribute_select('min')]));
      if ($element->attribute_select('max') !== null)                                                                                              $return[] = new markup('p', ['class' => ['max'       => 'max']],       translation::get('Maximal field value: %%_value.', ['value' => $element->attribute_select('max')]));
      if ($element->attribute_select('type') == 'range')                                                                                           $return[] = new markup('p', ['class' => ['cur'       => 'cur']],       translation::get('Current field value: %%_value.', ['value' => (new markup('x-value', [], $element->attribute_select('value')))->render()]));
      if ($element->attribute_select('minlength') !== null && $element->attribute_select('minlength') !== $element->attribute_select('maxlength')) $return[] = new markup('p', ['class' => ['minlength' => 'minlength']], translation::get('Field must contain a minimum of %%_number characters.', ['number' => $element->attribute_select('minlength')]));
      if ($element->attribute_select('maxlength') !== null && $element->attribute_select('maxlength') !== $element->attribute_select('minlength')) $return[] = new markup('p', ['class' => ['maxlength' => 'maxlength']], translation::get('Field must contain a maximum of %%_number characters.', ['number' => $element->attribute_select('maxlength')]));
      if ($element->attribute_select('minlength') !== null && $element->attribute_select('minlength') === $element->attribute_select('maxlength')) $return[] = new markup('p', ['class' => ['midlength' => 'midlength']], translation::get('Field must contain %%_number characters.',              ['number' => $element->attribute_select('minlength')]));
    }
    if ($this->description) $return[] = new markup('p', [], $this->description);
    if (count($return)) {
      $opener = new markup_simple('input', ['type' => 'checkbox', 'data-opener-type' => 'description', 'checked' => 'checked', 'title' => translation::get('Show description')]);
      if ($this->description_state == 'hidden'                                 ) return '';
      if ($this->description_state == 'opened' || $this->errors_count_get() > 0) return (new markup($this->description_tag_name, [], $return))->render();
      if ($this->description_state == 'closed')                return $opener->render().(new markup($this->description_tag_name, [], $return))->render();
      return '';
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $numbers = [];

  static function cur_number_get($name) {
    return !isset(static::$numbers[$name]) ?
                 (static::$numbers[$name] = 0) :
                ++static::$numbers[$name];
  }

  # ──────────────────────────────────────────────────────────────────────────────
  # functionality for $_POST and $_GET data
  # ──────────────────────────────────────────────────────────────────────────────

  static function request_values_reset() {
    $_POST = [];
    $_GET = [];
    $_REQUEST = [];
    $_FILES = [];
  }

  # conversion matrix:
  # ┌──────────────────────────────────────╥───────────────────────────────┐
  # │ input value (undefined|string|array) ║ output value                  │
  # ╞══════════════════════════════════════╬═══════════════════════════════╡
  # │ source[field] == undefined           ║ return ''                     │
  # │ source[field] == ''                  ║ return ''                     │
  # │ source[field] == 'value'             ║ return 'value'                │
  # ├──────────────────────────────────────╫───────────────────────────────┤
  # │ source[field] == [0 => '']           ║ return ''                     │
  # │ source[field] == [0 => '', …]        ║ return ''                     │
  # │ source[field] == [0 => 'value']      ║ return 'value'                │
  # │ source[field] == [0 => 'value', …]   ║ return 'value'                │
  # └──────────────────────────────────────╨───────────────────────────────┘

  static function request_value_get($name, $index = 0, $source = '_POST') {
    global ${$source};
    return !isset(${$source}[$name]) ? '' :
       (is_string(${$source}[$name]) ? ${$source}[$name] : 
        (is_array(${$source}[$name]) &&
            isset(${$source}[$name][$index]) ?
                  ${$source}[$name][$index] : ''));
  }

  # conversion matrix:
  # ┌──────────────────────────────────────╥───────────────────────────────┐
  # │ input value (undefined|string|array) ║ output value                  │
  # ╞══════════════════════════════════════╬═══════════════════════════════╡
  # │ source[field] == undefined           ║ return []                     │
  # │ source[field] == ''                  ║ return [0 => '']              │
  # │ source[field] == 'value'             ║ return [0 => 'value']         │
  # ├──────────────────────────────────────╫───────────────────────────────┤
  # │ source[field] == [0 => '']           ║ return [0 => '']              │
  # │ source[field] == [0 => '', …]        ║ return [0 => '', …]           │
  # │ source[field] == [0 => 'value']      ║ return [0 => 'value']         │
  # │ source[field] == [0 => 'value', …]   ║ return [0 => 'value', …]      │
  # └──────────────────────────────────────╨───────────────────────────────┘

  static function request_values_get($name, $source = '_POST') {
    global ${$source};
    return !isset(${$source}[$name]) ? [] :
       (is_string(${$source}[$name]) ? [${$source}[$name]] :
        (is_array(${$source}[$name]) ?
                  ${$source}[$name] : []));
  }

  static function request_values_set($name, $values, $source = '_POST') {
    global ${$source};
    ${$source}[$name] = $values;
  }

  # conversion matrix:
  # ┌─────────────────────────────────────────────────────────╥───────────────────────────────────────────────────────────────────────┐
  # │ input value (undefined|array)                           ║ output value                                                          │
  # ╞═════════════════════════════════════════════════════════╬═══════════════════════════════════════════════════════════════════════╡
  # │ $_FILES[field] == undefined                             ║ return []                                                             │
  # │ $_FILES[field] == [error = 4]                           ║ return []                                                             │
  # │ $_FILES[field] == [name = 'file']                       ║ return [0 => (object)[name = 'file']]                                 │
  # │ $_FILES[field] == [name = [0 => 'file']]                ║ return [0 => (object)[name = 'file']]                                 │
  # │ $_FILES[field] == [name = [0 => 'file1', 1 => 'file2']] ║ return [0 => (object)[name = 'file1'], 1 => (object)[name = 'file2']] │
  # └─────────────────────────────────────────────────────────╨───────────────────────────────────────────────────────────────────────┘

  static function request_files_get($name) {
    $return = [];
    if (isset($_FILES[$name]['name'])     &&
        isset($_FILES[$name]['type'])     &&
        isset($_FILES[$name]['size'])     &&
        isset($_FILES[$name]['tmp_name']) &&
        isset($_FILES[$name]['error'])) {
      $info = $_FILES[$name];
      if (!is_array($info['name']))     $info['name']     = [$info['name']];
      if (!is_array($info['type']))     $info['type']     = [$info['type']];
      if (!is_array($info['size']))     $info['size']     = [$info['size']];
      if (!is_array($info['tmp_name'])) $info['tmp_name'] = [$info['tmp_name']];
      if (!is_array($info['error']))    $info['error']    = [$info['error']];
      foreach ($info as $c_prop => $c_values) {
        foreach ($c_values as $c_index => $c_value) {
          if ($info['error'][$c_index] !== UPLOAD_ERR_NO_FILE) {
            if (!isset($return[$c_index]))
                       $return[$c_index] = new \stdClass;
            switch ($c_prop) {
              case 'name':
                $c_file = new file(trim(str_replace('/', '', $c_value), '.'));
                $return[$c_index]->{'name'} = $c_file->name_get();
                $return[$c_index]->{'type'} = $c_file->type_get();
                $return[$c_index]->{'file'} = $c_file->file_get();
                break;
              case 'type'    : $return[$c_index]->{'mime'}     = core::validate_mime_type($c_value) ? $c_value : ''; break;
              case 'tmp_name': $return[$c_index]->{'tmp_path'} = $c_value; break;
              default        : $return[$c_index]->{$c_prop}    = $c_value;
            }
          }
        }
      }
    }
    return $return;
  }

  static function is_disabled($field, $element) {
    return $element->attribute_select('disabled') ? true : false;
  }

  static function is_readonly($field, $element) {
    return $element->attribute_select('readonly') ? true : false;
  }

  static function validate($field, $form, $npath) {
    $name = $field->element_name_get();
    $type = $field->element_type_get();
  # add validate functionality to non specified fields
    if ($name && $type && get_called_class() == 'effcore\\field') {
      switch ($type) {
        case 'input:checkbox': return field_checkbox   ::validate($field, $form);
        case 'input:color'   : return field_color      ::validate($field, $form);
        case 'input:date'    : return field_date       ::validate($field, $form);
        case 'input:email'   : return field_email      ::validate($field, $form);
        case 'input:file'    : return field_file       ::validate($field, $form);
        case 'input:number'  : return field_number     ::validate($field, $form);
        case 'input:password': return field_password   ::validate($field, $form);
        case 'input:radio'   : return field_radiobutton::validate($field, $form);
        case 'input:range'   : return field_range      ::validate($field, $form);
        case 'input:search'  : return field_search     ::validate($field, $form);
        case 'input:tel'     : return field_phone      ::validate($field, $form);
        case 'input:text'    : return field_text       ::validate($field, $form);
        case 'input:time'    : return field_time       ::validate($field, $form);
        case 'input:url'     : return field_url        ::validate($field, $form);
        case 'select'        : return field_select     ::validate($field, $form);
        case 'textarea'      : return field_textarea   ::validate($field, $form);
      }
    }
  }

}}