<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

  if (version_compare(phpversion(), '5.6.0', '<')) {
    print 'Current version of PHP is '.phpversion().'. Required version 5.6.0+';
  } else {
    $base_dir = DIRECTORY_SEPARATOR == '\\' ? str_replace('\\', '/', __DIR__) : __DIR__;
    define('effcore\\dir_root',    $base_dir.'/');
    define('effcore\\dir_dynamic', $base_dir.'/dynamic/');
    define('effcore\\dir_system',  $base_dir.'/system/');
    require_once('system/boot.php');
  }