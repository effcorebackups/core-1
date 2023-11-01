<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\project;

use effcore\file;
use effcore\markup;
use effcore\text;

abstract class Events_Selection {

    static function handler__project_release__download_link($c_row_id, $c_row, $c_instance, $settings = []) {
        if ($c_row['path']['value']) {
            $file = new File($c_row['path']['value']);
            return new Markup('a', ['href' => new Text(
                '%%_request_scheme://%%_request_host/'.$c_row['path']['value'], [], false, true)
            ], $file->file_get());
        } else {
            return '—';
        }
    }

}
