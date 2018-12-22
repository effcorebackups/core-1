<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\markup;
          use \effcore\text;
          abstract class events_form_modules_uninstall {

  static function on_init($form, $items) {
    $info = $form->child_select('info');
    $info->child_insert(
      new markup('div', [], new text('UNDER CONSTRUCTION'))
    );
  }

  static function on_submit($form, $items) {
  }

}}