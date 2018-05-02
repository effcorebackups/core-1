<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class group_license_agreement extends container {

  public $tag_name = 'fieldset';
  public $title = 'License agreement';
  public $title_tag_name = 'legend';
  public $agree_title = 'I accept the terms of the license agreement';

  function build() {
    $lang_code = language::get_current();
    $license = new file(dir_root.'license'.($lang_code === 'en' ? '' : '-'.$lang_code).'.md');
    $markup_license = new markup('x-license', [], markdown::markdown_to_markup($license->load()));
    $markup_agree = new field_checkbox($this->agree_title);
    $markup_agree->element_attributes = ['name' => 'is_agree', 'value' => 'is_agree', 'required' => 'required'];
    $markup_agree->build();
    $this->child_insert($markup_license, 'license');
    $this->child_insert($markup_agree, 'is_agree');
  }

}}