<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field extends container {

  public $tag_name = 'x-field';
  public $title_tag_name = 'label';
  public $element_attributes = [];

  function build() {
    $element = $this->child_select('element');
    if ($element) {
      foreach ($this->attribute_select_all('element_attributes') as $c_name => $c_value) {
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

}}