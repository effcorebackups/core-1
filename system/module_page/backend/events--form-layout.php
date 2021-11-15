<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\area;
          use \effcore\core;
          use \effcore\layout;
          use \effcore\message;
          use \effcore\page;
          abstract class events_form_layout {

  static function on_init($event, $form, $items) {
    $id = page::get_current()->args_get('id');
    if (layout::select($id)) {
      $layout = core::deep_clone(layout::select($id));
      foreach ($layout->children_select_recursive() as $c_child)
        if ($c_child instanceof area)
            $c_child->managing_enable();
      $form->child_select('layout_manager')->child_insert($layout);
    }
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'save':
        message::insert('Changes was saved.');
        break;
    }
  }

}}