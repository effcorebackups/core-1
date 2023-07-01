<?php

######################################################################
### Copyright © 20NN—20NN Author/Rightholder. All rights reserved. ###
######################################################################

class Composer_helper {

    static function preInstallCmd($event) {
        print "!!! RUN COMPOSER AT THE ROOT OF THE EFFCORE PROJECT, NOT IN THIS DIRECTORY !!!\n";
        exit();
    }

}
