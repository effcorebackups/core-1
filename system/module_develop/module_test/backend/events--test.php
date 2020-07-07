<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\test {
          use \effcore\text;
          abstract class events_test {

  static function set_roles($id_user, $id_role, $data = []) {
    $data['results']['reports'][] = new text('set role ID = %%_id_role for user ID = %%_id_user', [
      'id_role' => $id_role,
      'id_user' => $id_user
    ]);
  }

}}