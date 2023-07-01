<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\composer_installer;

use Composer\Plugin\PluginInterface as Plugin_interface;
use effcore\composer_installer\Installer;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Plugin implements Plugin_interface {

    function activate($composer, $io) {
        $composer->getInstallationManager()->addInstaller(
            new Installer($io, $composer)
        );
    }

    function deactivate($composer, $io) {
    }

    function uninstall($composer, $io) {
    }

    static function postInstallCmd($event) {
        # rm -rf modules/vendors/packages/composer
        if (file_exists('modules/vendors/packages/composer/')) {
            $composer_items = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('modules/vendors/packages/composer/', FilesystemIterator::UNIX_PATHS|FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($composer_items as $c_path => $c_spl_file_info) {
                if ($c_spl_file_info->isFile()) {if (@unlink($c_path)) print "File '".      $c_path.  "' was removed.\n";}
                if ($c_spl_file_info->isDir ()) {if (@rmdir ($c_path)) print "Directory '". $c_path. "/' was removed.\n";} }
            if (@rmdir('modules/vendors/packages/composer/')) {
                print "Directory 'modules/vendors/packages/composer/' was removed.\n";
            }
        }
        # rm modules/vendors/packages/autoload.php
        if (file_exists('modules/vendors/packages/autoload.php') &&
                @unlink('modules/vendors/packages/autoload.php')) {
            print "File 'modules/vendors/packages/autoload.php' was removed.\n";
        }
        # cd shell && ./cache_clear.sh
        if (file_exists('dynamic/cache')) {
            $cache_items = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('dynamic/cache', FilesystemIterator::UNIX_PATHS|FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($cache_items as $c_path => $c_spl_file_info) {
                if ($c_path !== 'dynamic/cache/readme.md') {
                    if ($c_spl_file_info->isFile()) {if (@unlink($c_path)) print "File '".      $c_path.  "' was removed.\n";}
                    if ($c_spl_file_info->isDir ()) {if (@rmdir ($c_path)) print "Directory '". $c_path. "/' was removed.\n";}
                }
            }
        }
    }

}
