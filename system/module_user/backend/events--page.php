<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\block;
          use \effcore\core;
          use \effcore\entity;
          use \effcore\instance;
          use \effcore\locale;
          use \effcore\table;
          use \effcore\user;
          abstract class events_page {

  static function on_show_block_roles($page) {
    $thead = [['ID', 'Title', 'Is embed']];
    $tbody = entity::get('role')->instances_select();
    foreach ($tbody as $c_row) {
      $c_row->is_embed = $c_row->is_embed ? 'Yes' : 'No';
    }
    return new block('', ['class' => ['roles' => 'roles']],
      new table([], $tbody, $thead)
    );
  }

  static function on_show_block_user_info($page) {
    $user = (new instance('user', [
      'id' => $page->args_get('id_user')
    ]))->select();
    if ($user) {
      if ($user->id == user::current_get()->id ||               # owner
                 isset(user::current_get()->roles['admins'])) { # admin
      # get roles
        $roles = [];
        $storage_roles = entity::get('relation_role_ws_user')->instances_select(['id_user' => $user->id]);
        if ($storage_roles) {
          foreach ($storage_roles as $c_role) {
            $roles[] = $c_role->id_role;
          }
        }
      # get values
        $values = $user->values_get();
        $values['roles'] = count($roles) ? implode(', ', $roles) : '-';
        $values['created'] = locale::format_datetime($values['created']);
        $values['updated'] = locale::format_datetime($values['updated']);
        $values['password_hash'] = '*****';
        $values['is_embed'] = $values['is_embed'] ? 'Yes' : 'No';
        $values['avatar_path_relative'] = $values['avatar_path_relative'] ?: '-';
      # show table
        $thead = [['Parameter', 'Value']];
        $tbody = core::array_rotate([array_keys($values), array_values($values)]);
        return new block('', ['class' => ['user-info' => 'user-info']],
          new table([], $tbody, $thead)
        );
      } else {
        core::send_header_and_exit('access_denided');
      }
    } else {
      core::send_header_and_exit('page_not_found');
    }
  }

}}