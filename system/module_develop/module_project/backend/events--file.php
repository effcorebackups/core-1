<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\project {
          use \effcore\dynamic;
          use \effcore\instance;
          use \effcore\release;
          abstract class events_file {

  static function on_load_static_release($event, &$type_info, &$file) {
    if ($type_info->type === 'zip' || $type_info->type === '7z') {
      if ($file->dirs === dynamic::dir_files.'releases/') {
        $release = release::select_by_path($file->path_get_relative());
        if ($release &&
            $release->downloads_num < PHP_INT_32_MAX) {
            $release->downloads_num++;
            $release->update();
        }
      }
    }
  }

}}