<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

  if (version_compare(phpversion(), '5.6.0', '<')) {
    print 'Current version of PHP is '.phpversion().'. Required version 5.6.0+';
  } else {
    define('effcore\dir_root',    __DIR__.'/');
    define('effcore\dir_dynamic', __DIR__.'/dynamic/');
    define('effcore\dir_files',   __DIR__.'/dynamic/files/');
    define('effcore\dir_system',  __DIR__.'/system/');
    require_once('system/boot.php');
  }