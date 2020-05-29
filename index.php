<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

  if (version_compare(PHP_VERSION, '7.1.0', '>=')) {
    $www_root = DIRECTORY_SEPARATOR === '\\' ? str_replace('\\', '/', __DIR__) : __DIR__;
    define('effcore\\dir_root',    $www_root.'/');
    define('effcore\\dir_dynamic', $www_root.'/dynamic/');
    define('effcore\\dir_system',  $www_root.'/system/');
    define('effcore\\dir_modules', $www_root.'/modules/');
    require_once('system/boot.php');
  } else {
    print 'The System requires a version of PHP not lower than 7.1.0<br>';
    print 'The current version of PHP is '.PHP_VERSION;
  }