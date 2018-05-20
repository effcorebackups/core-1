<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

  # html5 elements and attributes support:
  # ─────────────────────────────────────────────────────────────────────
  #   x  attribute | d | r | r | m | m | m | m | s | m | c | p | v |
  #    x           | i | e | e | i | a | i | a | t | u | h | a | a |
  #     x          | s | a | q | n | x | n | x | e | l | e | t | l |
  #      x         | a | d | u | l | l |   |   | p | t | c | t | u |
  #       x        | b | o | i | e | e |   |   |   | i | k | e | e |
  #        x       | l | n | r | n | n |   |   |   | p | e | r |   |
  #         x      | e | l | e | g | g |   |   |   | l | d | n | [ |
  #          x     | d | y | d | t | t |   |   |   | e |   |   | ] |
  # element   x    |   |   |   | h | h |   |   |   |   |   |   |   |
  # ─────────────────────────────────────────────────────────────────────
  # input:text     | + | + | + | x | + |   |   |   |   |   | + | ? |
  # input:color    | + | x | x | x | x |   |   |   |   |   | x | ? |
  # input:email    | + | + | + | x | + |   |   |   | + |   | + | ? |
  # input:file     | + |   | + |   |   |   |   |   | + |   |   | ? |
  # input:password | + | + | + | x | + |   |   |   |   |   | + | ? |
  # input:search   | + | + | + | x | + |   |   |   |   |   | + | ? |
  # input:tel      | + | + | + | x | + |   |   |   |   |   | + | ? |
  # input:url      | + | + | + | x | + |   |   |   |   |   | + | ? |
  # input:date     | + | + | + | x | x | + | + | - |   |   | x | ? |
  # input:time     | + | + | + | x | x | + | + | - |   |   | x | ? |
  # input:number   | + | + | + | x | x | + | + | + |   |   | x | ? |
  # input:range    | + | x | x | x | x | + | + | + |   |   | x | ? |
  # textarea       | + | + | + | x | + |   |   |   |   |   | x | ? |
  # select         | + |   | + |   |   |   |   |   | + |   |   | ? |
  # select:option  | ? |   |   |   |   |   |   |   |   |   |   |   |
  # input:checkbox | + |   | + |   |   |   |   |   |   | + |   | ? |
  # input:radio    | + |   | + |   |   |   |   |   |   | + |   | ? |
  # ─────────────────────────────────────────────────────────────────────
  # note: x - extended feature of the system
  # ─────────────────────────────────────────────────────────────────────
  # input:hidden         | protected from change the value from user side
  # input:button         | not processed - use button:button instead
  # input:reset          | not processed - use button:reset instead
  # input:submit         | not processed - use button:submit instead
  # input:image          | not processed - use imgage instead
  # input:datetime       | not processed - use date + time instead
  # input:datetime-local | not processed - use date + time instead
  # input:week           | not processed
  # input:month          | not processed
  # ─────────────────────────────────────────────────────────────────────

namespace effcore {
          class field extends container {

  public $tag_name = 'x-field';
  public $title_tag_name = 'label';
# ─────────────────────────────────────────────────────────────────────
  public $element_tag_name = 'input';
  public $element_class = '\\effcore\\markup_simple';
  public $element_attributes_default = [];
  public $element_attributes = [];
# ─────────────────────────────────────────────────────────────────────
  protected $_form;
  protected $_path;

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

