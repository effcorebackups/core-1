<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

  if (version_compare(phpversion(), '7.1.0', '<')) {
    print 'Current version of PHP is: '.phpversion().'<br>';
    print 'Required version is: 7.1.0+';
  } else {
    $www_root = DIRECTORY_SEPARATOR == '\\' ? str_replace('\\', '/', __DIR__) : __DIR__;
    define('effcore\\dir_root',    $www_root.'/');
    define('effcore\\dir_dynamic', $www_root.'/dynamic/');
    define('effcore\\dir_system',  $www_root.'/system/');
    require_once('system/boot.php');
  }