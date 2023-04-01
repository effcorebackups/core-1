<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

  # html5 elements and attributes support:
  # ┌──────────────────────╥───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┐
  # │      ╲     attribute ║ d │ r │ r │ m │ m │ m │ m │ s │ m │ c │ p │ v │
  # │       ╲              ║ i │ e │ e │ i │ a │ i │ a │ t │ u │ h │ a │ a │
  # │        ╲             ║ s │ a │ q │ n │ x │ n │ x │ e │ l │ e │ t │ l │
  # │         ╲            ║ a │ d │ u │ l │ l │   │   │ p │ t │ c │ t │ u │
  # │          ╲           ║ b │ o │ i │ e │ e │   │   │   │ i │ k │ e │ e │
  # │           ╲          ║ l │ n │ r │ n │ n │   │   │   │ p │ e │ r │   │
  # │            ╲         ║ e │ l │ e │ g │ g │   │   │   │ l │ d │ n │ [ │
  # │             ╲        ║ d │ y │ d │ t │ t │   │   │   │ e │   │   │ ] │
  # │ element      ╲       ║   │   │   │ h │ h │   │   │   │   │   │   │   │
  # ╞══════════════════════╬═══╪═══╪═══╪═══╪═══╪═══╪═══╪═══╪═══╪═══╪═══╪═══╡
  # │ input:text           ║ + │ + │ + │ x │ + │   │   │   │   │   │ + │ + │
  # │ input:color          ║ + │ x │ x │ x │ x │   │   │   │   │   │ x │ + │
  # │ input:email          ║ + │ + │ + │ x │ + │   │   │   │ + │   │ + │ + │
  # │ input:file           ║ + │   │   │   │   │   │   │   │ + │   │   │ + │
  # │ input:password       ║ + │ + │ + │ x │ + │   │   │   │   │   │ + │ + │
  # │ input:search         ║ + │ + │ + │ x │ + │   │   │   │   │   │ + │ + │
  # │ input:tel            ║ + │ + │ + │ x │ + │   │   │   │   │   │ + │ + │
  # │ input:url            ║ + │ + │ + │ x │ + │   │   │   │   │   │ + │ + │
  # │ input:time           ║ + │ + │ + │ x │ x │ + │ + │ - │   │   │ x │ + │
  # │ input:date           ║ + │ + │ + │ x │ x │ + │ + │ - │   │   │ x │ + │
  # │ input:datetime-local ║ + │ + │ + │ x │ x │ + │ + │ - │   │   │ x │ + │
  # │ input:number         ║ + │ + │ + │ x │ x │ + │ + │ + │   │   │ x │ + │
  # │ input:range          ║ + │ x │ x │ x │ x │ + │ + │ + │   │   │ x │ + │
  # │ textarea             ║ + │ + │ + │ x │ + │   │   │   │   │   │ x │ + │
  # │ select               ║ + │   │ + │   │   │   │   │   │ + │   │   │ + │
  # │ select:option        ║ + │   │   │   │   │   │   │   │   │   │   │   │
  # │ input:checkbox       ║ + │   │ + │   │   │   │   │   │   │ + │   │ + │
  # │ input:radio          ║ + │   │ + │   │   │   │   │   │   │ + │   │ + │
  # │ button:submit        ║ + │   │   │   │   │   │   │   │   │   │   │   │
  # └──────────────────────╨───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┘
  # note: x - is extended feature of the system
  # ┌──────────────────────╥────────────────────────────────────────────────┐
  # │ input:hidden         ║ protected from change the value from user side │
  # │ input:button         ║ not processed - use button:button instead      │
  # │ input:reset          ║ not processed - use button:reset instead       │
  # │ input:submit         ║ not processed - use button:submit instead      │
  # │ input:image          ║ not processed - use image instead              │
  # │ input:datetime       ║ not processed - use datetime-local instead     │
  # │ input:week           ║ not processed                                  │
  # │ input:month          ║ not processed                                  │
  # └──────────────────────╨────────────────────────────────────────────────┘

namespace effcore {
          class field extends control {

  public $tag_name = 'x-field';
  public $title_tag_name = 'label';
  public $title_attributes = ['data-field-title' => true];
  public $name_prefix = null; # unused inherited property
# ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
  public $element_class = '\\effcore\\markup_simple';
  public $element_tag_name = 'input';
  public $element_attributes = [];
  public $description_state = 'closed'; # opened | closed[checked] | hidden
  public $set_auto_id = true;
  public $has_error = false;

