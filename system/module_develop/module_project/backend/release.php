<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class release {

  static function select_by_path($path) {
    return (new instance('release', [
      'path' => $path
    ]))->select();
  }

  static function select($id_project, $build) {
    return entity::get('release')->instances_select(['conditions' => [
      'id_project_!f'       => 'id_project',
      'id_project_operator' => '=',
      'id_project_!v'       => $id_project,
      'conjunction'         => 'and',
      'build_!f'            => 'build',
      'build_operator'      => '=',
      'build!v'             => $build], 'limit' => 1
    ]);
  }

}}