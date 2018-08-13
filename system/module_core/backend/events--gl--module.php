<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class events_module {

  static function on_start() {}

  static function on_install($module_id = null) {
    if ($module_id) {
    # install entities
      foreach (entity::all_by_module_get($module_id) as $c_entity) {
        if ($c_entity->install())
             message::insert(translation::get('Entity %%_name was installed.',     ['name' => $c_entity->name_get()]));
        else message::insert(translation::get('Entity %%_name was not installed!', ['name' => $c_entity->name_get()]), 'error');
      }
    # insert instances
      foreach (instance::all_by_module_get($module_id) as $c_instance) {
        if ($c_instance->insert())
             message::insert(translation::get('Instances of entity %%_name was added.',     ['name' => $c_entity->name_get()]));
        else message::insert(translation::get('Instances of entity %%_name was not added!', ['name' => $c_entity->name_get()]), 'error');
      }
    }
  }

}}