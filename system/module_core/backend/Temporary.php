<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

abstract class Temporary extends Dynamic {

    const TYPE = 'tmp';
    const DIRECTORY = parent::DIRECTORY.'tmp/';

}
