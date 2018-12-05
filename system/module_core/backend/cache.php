<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class cache extends dynamic {

  const type = 'cache';
  const directory = dir_dynamic.'cache/';

  static function update($name, $data, $sub_dirs = '', $info = null) {
    if (parent::update($name, $data, $sub_dirs, $info)) {
      console::log_insert('storage', 'cache', 'cache for '.$name.' was rebuilded', 'ok', 0);
    }
  }

}}