  function __construct($title = null, $description = null, $attributes = [], $weight = 0) {
    parent::__construct(null, $title, $description, $attributes, [], $weight);
  }

  function build() {
    if (!$this->is_builded) {
      $element = new $this->element_class($this->element_tag_name);
      $this->child_insert($element, 'element');
      foreach ($this->attributes_select('element_attributes') as $c_key => $c_value) {
        if ($c_value === null) $element->attribute_delete($c_key);
        if ($c_value !== null) $element->attribute_insert($c_key, $c_value); }
      $this->is_builded = true;
    }
  }

  # ─────────────────────────────────────────────────────────────────────
  # element properties
  # ─────────────────────────────────────────────────────────────────────

  # supporting of markup styles:
  # ┌──────────────────────────────╥──────────╥─────────┐
  # │ style                        ║ is valid ║ support │
  # ╞══════════════════════════════╬══════════╬═════════╡
  # │ <x-tag disabled/>            ║ yes      ║ yes     │
  # │ <x-tag disabled=""/>         ║ yes      ║ no      │
  # │ <x-tag disabled="disabled"/> ║ yes      ║ no      │
  # │ <x-tag disabled="true"/>     ║ no       ║ no      │
  # │ <x-tag disabled="false"/>    ║ no       ║ no      │
  # └──────────────────────────────╨──────────╨─────────┘

  function auto_id_generate() {
    $name = $this->name_get();
    if ($name !== null) {
      static::$auto_ids[$name] = isset(static::$auto_ids[$name]) ? ++static::$auto_ids[$name] : 1;
      if (static::$auto_ids[$name] === 1)
           return 'auto_id-'.$name;
      else return 'auto_id-'.$name.'-'.static::$auto_ids[$name];
    }
  }

  function auto_id_set() {
    $name = $this->name_get();
    if ($this->id_get() === null && $name !== null) {
      $this->id_set(
        $this->auto_id_generate()
      );
    }
  }

  function accept_get() {
    $element = $this->child_select('element');
    return $element->attribute_select('accept');
  }

  function accept_set($accept = null) {
    $element = $this->child_select('element');
    if ($accept !== null) $element->attribute_insert('accept', $accept);
    else                  $element->attribute_delete('accept');
  }

  function autofocus_set($is_focused = true) {
    $element = $this->child_select('element');
    if ($is_focused) $element->attribute_insert('autofocus', true);
    else             $element->attribute_delete('autofocus');
  }

  function checked_get() {
    $element = $this->child_select('element');
    return $element->attribute_select('checked') === true;
  }

  function checked_set($is_checked = true) {
    $element = $this->child_select('element');
    if ($is_checked) $element->attribute_insert('checked', true);
    else             $element->attribute_delete('checked');
  }

  function disabled_get() {
    $element = $this->child_select('element');
    return $element->attribute_select('disabled') === true;
  }

  function disabled_set($is_disabled = true) {
    $element = $this->child_select('element');
    if ($is_disabled) $element->attribute_insert('disabled', true);
    else              $element->attribute_delete('disabled');
  }

  function id_get() {
    $element = $this->child_select('element');
    return $element->attribute_select('id');
  }

  function id_set($id = null) {
    $element = $this->child_select('element');
    if ($id !== null) $element->attribute_insert('id', $id);
    else              $element->attribute_delete('id');
  }

  function invalid_set($is_invalid = true) {
    $element = $this->child_select('element');
    if ($is_invalid) $element->attribute_insert('aria-invalid', 'true');
    else             $element->attribute_delete('aria-invalid');
  }

  function min_get() {
    $element = $this->child_select('element');
    return $element->attribute_select('min');
  }

  function min_set($min = null) {
    $element = $this->child_select('element');
    if ($min !== null) $element->attribute_insert('min', $min);
    else               $element->attribute_delete('min');
  }

  function max_get() {
    $element = $this->child_select('element');
    return $element->attribute_select('max');
  }

  function max_set($max = null) {
    $element = $this->child_select('element');
    if ($max !== null) $element->attribute_insert('max', $max);
    else               $element->attribute_delete('max');
  }

  function minlength_get() {
    $element = $this->child_select('element');
    return $element->attribute_select('minlength');
  }

  function minlength_set($minlength = null) {
    $element = $this->child_select('element');
    if ($minlength !== null) $element->attribute_insert('minlength', $minlength);
    else                     $element->attribute_delete('minlength');
  }

  function maxlength_get() {
    $element = $this->child_select('element');
    return $element->attribute_select('maxlength');
  }

