<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class instances_manager {

  static function instance_insert($page) {
    foreach (entity::all_get(false) as $c_entity) {
      tabs::insert_item($c_entity->title, 'insert_'.$c_entity->name, 'insert', $c_entity->name);
    }
  }

  static function instance_update($page) {
    return new text('instance_update is UNDER CONSTRUCTION');
  }

  static function instance_delete($page) {
    return new text('instance_delete is UNDER CONSTRUCTION');
  }

  static function instance_select($page) {
    $pager = new pager();
    if ($pager->has_error) {
      core::send_header_and_exit('page_not_found');
    } else {
      $thead = [['ID', 'EMail', 'Nick', 'Created', 'Is embed', '']];
      $tbody = [];
      foreach (entity::get('user')->instances_select() as $c_user) {
        $c_action_list = new control_actions_list();
        $c_action_list->action_add('/user/'.$c_user->id, 'view');
        $c_action_list->action_add('/user/'.$c_user->id.'/edit?'.url::back_part_make(), 'edit');
        $c_action_list->action_add('/manage/users/delete/'.$c_user->id.'?'.url::back_part_make(), 'delete', !$c_user->is_embed);
        $tbody[] = [
          new table_body_row_cell(['class' => ['id'       => 'id']], $c_user->id),
          new table_body_row_cell(['class' => ['email'    => 'email']], $c_user->email),
          new table_body_row_cell(['class' => ['nick'     => 'nick']], $c_user->nick),
          new table_body_row_cell(['class' => ['created'  => 'created']], locale::format_datetime($c_user->created)),
          new table_body_row_cell(['class' => ['is_embed' => 'is_embed']], $c_user->is_embed ? 'Yes' : 'No'),
          new table_body_row_cell(['class' => ['actions'  => 'actions']], $c_action_list)
        ];
      }
      return new block('', ['class' => ['users' => 'users']],
        new table([], $tbody, $thead)
      );
    }
  }

}}