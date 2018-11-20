<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class module extends module_embed {

  public $enabled = 'no';

  function disable() {
    core::boot_delete($this->id, 'enabled');
  }

  function uninstall() {
  # delete instances
    foreach (instance::all_by_module_get($this->id) as $c_instance) {
      if ($c_instance->select())
          $c_instance->delete();
    }
  # delete entities
    foreach (entity::all_by_module_get($this->id) as $c_entity) {
      if ($c_entity->uninstall())
           message::insert(translation::get('Entity %%_name was uninstalled.',     ['name' => $c_entity->name]));
      else message::insert(translation::get('Entity %%_name was not uninstalled!', ['name' => $c_entity->name]), 'error');
    }
  # delete from boot
    core::boot_delete($this->id, 'installed');
  }

}}