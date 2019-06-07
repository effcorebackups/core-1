<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\layout;
          use \effcore\message;
          use \effcore\page;
          abstract class events_form_layouts {

  static function on_init($form, $items) {
    $id = page::get_current()->args_get('id');
    if ($id) {
      $layout = layout::select($id);
      $items['modeling']->child_insert($layout, 'layout');
    }
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'save':
        message::insert('The changes was saved.');
        break;
    }
  }

}}