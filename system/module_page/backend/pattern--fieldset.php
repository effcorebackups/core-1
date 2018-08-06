<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class fieldset extends container {

  public $tag_name = 'fieldset';
  public $title_tag_name = 'label';
  public $content_wrapper_tag_name = 'x-content';
# ─────────────────────────────────────────────────────────────────────
  public $state = ''; # opened | closed[checked]
  static public $c_index = 0;

  function __construct($title = null, $description = null, $attributes = [], $children = [], $weight = 0) {
    parent::__construct(null, $title, $description, $attributes, $children, $weight);
  }

  function render_self() {
    if ($this->title) {
      $opener = $this->state == 'opened' ? new markup_simple('input', ['type' => 'checkbox', 'data-opener-type' => 'fieldset', 'title' => translation::get('Show or hide content'), 'name' => 'f_opener_'.(++static::$c_index), 'id' => 'f_opener_'.static::$c_index,                       ]) : (
                $this->state == 'closed' ? new markup_simple('input', ['type' => 'checkbox', 'data-opener-type' => 'fieldset', 'title' => translation::get('Show or hide content'), 'name' => 'f_opener_'.(++static::$c_index), 'id' => 'f_opener_'.static::$c_index, 'checked' => 'checked']) : null);
      if ($opener && field::request_value_get('form_id') && field::request_value_get('f_opener_'.static::$c_index) == 'on') $opener->attribute_insert('checked', 'checked');
      if ($opener && field::request_value_get('form_id') && field::request_value_get('f_opener_'.static::$c_index) != 'on') $opener->attribute_delete('checked');
      return $opener ? $opener->render().(new markup($this->title_tag_name,['for' => 'f_opener_'.static::$c_index], [$this->title]))->render() :
                                         (new markup($this->title_tag_name,[                                     ], [$this->title]))->render();
    }
  }

  # ─────────────────────────────────────────────────────────────────────
  # functionality for errors
  # ─────────────────────────────────────────────────────────────────────

  function group_errors_count_get() {
    $return = 0;
    foreach ($this->group_errors_get() as $c_errors) {
      $return += count($c_errors);
    }
    return $return;
  }

  function group_errors_get() {
    $return = [];
    foreach ($this->children_select_recursive() as $c_npath => $c_item) {
      $c_full_npath = $this->npath.'/'.$c_npath;
      if (isset(field::$errors[$c_full_npath])) {
        $return[$c_full_npath] = field::$errors[$c_full_npath];
      }
    }
    return $return;
  }

  ###########################
  ### static declarations ###
  ###########################

  static function validate($fieldset, $form, $npath) {
  }

}}