  function maxlength_set($maxlength = null) {
    $element = $this->child_select('element');
    if ($maxlength !== null) $element->attribute_insert('maxlength', $maxlength);
    else                     $element->attribute_delete('maxlength');
  }

  function multiple_get() {
    $element = $this->child_select('element');
    return $element->attribute_select('multiple') === true;
  }

  function multiple_set($is_multiple = true) {
    $element = $this->child_select('element');
    if ($is_multiple) $element->attribute_insert('multiple', true);
    else              $element->attribute_delete('multiple');
  }

  function name_get($trim = true) {
    $element = $this->child_select('element');
    return $trim ? rtrim($element->attribute_select('name'), '[]') :
                         $element->attribute_select('name');
  }

  function name_set($name) {
    $element = $this->child_select('element');
    $element->attribute_insert('name', $name);
  }

  function pattern_get() {
    $element = $this->child_select('element');
    return $element->attribute_select('pattern');
  }

  function pattern_set($pattern = null) {
    $element = $this->child_select('element');
    if ($pattern !== null) $element->attribute_insert('pattern', $pattern);
    else                   $element->attribute_delete('pattern');
  }

  function readonly_get() {
    $element = $this->child_select('element');
    return $element->attribute_select('readonly') === true;
  }

  function readonly_set($is_readonly = true) {
    $element = $this->child_select('element');
    if ($is_readonly) $element->attribute_insert('readonly', true);
    else              $element->attribute_delete('readonly');
  }

  function required_get() {
    $element = $this->child_select('element');
    return $element->attribute_select('required') === true;
  }

  function required_set($is_required = true) {
    $element = $this->child_select('element');
    if ($is_required) $element->attribute_insert('required', true);
    else              $element->attribute_delete('required');
  }

  function size_get() {
    $element = $this->child_select('element');
    return $element->attribute_select('size');
  }

  function size_set($size = null) {
    $element = $this->child_select('element');
    if ($size !== null) $element->attribute_insert('size', $size);
    else                $element->attribute_delete('size');
  }

  function step_get() {
    $element = $this->child_select('element');
    return $element->attribute_select('step');
  }

  function step_set($step = null) {
    $element = $this->child_select('element');
    if ($step !== null) $element->attribute_insert('step', $step);
    else                $element->attribute_delete('step');
  }

  function type_get($full = true) {
    $element = $this->child_select('element');
    switch ($element->tag_name) {
      case 'input'   : return 'input'.($full ? ':'.$element->attribute_select('type') : '');
      case 'textarea': return 'textarea';
      case 'select'  : return 'select';
    }
  }

  function value_get() { # return: null | string | __OTHER_TYPE__ (when "value" in *.data is another type)
    $element = $this->child_select('element');
    return $element->attribute_select('value');
  }

  function value_set($value) {
    $this->value_set_initial($value);
    $element = $this->child_select('element');
    if (is_null   ($value)) return $element->attribute_insert('value', null);
    if (is_int    ($value)) return $element->attribute_insert('value', core::format_number($value));
    if (is_float  ($value)) return $element->attribute_insert('value', core::format_number($value, core::fpart_max_len));
    if (is_string ($value)) return $element->attribute_insert('value', $value);
  }

  # ─────────────────────────────────────────────────────────────────────
  # functionality for errors
  # ─────────────────────────────────────────────────────────────────────

  function has_error() {
    return $this->has_error;
  }

  function error_set($message = null, $args = []) {
    if ($this->disabled_get() === false &&
        $this->readonly_get() === false) {
      $new_error = new \stdClass;
      $new_error->message = $message;
      $new_error->args    = $args;
      $new_error->pointer = &$this;
      form::$errors[] = $new_error;
      if (!$this->has_error) {
           $this->has_error = true;
        $this->invalid_set(true);
        if (++static::$error_tabindex === 1) {
          $this->autofocus_set(true);
        }
      }
    }
  }

  # ─────────────────────────────────────────────────────────────────────
  # functionality for render
  # ─────────────────────────────────────────────────────────────────────

  function render() {
    $element = $this->child_select('element');
    if ($this->set_auto_id   ) $this->auto_id_set();
    if ($this->disabled_get()) $this->attribute_insert('aria-disabled', 'true');
    if ($this->required_get()) $this->attribute_insert('aria-required', 'true');
    return parent::render();
  }

  function render_self() {
    $element = $this->child_select('element');
    if ($this->title) {
      return (new markup($this->title_tag_name, $this->title_attributes + [
        'for'                => $this->id_get(),
        'data-mark-required' => $this->attribute_select('required') || ($element instanceof node_simple && $element->attribute_select('required')) ? true : null], $this->title
      ))->render();
    }
  }

