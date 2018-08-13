<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class fieldset extends container {

  public $tag_name = 'fieldset';
  public $title_tag_name = 'label';
  public $content_wrapper_tag_name = 'x-content';
# ─────────────────────────────────────────────────────────────────────
  public $state = ''; # opened | closed[checked] | with_error
  public $number = 0;

  function __construct($title = null, $description = null, $attributes = [], $children = [], $weight = 0) {
    parent::__construct(null, $title, $description, $attributes, $children, $weight);
  }

  function build() {
    $this->number = static::cur_number_get();
  }

  function render_self() {
    if ($this->title) {
      $opener = $this->state == 'opened' ? new markup_simple('input', ['type' => 'checkbox', 'data-opener-type' => 'fieldset', 'title' => translation::get('Show or hide content'), 'name' => 'f_opener_'.$this->number, 'id' => 'f_opener_'.$this->number,                       ]) : (
                $this->state == 'closed' ? new markup_simple('input', ['type' => 'checkbox', 'data-opener-type' => 'fieldset', 'title' => translation::get('Show or hide content'), 'name' => 'f_opener_'.$this->number, 'id' => 'f_opener_'.$this->number, 'checked' => 'checked']) : null);
      if ($opener && field::request_value_get('form_id') && field::request_value_get('f_opener_'.$this->number) == 'on') $opener->attribute_insert('checked', 'checked');
      if ($opener && field::request_value_get('form_id') && field::request_value_get('f_opener_'.$this->number) != 'on') $opener->attribute_delete('checked');
      return $opener ? $opener->render().(new markup($this->title_tag_name,['for' => 'f_opener_'.$this->number], [$this->title]))->render() :
                                         (new markup($this->title_tag_name,[                                  ], [$this->title]))->render();
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $c_number = 0;

  static function cur_number_get() {
    return static::$c_number++;
  }

  static function validate($fieldset, $form, $npath) {
    if (($fieldset->state == 'opened'  ||
         $fieldset->state == 'closed') &&
         $fieldset->group_errors_count_get() != 0) {
      $fieldset->state = 'with_error';
    }
  }

}}