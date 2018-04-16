<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class group_license_agreement extends container {

  public $tag_name = 'fieldset';
  public $title = 'License agreement';
  public $title_tag_name = 'legend';
  public $title_for_agree = 'I accept the terms of the license agreement';

  function build() {
    $lang_code = language::get_current();
    $license_file = new file(dir_root.'license'.($lang_code === 'en' ? '' : '-'.$lang_code).'.md');
    $markup_license = new markup('x-license', [], markdown::markdown_to_markup($license_file->load()));
    $markup_agree = new group_checkboxes(['is_agree' => $this->title_for_agree], ['is_agree' => 'is_agree']);
    $markup_agree->attribute_insert('name', 'is_agree', 'element_attributes');
    $markup_agree->build();
    $this->child_insert($markup_license, 'license');
    $this->child_insert($markup_agree, 'is_agree');
  }

}}