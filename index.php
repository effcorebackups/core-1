<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

if (version_compare(PHP_VERSION, '7.1.0', '>=') !== true) {print 'Requires PHP version 7.1.0 or higher! The current version is '.PHP_VERSION; exit();}
if (extension_loaded('bcmath')                  !== true) {print 'Requires PHP extension "bcmath"!';                                          exit();}
if (extension_loaded('exif')                    !== true) {print 'Requires PHP extension "exif"!';                                            exit();}
if (extension_loaded('fileinfo')                !== true) {print 'Requires PHP extension "fileinfo"!';                                        exit();}
if (extension_loaded('filter')                  !== true) {print 'Requires PHP extension "filter"!';                                          exit();}
if (extension_loaded('gd')                      !== true) {print 'Requires PHP extension "gd"!';                                              exit();}
if (extension_loaded('hash')                    !== true) {print 'Requires PHP extension "hash"!';                                            exit();}
if (extension_loaded('mbstring')                !== true) {print 'Requires PHP extension "mbstring"!';                                        exit();}
if (DIRECTORY_SEPARATOR === '\\') $web_root = str_replace('\\', '/', __DIR__);
if (DIRECTORY_SEPARATOR !== '\\') $web_root =                        __DIR__;
require_once('system/boot.php');
