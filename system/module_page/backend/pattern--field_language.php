<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_language extends field_select {

  public $title = 'Language';
  public $attributes = ['data-type' => 'language'];
  public $element_attributes = [
    'name'     => 'lang_code',
    'required' => true
  ];

  function build() {
    if (!$this->is_builded) {
      parent::build();
      $languages = language::get_all();
      core::array_sort_by_text_property($languages, 'title_en', 'd', false);
      $this->option_insert('- no -', 'not_selected');
      foreach ($languages as $c_code => $c_info) {
        $this->option_insert(
          $c_info->title_en.($c_code != 'en' ? ' ('.
          $c_info->title_native.')' : ''), $c_code);}
      $this->is_builded = true;
    }
  }

}}