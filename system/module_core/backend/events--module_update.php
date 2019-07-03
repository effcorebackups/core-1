<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\entity;
          use \effcore\message;
          use \effcore\text;
          abstract class events_module_update {

  static function on_update_1000($update) {
    $entity = entity::get('message');
    if ($entity->install())
         {message::insert(new text('Entity "%%_name" was installed.',     ['name' => $entity->name])         ); return true; }
    else {message::insert(new text('Entity "%%_name" was not installed!', ['name' => $entity->name]), 'error'); return false;}
  }

}}