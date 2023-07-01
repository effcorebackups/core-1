<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\composer_installer;

use Composer\Installer\LibraryInstaller as Library_installer;
use Composer\Installers\Installer as Composer_installer;
use effcore\composer_installer\Custom_installer;

class Installer extends Composer_installer {

    public function supports($packageType) {
        return in_array($packageType, [
            'effcore-bundle',
            'effcore-module'
        ]);
    }

    public function getInstallPath($package) {
        $custom_installer = new Custom_installer($package, $this->composer, $this->io);
        $path = $custom_installer->getInstallPath($package, $package->getType());
        return $path ?: Library_installer::getInstallPath($package);
    }

}
