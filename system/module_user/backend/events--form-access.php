<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\access;
          use \effcore\message;
          use \effcore\page;
          use \effcore\text;
          abstract class events_form_access {

  static function on_init($form, $items) {
    $access = null;
    if (isset($form->entity_name)) {
      switch ($form->entity_name) {
        case 'page': $access = page::current_get()->access; break;
      }
    }
    $items['settings/roles']->values = access::roles_get();
    $items['settings/roles']->checked = $access->roles ?? [];
    $items['settings/roles']->build();
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'save':
        foreach ($items['*roles']->values_get() as $c_id) {
          message::insert(new text('Role %%_id was selected.', ['id' => $c_id]));
        }
        break;
    }
  }

}}