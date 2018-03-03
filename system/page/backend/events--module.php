<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\page;
          use \effcore\entity;
          use \effcore\message;
          use \effcore\translation;
          abstract class events_module extends \effcore\events_module {

  static function on_start() {
    return page::find_and_render();
  }

  static function on_install() {
  # install entities
    foreach (entity::get_all_by_module('page') as $c_entity) {
      if ($c_entity->install()) message::insert(translation::get('Entity %%_name was installed.',     ['name' => $c_entity->get_name()]));
      else                      message::insert(translation::get('Entity %%_name was not installed!', ['name' => $c_entity->get_name()]), 'error');
    }
  }

}}