  function get_form() {return $this->_form;}
  function get_path() {return $this->_path;}
  function set_form($form) {$this->_form = $form;}
  function set_path($path) {$this->_path = $path;}

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
    if ($element instanceof node_simple && $element->attribute_select('pattern') !== null)   $return[] = new markup('p', ['class' => ['pattern' => 'pattern']],     translation::get('Field value should match the regular expression %%_expression.', ['expression' => $element->attribute_select('pattern')]));
    if ($element instanceof node_simple && $element->attribute_select('minlength') !== null) $return[] = new markup('p', ['class' => ['minlength' => 'minlength']], translation::get('Field must contain a minimum of %%_num characters.', ['num' => $element->attribute_select('minlength')]));
    if ($element instanceof node_simple && $element->attribute_select('maxlength') !== null) $return[] = new markup('p', ['class' => ['maxlength' => 'maxlength']], translation::get('Field must contain a maximum of %%_num characters.', ['num' => $element->attribute_select('maxlength')]));
    if ($element instanceof node_simple && $element->attribute_select('min') !== null)       $return[] = new markup('p', ['class' => ['min' => 'min']],             translation::get('Minimal field value: %%_value.', ['value' => $element->attribute_select('min')]));
    if ($element instanceof node_simple && $element->attribute_select('max') !== null)       $return[] = new markup('p', ['class' => ['max' => 'max']],             translation::get('Maximal field value: %%_value.', ['value' => $element->attribute_select('max')]));
    if ($element instanceof node_simple && $element->attribute_select('type') == 'range')    $return[] = new markup('p', ['class' => ['cur' => 'cur']],             translation::get('Current field value: %%_value.', ['value' => (new markup('x-value', [], $element->attribute_select('value')))->render()]));
    if ($this->description)                                                                  $return[] = new markup('p', [], $this->description);
    if (count($return)) {
      return (new markup($this->description_tag_name, [], $return))->render();
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

  # conversion matrix (expected from $_POST - undefined|string|array):
  # ─────────────────────────────────────────────────────────────────────
  # - $_POST[field] == undefined           -> return ''
  # - $_POST[field] == ''                  -> return ''
  # - $_POST[field] == 'value'             -> return 'value'
  # ─────────────────────────────────────────────────────────────────────
  # - $_POST[field] == [0 => '']           -> return ''
  # - $_POST[field] == [0 => '', ...]      -> return ''
  # - $_POST[field] == [0 => 'value']      -> return 'value'
  # - $_POST[field] == [0 => 'value', ...] -> return 'value'
  # ─────────────────────────────────────────────────────────────────────

  static function get_new_value($name, $index = 0) {
    return !isset($_POST[$name]) ? '' :
       (is_string($_POST[$name]) ? $_POST[$name] : 
        (is_array($_POST[$name]) &&
            isset($_POST[$name][$index]) ?
                  $_POST[$name][$index] : ''));
  }

  # conversion matrix (expected from $_POST - undefined|string|array):
  # ─────────────────────────────────────────────────────────────────────
  # - $_POST[field] == undefined           -> return []
  # - $_POST[field] == ''                  -> return [0 => '']
  # - $_POST[field] == 'value'             -> return [0 => 'value']
  # ─────────────────────────────────────────────────────────────────────
  # - $_POST[field] == [0 => '']           -> return [0 => '']
  # - $_POST[field] == [0 => '', ...]      -> return [0 => '', ...]
  # - $_POST[field] == [0 => 'value']      -> return [0 => 'value']
  # - $_POST[field] == [0 => 'value', ...] -> return [0 => 'value', ...]
  # ─────────────────────────────────────────────────────────────────────

  static function get_new_value_multiple($name) {
    return !isset($_POST[$name]) ? [] :
       (is_string($_POST[$name]) ? [$_POST[$name]] :
        (is_array($_POST[$name]) ?
                  $_POST[$name] : []));
  }

  # conversion matrix (expected: undefined|array):
  # ─────────────────────────────────────────────────────────────────────
  # - $_FILES[field] == undefined                             -> return []
  # - $_FILES[field] == [error = 4]                           -> return []
  # - $_FILES[field] == [name = 'file']                       -> return [0 => (object)[name = 'file']]
  # - $_FILES[field] == [name = [0 => 'file']]                -> return [0 => (object)[name = 'file']]
  # - $_FILES[field] == [name = [0 => 'file1', 1 => 'file2']] -> return [0 => (object)[name = 'file1'], 1 => (object)[name = 'file2']]
  # ─────────────────────────────────────────────────────────────────────

  static function get_new_files($name) {
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
                $c_file = new file($c_value);
                $c_file->sanitize_file();
                $return[$c_index]->{'name'} = $c_file->get_name();
                $return[$c_index]->{'type'} = $c_file->get_type();
                $return[$c_index]->{'file'} = $c_file->get_file();
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
    $name = $field->get_element_name();
    $type = $field->get_element_type();
    if ($name && $type && get_called_class() == 'effcore\\field') {
      switch ($type) {
        case 'input:checkbox': return field_checkbox   ::validate($field, $form, $npath);
        case 'input:color'   : return field_color      ::validate($field, $form, $npath);
        case 'input:date'    : return field_date       ::validate($field, $form, $npath);
        case 'input:email'   : return field_email      ::validate($field, $form, $npath);
        case 'input:file'    : return field_file       ::validate($field, $form, $npath);
        case 'input:number'  : return field_number     ::validate($field, $form, $npath);
        case 'input:password': return field_password   ::validate($field, $form, $npath);
        case 'input:radio'   : return field_radiobutton::validate($field, $form, $npath);
        case 'input:range'   : return field_range      ::validate($field, $form, $npath);
        case 'input:search'  : return field_search     ::validate($field, $form, $npath);
        case 'input:tel'     : return field_phone      ::validate($field, $form, $npath);
        case 'input:text'    : return field_text       ::validate($field, $form, $npath);
        case 'input:time'    : return field_time       ::validate($field, $form, $npath);
        case 'input:url'     : return field_url        ::validate($field, $form, $npath);
        case 'select'        : return field_select     ::validate($field, $form, $npath);
        case 'textarea'      : return field_textarea   ::validate($field, $form, $npath);
      }
    }
  }

}}