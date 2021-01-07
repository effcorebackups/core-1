<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\message;
          abstract class events_form_seo_sitemap {

  static function on_init($event, $form, $items) {
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'save':
        message::insert('The changes was saved.');
        break;
    }
  }

}}