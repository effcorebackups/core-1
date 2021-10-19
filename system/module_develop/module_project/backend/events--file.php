<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\project {
          use \effcore\dynamic;
          use \effcore\instance;
          abstract class events_file {

  static function on_load_static_release($event, &$type_info, &$file) {
    if ($type_info->type === 'zip' || $type_info->type === '7z') {
      if ($file->dirs === dynamic::dir_files.'releases/') {
        $release = new instance('release', [
          'path' => $file->path_get_relative()
        ]);
        if ($release->select()) {
          $release->downloads_num++;
          $release->update();
        }
      }
    }
  }

}}