<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class template_file extends template_text {

  public $path = '';

  function render() {
    $path = module::get($this->module_id)->path.$this->path;
    $file = new file($path);
    $this->data = $file->load();
    return parent::render();
  }

  ###########################
  ### static declarations ###
  ###########################

  static function get_copied_properties() {
    return ['path' => 'path'] + parent::get_copied_properties();
  }

}}