<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

abstract class Project_release {

    static function select_by_path($path) {
        return (new Instance('project_release', [
            'path' => $path
        ]))->select();
    }

    static function select($id_project, $build) {
        return Entity::get('project_release')->instances_select(['conditions' => ['conjunction_!and' => [
            'id_project' => ['id_project_!f' => 'id_project', 'id_project_operator' => '=', 'id_project_!v' => $id_project],
            'build'      => [     'build_!f' => 'build',           'build_operator' => '=',      'build_!v' => $build     ] ]], 'limit' => 1
        ]);
    }

}
