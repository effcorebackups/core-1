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
                $release = Project_release::select_by_path($file->path_get_relative());
                if ($release &&
                    $release->downloads_num < PHP_INT_32_MAX) {
                    $release->downloads_num++;
                    $release->update();
                }
            }
        }
    }

}
