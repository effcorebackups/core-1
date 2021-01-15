<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

  if (version_compare(PHP_VERSION, '7.1.0', '>=')) {
    $www_root = DIRECTORY_SEPARATOR === '\\' ? str_replace('\\', '/', __DIR__) : __DIR__;
    define('effcore\\dir_root',    $www_root.'/');
    define('effcore\\dir_dynamic', $www_root.'/dynamic/');
    define('effcore\\dir_system',  $www_root.'/system/');
    define('effcore\\dir_modules', $www_root.'/modules/');
    require_once('system/boot.php');
  } else {
    print 'Current PHP version number is '.PHP_VERSION.'<br>';
    print 'Required version number is 7.1.0+';
  }