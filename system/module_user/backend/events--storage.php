<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use const \effcore\dir_root;
          abstract class events_storage {

  static function on_instance_delete_before($event, $instance) {
    if (!empty($instance->avatar_path)) {
      @unlink(dir_root.$instance->avatar_path);
    }
  }

}}
