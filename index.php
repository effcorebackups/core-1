<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

if (version_compare(PHP_VERSION, '7.3.0', '>=') !== true) {
    print 'Requires PHP version 7.3.0 or higher! The current version is '.PHP_VERSION;
    exit();
}

if (PHP_INT_SIZE < 8) {
    print 'Requires 64-bit platform!';
    exit();
}

require_once('system/boot_initialization.php');
require_once('system/boot_web.php');
