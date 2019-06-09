<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\area;
          use \effcore\core;
          use \effcore\layout;
          use \effcore\message;
          use \effcore\page;
          abstract class events_form_layouts {

  static function on_init($form, $items) {
    $id = page::get_current()->args_get('id');
    if ($id) {
      $layout = core::deep_clone(layout::select($id));
      foreach ($layout->children_select_recursive() as $c_child)
        if ($c_child instanceof area)
            $c_child->is_managed = true;
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