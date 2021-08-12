<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          abstract class events_selection {

  static function on_selection_build_before($event, $selection) {
    if ($selection->id === 'instance_select-user') {
      $selection->field_insert_handler('roles', 'Roles', '\\effcore\\modules\\user\\events_page_user::on_show_user_roles');
    }
  }

}}