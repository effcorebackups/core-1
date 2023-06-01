<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

class composer_helper {

    static function preInstall($event) {
        print "!!! RUN COMPOSER AT THE ROOT OF THE EFFCORE PROJECT, NOT IN THIS DIRECTORY !!!\n";
        exit();
    }

}
