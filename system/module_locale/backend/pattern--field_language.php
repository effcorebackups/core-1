<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_language extends field_select {

  public $title = 'Language';
  public $title__not_selected = '- select -';
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
      $languages = ['en' => $languages['en']] + $languages;
      $this->option_insert($this->title__not_selected, 'not_selected');
      foreach ($languages as $c_code => $c_info) {
        $this->option_insert(new text_simple(
          $c_code !== 'en' ? $c_info->title_en.' / '.$c_info->title_native.' ('.$c_code.')' :
                             $c_info->title_en.                            ' ('.$c_code.')'), $c_code); }
      $this->is_builded = true;
    }
  }

}}