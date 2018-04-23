<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\url;
          use \effcore\user;
          use \effcore\table;
          use \effcore\pager;
          use \effcore\locale;
          use \effcore\markup;
          use \effcore\entity;
          use \effcore\factory;
          use \effcore\instance;
          use \effcore\table_body_row_cell;
          abstract class events_page extends \effcore\events_page {

  static function on_show_block_roles($page) {
    $thead = [['ID', 'Title', 'Is embed']];
    $tbody = entity::get('role')->select_instances();
    foreach ($tbody as $c_row) {
      $c_row->is_embed = $c_row->is_embed ? 'Yes' : 'No';
    }
    return new markup('x-block', ['class' => ['roles']],
      new table([], $tbody, $thead)
    );
  }

  static function on_show_block_user_info($page) {
    $user = (new instance('user', [
      'id' => $page->args_get('id_user')
    ]))->select();
    if ($user) {
      if ($user->id == user::get_current()->id ||               # owner
                 isset(user::get_current()->roles['admins'])) { # admin
      # get roles
        $roles = [];
        $storage_roles = entity::get('relation_role_ws_user')->select_instances(['id_user' => $user->id]);
        if ($storage_roles) {
          foreach ($storage_roles as $c_role) {
            $roles[] = $c_role->id_role;
          }
        }
      # get values
        $values = $user->get_values();
        $values['roles'] = count($roles) ? implode(', ', $roles) : '-';
        $values['created'] = locale::format_datetime($values['created']);
        $values['updated'] = locale::format_datetime($values['updated']);
        $values['password_hash'] = '*****';
        $values['is_embed'] = $values['is_embed'] ? 'Yes' : 'No';
        $values['avatar_path_relative'] = $values['avatar_path_relative'] ?: '-';
      # show table
        $thead = [['Parameter', 'Value']];
        $tbody = factory::array_rotate([array_keys($values), array_values($values)]);
        return new markup('x-block', ['class' => ['user_info']],
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