<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
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
                    'build'      => ['field_!f' => 'build'     , 'operator' => '=', 'value_!v' => $build     ] ]],
            'limit' => 1
        ]);
    }

    static function downloads_num_increment($path) {
        Storage::get(
            Entity::get('project_release')->storage_name
        )->query([
            'action' => 'UPDATE',
            'target_!t' => '~project_release',
            'set' => 'SET',
            'fields_with_values' => [
                'downloads_num' => [
                    'field_1_!f' => 'downloads_num', 'operator_1' => '=',
                    'field_2_!f' => 'downloads_num', 'operator_2' => '+', 'value_!v' => 1 ]],
            'where_begin' => 'WHERE',
            'where' => [
                'path' => [
                    'field_!f' => 'path',
                    'operator' => '=',
                    'value_!v' => $path
                ]
            ]
        ]);
    }

}
