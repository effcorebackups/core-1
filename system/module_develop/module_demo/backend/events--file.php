<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use const \effcore\br;
          use const \effcore\nl;
          use \effcore\access;
          use \effcore\console;
          use \effcore\core;
          use \effcore\text_multiline;
          use \effcore\user;
          abstract class events_file {

  static function on_load_virtual($event, $type_info, $file_info, $path) {
    if (access::check((object)['roles' => ['admins' => 'admins']])) {
      $user = user::get_current();
      $data = 'dirs: '.$file_info->dirs.nl;
      $data.= 'name: '.$file_info->name.nl;
      $data.= 'type: '.$file_info->type.nl;
      $data.= 'current user: '.$user->nickname.nl;
      $data.= 'current user roles: '.implode(', ', $user->roles);
      header('Content-Length: '.strlen($data));
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename='.$file_info->name.'.txt');
      header('Cache-Control: private, no-cache, no-store, must-revalidate');
      header('Expires: 0');
      print $data;
      console::log_store();
      exit();
    } else {
      core::send_header_and_exit('access_forbidden', null, new text_multiline([
        'go to <a href="/">front page</a>'
      ], [], br.br));
    }
  }

}}