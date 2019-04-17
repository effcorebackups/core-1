<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class fieldset extends container {

  public $tag_name = 'fieldset';
  public $title_tag_name = 'label';
  public $content_tag_name = 'x-fieldset-content';
# ─────────────────────────────────────────────────────────────────────
  public $state = ''; # '' | opened | closed[checked]
  public $number = 0;

  function __construct($title = null, $description = null, $attributes = [], $children = [], $weight = 0) {
    parent::__construct(null, $title, $description, $attributes, $children, $weight);
  }

  function build() {
    $this->number = static::get_cur_number();
  }

  function render_self() {
    if ($this->title) {
      $opener = $this->render_opener();
      return $opener ? $opener.(new markup($this->title_tag_name, ['for' => 'f_opener_'.$this->number], [$this->title]))->render() :
                               (new markup($this->title_tag_name, [                                  ], [$this->title]))->render();
    }
  }

  function render_opener() {
    switch ($this->state) {
      case 'opened': $opener = new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'fieldset', 'title' => new text('Press to show or hide nested content'), 'name' => 'f_opener_'.$this->number, 'id' => 'f_opener_'.$this->number                   ]); break;
      case 'closed': $opener = new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'fieldset', 'title' => new text('Press to show or hide nested content'), 'name' => 'f_opener_'.$this->number, 'id' => 'f_opener_'.$this->number, 'checked' => true]); break;
      default      : $opener = null;
    }
    if ($opener && $this->cform && $this->cform->attribute_select('id') == field::request_value_get('form_id') && field::request_value_get('f_opener_'.$this->number) == 'on') $opener->attribute_insert('checked', true);
    if ($opener && $this->cform && $this->cform->attribute_select('id') == field::request_value_get('form_id') && field::request_value_get('f_opener_'.$this->number) != 'on') $opener->attribute_delete('checked'      );
    if ($opener && $this->has_error()                                                                                                                                        ) $opener->attribute_delete('checked'      );
    return $opener ?
           $opener->render() : '';
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $c_number = 0;

  static function get_cur_number() {
    return static::$c_number++;
  }

}}