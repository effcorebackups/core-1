<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

if (extension_loaded('bcmath')   !== true) {print 'Requires PHP extension "bcmath"!'.  "\n"; exit();}
if (extension_loaded('exif')     !== true) {print 'Requires PHP extension "exif"!'.    "\n"; exit();}
if (extension_loaded('fileinfo') !== true) {print 'Requires PHP extension "fileinfo"!'."\n"; exit();}
if (extension_loaded('filter')   !== true) {print 'Requires PHP extension "filter"!'.  "\n"; exit();}
if (extension_loaded('gd')       !== true) {print 'Requires PHP extension "gd"!'.      "\n"; exit();}
if (extension_loaded('hash')     !== true) {print 'Requires PHP extension "hash"!'.    "\n"; exit();}
if (extension_loaded('mbstring') !== true) {print 'Requires PHP extension "mbstring"!'."\n"; exit();}

if (DIRECTORY_SEPARATOR === '\\') $web_root = str_replace('\\', '/', realpath(__DIR__.'/../'));
if (DIRECTORY_SEPARATOR !== '\\') $web_root =                        realpath(__DIR__.'/../');
if (!$web_root) {
    print 'Web root is not defined!'."\n";
    exit();
}

define('PHP_INT_32_MAX', 0x7fffffff);
define('effcore\\A0', "\0");
define('effcore\\NL', "\n");
define('effcore\\CR', "\r");
define('effcore\\TB', "\t");
define('effcore\\BR', "<br>");
define('effcore\\HR', "<hr>");
define('effcore\\DIR_ROOT',    $web_root.'/');
define('effcore\\DIR_DYNAMIC', $web_root.'/dynamic/');
define('effcore\\DIR_SYSTEM',  $web_root.'/system/');
define('effcore\\DIR_MODULES', $web_root.'/modules/');

ini_set('pcre.jit', false);
date_default_timezone_set('UTC');

require_once(DIR_SYSTEM.'module_core/backend/Core.php');
require_once(DIR_SYSTEM.'module_storage/backend/interfaces/markers.php');
spl_autoload_register('\\effcore\\Core::structure_autoload');
if (in_array('container', stream_get_wrappers(), true) !== true) {
    stream_wrapper_register('container', '\\effcore\\File_container');
}
