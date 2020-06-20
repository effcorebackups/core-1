<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          abstract class events_selection {

  static function on_selection_build_before($event, $selection) {
    if ($selection->id === 'instance_select_multiple-relation_role_ws_user' ||
        $selection->id === 'instance_select-relation_role_ws_user') {
      $selection->field_insert_entity('user.nickname', 'user', 'nickname', ['type' => 'join_field', 'weight' => 390]);
   /* $selection->fields['relation_role_ws_user.id_user']->title = 'User ID'; */
      $selection->query_params['join_script']['with_user'] = (object)[
        'type'                 => 'left outer join',
        'entity_name'          => 'user',
        'entity_field_name'    => 'id',
        'on_entity_name'       => 'relation_role_ws_user',
        'on_entity_field_name' => 'id_user'
      ];
    }
  }

}}