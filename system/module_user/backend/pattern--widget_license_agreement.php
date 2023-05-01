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
      $this->child_insert(static::widget_manage_get($this), 'manage');
      $this->is_builded = true;
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static function widget_manage_get($widget) {
    $result = new fieldset($widget->title);
    $result->title = $widget->main_title;
    $result->state = 'closed';
  # text of license agreement
    $language = language::get(language::code_get_current());
    $license_file = new file($language->license_path ?: dir_root.'license.md');
    $license_markup = new markup('x-document', ['data-style' => 'license'], markdown::markdown_to_markup($license_file->load()));
  # switcher 'agree to license agreement'
    $field_switcher_is_agree = new field_switcher($widget->text_agree);
    $field_switcher_is_agree->build();
    $field_switcher_is_agree->name_set('is_agree');
    $field_switcher_is_agree->required_set(true);
  # relate new controls with the widget
    $widget->controls['#is_agree'] = $field_switcher_is_agree;
    $result->child_insert($license_markup,          'license_markup');
    $result->child_insert($field_switcher_is_agree, 'field_switcher_is_agree');
    return $result;
  }

}}