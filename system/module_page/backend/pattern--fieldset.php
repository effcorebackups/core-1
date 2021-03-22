<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class fieldset extends container {

  public $tag_name = 'fieldset';
  public $title_tag_name = 'label';
  public $title_attributes = ['data-fieldset-title' => true];
  public $content_tag_name = 'x-fieldset-content';
# ─────────────────────────────────────────────────────────────────────
  public $title_position = 'top'; # opener not working in 'bottom' mode
  public $state = ''; # '' | opened | closed[checked]
  public $number;

  function __construct($title = null, $description = null, $attributes = [], $children = [], $weight = 0) {
    parent::__construct(null, $title, $description, $attributes, $children, $weight);
  }

  function build() {
    if (!$this->is_builded) {
      if ($this->number === null)
          $this->number = static::current_number_generate();
      $this->is_builded = true;
    }
  }

  function render() {
    $this->build();
    return parent::render();
  }

  function render_self() {
    if ($this->title) {
      $opener = $this->render_opener();
      if ((bool)$this->title_is_visible === true && $opener !== '') return $opener.(new markup($this->title_tag_name, $this->title_attributes + ['for' => 'f_opener_'.$this->number                         ], $this->title))->render();
      if ((bool)$this->title_is_visible !== true && $opener !== '') return $opener.(new markup($this->title_tag_name, $this->title_attributes + ['for' => 'f_opener_'.$this->number, 'aria-hidden' => 'true'], $this->title))->render();
      if ((bool)$this->title_is_visible !== true && $opener === '') return         (new markup($this->title_tag_name, $this->title_attributes + [                                    'aria-hidden' => 'true'], $this->title))->render();
      if ((bool)$this->title_is_visible === true && $opener === '') return         (new markup($this->title_tag_name, $this->title_attributes + [                                                           ], $this->title))->render();
    }
  }

  function render_opener() {
    switch ($this->state) {
      case 'opened': $opener = new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'title', 'title' => new text('press to show or hide nested content'), 'name' => 'f_opener_'.$this->number, 'id' => 'f_opener_'.$this->number                   ]); break;
      case 'closed': $opener = new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'title', 'title' => new text('press to show or hide nested content'), 'name' => 'f_opener_'.$this->number, 'id' => 'f_opener_'.$this->number, 'checked' => true]); break;
      default      : $opener = null;
    }
    if ($opener && request::value_get('form_id') && request::value_get('f_opener_'.$this->number) === 'on') $opener->attribute_insert('checked', true);
    if ($opener && request::value_get('form_id') && request::value_get('f_opener_'.$this->number) !== 'on') $opener->attribute_delete('checked'      );
    if ($opener && $this->has_error_in_container()                                                        ) $opener->attribute_delete('checked'      );
    return $opener ?
           $opener->render() : '';
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $c_number = 0;

  static function current_number_generate() {
    return static::$c_number++;
  }

}}