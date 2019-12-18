<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_poll_fields extends container {

  public $tag_name = 'x-widget';
  public $attributes = ['data-type' => 'poll_fields'];

  function __construct($attributes = [], $weight = 0) {
    parent::__construct(null, null, null, $attributes, [], $weight);
  }

  function build() {
    if (!$this->is_builded) {
      $widgets_manage_group = new markup('x-widgets-group', ['data-type' => 'manage', 'data-has-rearrangeable' => 'true']);
      for ($i = 0; $i < 10; $i++) {
      # field for answer text
        $c_field_answer_text = new field_text('Text');
        $c_field_answer_text->description_state = 'hidden';
        $c_field_answer_text->build();
        $c_field_answer_text->name_set('answer_text_'.$i);
        $c_field_answer_text->required_set($i == 0 || $i == 1);
      # make final wrapper
        $c_widget_insert = new markup('x-widget', ['data-rearrangeable' => 'true', 'data-fields-is-inline' => 'true']);
        $c_widget_insert     ->child_insert($c_field_answer_text, 'answer_text'  );
        $widgets_manage_group->child_insert($c_widget_insert, 'widget_insert_'.$i);}
      $this->child_insert($widgets_manage_group, 'widgets_manage_group');
      $this->is_builded = true;
    }
  }

  function items_get() {
  }

  function items_set($items) {
  }

  function on_click_insert($group, $form, $npath, $value) {
  }

  function on_click_delete($group, $form, $npath) {
  }

}}