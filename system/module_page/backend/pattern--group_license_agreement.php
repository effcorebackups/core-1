<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class group_license_agreement extends fieldset {

  public $title = 'License agreement';
  public $attributes = ['data-type' => 'license_agreement'];
  public $agree_title = 'I accept the terms of the license agreement.';

  function build() {
    if (!$this->is_builded) {
      parent::build();
      $language = language::get(language::code_get_current());
      $license_file = new file($language->license_path ?
                    dir_system.$language->license_path : dir_root.'license.md');
      $markup_license = new markup('x-document', ['data-type' => 'license'], markdown::markdown_to_markup($license_file->load()));
      $switcher_agree = new field_switcher($this->agree_title);
      $switcher_agree->build();
      $switcher_agree->name_set('is_agree');
      $switcher_agree->required_set(true);
      $this->child_insert($markup_license, 'license' );
      $this->child_insert($switcher_agree, 'is_agree');
      $this->is_builded = true;
    }
  }

}}