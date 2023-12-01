<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\project;

use effcore\File;
use effcore\Markup;
use effcore\Text_simple;
use effcore\Text;

abstract class Events_Selection {

    static function handler__project_release__path_as_link($c_cell_id, $c_row, $c_instance, $origin) {
        if (array_key_exists('path', $c_instance->values_get())) {
            if ($c_instance->path) {
                $file = new File($c_instance->path);
                return new Markup('a', ['href' => new Text(
                    '%%_request_scheme://%%_request_host/'.$c_instance->path, [], false, true)
                ], new Text_simple($file->file_get()));
            } else return '';
        }     else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'path']);
    }

}
