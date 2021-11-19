<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class fieldset extends container {

  public $tag_name = 'fieldset';
  public $title_tag_name = 'label';
  public $title_attributes = ['data-fieldset-title' => true];
  public $content_tag_name = 'x-fieldset-content';
  public $content_attributes = ['data-fieldset-content' => true, 'data-nested-content' => true];
# ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
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
      $html_name = 'f_opener_'.$this->number;
      $opener  = $this->render_opener();
      if ((bool)$this->title_is_visible === true && $opener !== '') return $opener.(new markup($this->title_tag_name, $this->title_attributes + ['for' => $html_name                         ], $this->title))->render();
      if ((bool)$this->title_is_visible !== true && $opener !== '') return $opener.(new markup($this->title_tag_name, $this->title_attributes + ['for' => $html_name, 'aria-hidden' => 'true'], $this->title))->render();
      if ((bool)$this->title_is_visible !== true && $opener === '') return         (new markup($this->title_tag_name, $this->title_attributes + [                     'aria-hidden' => 'true'], $this->title))->render();
      if ((bool)$this->title_is_visible === true && $opener === '') return         (new markup($this->title_tag_name, $this->title_attributes + [                                            ], $this->title))->render();
    }
  }

  function render_opener() {
    if ($this->state === 'opened' ||
        $this->state === 'closed') {
      $html_name    = 'f_opener_'.$this->number;
      $form_id      = request::value_get('form_id');
      $submit_value = request::value_get($html_name);
      $has_error    = $this->has_error_in();
      if ($form_id === '' && $this->state === 'opened'                    ) /*               default = opened */ return (new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'title', 'title' => new text('press to show or hide nested content'), 'name' => $html_name, 'id' => $html_name, 'checked' => null                          ]))->render();
      if ($form_id === '' && $this->state === 'closed'                    ) /*               default = closed */ return (new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'title', 'title' => new text('press to show or hide nested content'), 'name' => $html_name, 'id' => $html_name, 'checked' => true                          ]))->render();
      if ($form_id !== '' && $has_error !== true && $submit_value !== 'on') /* no error + no checked = opened */ return (new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'title', 'title' => new text('press to show or hide nested content'), 'name' => $html_name, 'id' => $html_name, 'checked' => null                          ]))->render();
      if ($form_id !== '' && $has_error !== true && $submit_value === 'on') /* no error +    checked = closed */ return (new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'title', 'title' => new text('press to show or hide nested content'), 'name' => $html_name, 'id' => $html_name, 'checked' => true                          ]))->render();
      if ($form_id !== '' && $has_error === true && $submit_value !== 'on') /*    error + no checked = opened */ return (new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'title', 'title' => new text('press to show or hide nested content'), 'name' => $html_name, 'id' => $html_name, 'checked' => null, 'aria-invalid' => 'true']))->render();
      if ($form_id !== '' && $has_error === true && $submit_value === 'on') /*    error +    checked = opened */ return (new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'title', 'title' => new text('press to show or hide nested content'), 'name' => $html_name, 'id' => $html_name, 'checked' => null, 'aria-invalid' => 'true']))->render();
    }
    return '';
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $c_number = 0;

  static function current_number_generate() {
    return static::$c_number++;
  }

}}