<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\entity;
          abstract class events_form_access {

  static function on_init($form, $items) {
    $entity = entity::get('role');
    $instances = $entity->instances_select();
    foreach ($instances as $c_instance) {
      // print_R( $c_instance );
    }
  }

}}