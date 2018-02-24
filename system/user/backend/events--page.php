<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effectivecore\modules\user {
          use \effectivecore\url as url;
          use \effectivecore\user as user;
          use \effectivecore\table as table;
          use \effectivecore\pager as pager;
          use \effectivecore\locale as locale;
          use \effectivecore\markup as markup;
          use \effectivecore\entity as entity;
          use \effectivecore\factory as factory;
          use \effectivecore\instance as instance;
          use \effectivecore\table_body_row_cell as table_body_row_cell;
          use \effectivecore\control_actions_list as control_actions_list;
          abstract class events_page extends \effectivecore\events_page {

  static function on_show_block_roles($page) {
    $thead = [['ID', 'Title', 'Is embed']];
    $tbody = entity::get('role')->select_instances();
    foreach ($tbody as $c_row) {
      $c_row->is_embed = $c_row->is_embed ? 'Yes' : 'No';
    }
    return new markup('x-block', ['id' => 'roles_admin'],
      new table([], $tbody, $thead)
    );
  }

  static function on_show_block_users($page) {
    $pager = new pager();
    if ($pager->has_error) {
      factory::send_header_and_exit('not_found');
    } else {
      $thead = [['ID', 'EMail', 'Nick', 'Created', 'Is embed', '']];
      $tbody = [];
      foreach (entity::get('user')->select_instances() as $c_user) {
        $c_action_list = new control_actions_list([], [], null);
        $c_action_list->action_add('/user/'.$c_user->id, 'view');
        $c_action_list->action_add('/user/'.$c_user->id.'/edit?'.url::make_back_part(), 'edit');
        $c_action_list->action_add('/admin/users/delete/'.$c_user->id.'?'.url::make_back_part(), 'delete', !$c_user->is_embed);
        $tbody[] = [
          new table_body_row_cell(['class' => ['id' => 'id']], $c_user->id),
          new table_body_row_cell(['class' => ['email' => 'email']], $c_user->email),
          new table_body_row_cell(['class' => ['nick' => 'nick']], $c_user->nick),
          new table_body_row_cell(['class' => ['created' => 'created']], locale::format_datetime($c_user->created)),
          new table_body_row_cell(['class' => ['is_embed' => 'is_embed']], $c_user->is_embed ? 'Yes' : 'No'),
          new table_body_row_cell(['class' => ['actions' => 'actions']], $c_action_list)
        ];
      }
      return new markup('x-block', ['id' => 'users_admin'],
        new table([], $tbody, $thead)
      );
    }
  }

  static function on_show_block_user_info($page, $id) {
    $user = (new instance('user', ['id' => $id]))->select();
    if ($user) {
      if ($user->id == user::get_current()->id ||               # owner
                 isset(user::get_current()->roles['admins'])) { # admin
      # get roles
        $roles = [];
        $storage_roles = entity::get('relation_role_ws_user')->select_instances(['id_user' => $id]);
        if ($storage_roles) {
          foreach ($storage_roles as $c_role) {
            $roles[] = $c_role->id_role;
          }
        }
      # get values
        $values = $user->get_values();
        $values['roles'] = count($roles) ? implode(', ', $roles) : '-';
        $values['created'] = locale::format_datetime($values['created']);
        $values['password_hash'] = '*****';
        $values['is_embed'] = $values['is_embed'] ? 'Yes' : 'No';
      # show table
        $thead = [['Parameter', 'Value']];
        $tbody = factory::array_rotate([array_keys($values), array_values($values)]);
        return new markup('x-block', ['id' => 'user_info'],
          new table([], $tbody, $thead)
        );
      } else {
        factory::send_header_and_exit('access_denided');
      }
    } else {
      factory::send_header_and_exit('not_found');
    }
  }

}}