  function render_opener() {
    return (new markup_simple('input', [
      'type'             => 'checkbox',
      'role'             => 'button',
      'data-opener-type' => 'description',
      'title'            => new text('press to show description')
    ]))->render();
  }

  function render_description() {
    $this->description = static::description_prepare($this->description);
    $element = $this->child_select('element');
    if ($element instanceof node_simple) {
      if (strlen($element->attribute_select('pattern'  ))                                                                                       ) $this->description['pattern'  ] = $this->render_description_pattern  ($element);
      if (strlen($element->attribute_select('min'      ))                                                                                       ) $this->description['min'      ] = $this->render_description_min      ($element);
      if (strlen($element->attribute_select('max'      ))                                                                                       ) $this->description['max'      ] = $this->render_description_max      ($element);
      if (strlen($element->attribute_select('value'    )) && $element->attribute_select('type'     ) === 'range'                                ) $this->description['cur'      ] = $this->render_description_cur      ($element);
      if (strlen($element->attribute_select('minlength')) && $element->attribute_select('minlength') !== $element->attribute_select('maxlength')) $this->description['minlength'] = $this->render_description_minlength($element);
      if (strlen($element->attribute_select('maxlength')) && $element->attribute_select('minlength') !== $element->attribute_select('maxlength')) $this->description['maxlength'] = $this->render_description_maxlength($element);
      if (strlen($element->attribute_select('minlength')) && $element->attribute_select('minlength') === $element->attribute_select('maxlength')) $this->description['midlength'] = $this->render_description_midlength($element);
    }
    if (count($this->description)) {
      if ($this->id_get() && $this->description_state !== 'hidden') $element->attribute_insert('aria-describedby', 'description-'.$this->id_get());
      if ($this->description_state === 'hidden'                      ) return '';
      if ($this->description_state === 'opened' || $this->has_error()) return                        (new markup($this->description_tag_name, ['id' => $this->id_get() ? 'description-'.$this->id_get() : null], $this->description))->render();
      if ($this->description_state === 'closed'                      ) return $this->render_opener().(new markup($this->description_tag_name, ['id' => $this->id_get() ? 'description-'.$this->id_get() : null], $this->description))->render();
      return '';
    }
  }

  function render_description_pattern  ($element) {return new markup('p', ['data-id' => 'pattern'  ], new text('Field value should match the regular expression: %%_expression',               ['expression' => $element->attribute_select('pattern'  )]));}
  function render_description_midlength($element) {return new markup('p', ['data-id' => 'midlength'], new text('Field value can contain only %%_number character%%_plural{number|s}.',         ['number'     => $element->attribute_select('minlength')]));}
  function render_description_minlength($element) {return new markup('p', ['data-id' => 'minlength'], new text('Field value can contain a minimum of %%_number character%%_plural{number|s}.', ['number'     => $element->attribute_select('minlength')]));}
  function render_description_maxlength($element) {return new markup('p', ['data-id' => 'maxlength'], new text('Field value can contain a maximum of %%_number character%%_plural{number|s}.', ['number'     => $element->attribute_select('maxlength')]));}
  function render_description_min      ($element) {return new markup('p', ['data-id' => 'min'      ], new text('Field value cannot be less than: %%_value',                                    ['value'      => $element->attribute_select('min'      )]));}
  function render_description_max      ($element) {return new markup('p', ['data-id' => 'max'      ], new text('Field value cannot be greater than: %%_value',                                 ['value'      => $element->attribute_select('max'      )]));}
  function render_description_cur      ($element) {return new markup('p', ['data-id' => 'cur'      ], new text('Field value at the current moment: %%_value',                                  ['value'      => (new markup('x-value', [], $element->attribute_select('value')))->render()]));}

  ###########################
  ### static declarations ###
  ###########################

  static protected $numbers = [];
  static protected $error_tabindex = 0;
  static protected $auto_ids = [];

  static function current_number_generate($name) {
    return !isset(static::$numbers[$name]) ?
                 (static::$numbers[$name] = 0) :
                ++static::$numbers[$name];
  }

  static function on_validate         ($field, $form, $npath) {} /*
  static function on_validate_phase_2 ($field, $form, $npath) {}
  static function on_validate_phase_3 ($field, $form, $npath) {}
  static function on_request_value_set($field, $form, $npath) {}
  static function on_submit           ($field, $form, $npath) {} */

}}