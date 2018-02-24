<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effectivecore {
          abstract class cache extends \effectivecore\dynamic {

  static public $type = 'cache';
  static public $directory = dir_dynamic.'cache/';

  static function update($name, $data, $info = null) {
    if (parent::update($name, $data, $info)) {
      console::add_log('storage', 'cache', 'cache for '.$name.' was rebuilded', 'ok', 0);
    }
  }

}}