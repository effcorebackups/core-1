<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class group_license_agreement extends fieldset {

  public $title = 'License agreement';
  public $attributes = ['data-type' => 'license_agreement'];
  public $agree_title = 'I accept the terms of the license agreement';

  function build() {
    parent::build();
    $language = language::get(language::code_get_current());
    $license = new file($language->license_translation ?
             dir_system.$language->license_translation : dir_root.'license.md');
    $markup_license = new markup('x-document', ['class' => ['license' => 'license']], markdown::markdown_to_markup($license->load()));
    $switcher_agree = new field_switcher($this->agree_title);
    $switcher_agree->build();
    $switcher_agree->name_set ('is_agree');
    $switcher_agree->value_set('is_agree');
    $switcher_agree->required_set(true);
    $this->child_insert($markup_license, 'license' );
    $this->child_insert($switcher_agree, 'is_agree');
  }

}}