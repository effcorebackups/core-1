<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\page;
          use \effcore\url;
          abstract class events_form_instance_insert {

  static function on_init($form, $items) {
  }

  static function on_submit($form, $items) {
    $base        = page::current_get()->args_get('base');
    $entity_name = page::current_get()->args_get('entity_name');
    switch ($form->clicked_button->value_get()) {
      case 'insert':
        break;
      case 'cancel':
        url::go(url::back_url_get() ?: $base.'/select/'.$entity_name);
        break;
    }
  }

}}