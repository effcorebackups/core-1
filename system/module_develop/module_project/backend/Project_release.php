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
        return Entity::get('project_release')->instances_select([
            'where' => [
                'conjunction_!and' => [
                    'id_project' => ['field_!f' => 'id_project', 'operator' => '=', 'value_!v' => $id_project],
                    'build'      => ['field_!f' => 'build',      'operator' => '=', 'value_!v' => $build     ] ]],
            'limit' => 1
        ]);
    }

}
