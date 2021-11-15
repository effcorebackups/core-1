<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_license_agreement extends control {

  public $tag_name = 'x-widget';
  public $attributes = ['data-type' => 'license_agreement'];
  public $main_title = 'License agreement';
  public $text_agree = 'I accept the terms of the license agreement.';

  function build() {
    if (!$this->is_builded) {
      $wrapper = new fieldset($this->title);
      $wrapper->title = $this->main_title;
      $wrapper->state = 'closed';
      $this->child_insert($wrapper, 'wrapper');
    # text of license agreement
      $language = language::get(language::code_get_current());
      $license_file = new file($language->license_path ?: dir_root.'license.md');
      $license_markup = new markup('x-document', ['data-style' => 'license'], markdown::markdown_to_markup($license_file->load()));
      $wrapper->child_insert($license_markup, 'license');
    # switcher 'agree to license agreement'
      $switcher_agree = new field_switcher($this->text_agree);
      $switcher_agree->build();
      $switcher_agree->name_set('is_agree');
      $switcher_agree->required_set(true);
      $wrapper->child_insert($switcher_agree, 'is_agree');
      $this->is_builded = true;
    }
  }

}}