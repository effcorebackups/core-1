<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\project;

use effcore\Dynamic;
use effcore\Project_release;

abstract class Events_File {

    static function on_load_static_project_release($event, &$type_info, &$file) {
        if ($type_info->type === 'zip' || $type_info->type === '7z') {
            if ($file->dirs === Dynamic::DIR_FILES.'project_releases/') {
                Project_release::downloads_num_increment(
                    $file->path_get_relative()
                );
            }
        }
    }

}
