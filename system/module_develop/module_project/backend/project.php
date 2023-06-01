<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

abstract class project {

    static function select($id) {
        return (new instance('project', [
            'id' => $id
        ]))->select();
    }

}
