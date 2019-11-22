<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use const \effcore\br;
          use const \effcore\nl;
          use \effcore\access;
          use \effcore\console;
          use \effcore\core;
          use \effcore\event;
          use \effcore\session;
          use \effcore\text_multiline;
          use \effcore\user;
          abstract class events_file {

  static function process_demotype($file_info) {
    if (access::check((object)['roles' => ['registered' => 'registered']])) {
      $data = '';
      event::start('on_file_process_demotype', null, [$file_info, &$data]);
      header('Content-Length: '.strlen($data));
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename=demo.txt');
      header('Cache-Control: private, no-cache, no-store, must-revalidate');
      header('Expires: 0');
      print $data;
      console::log_store();
      exit();
    } else {
      core::send_header_and_exit('access_forbidden', null, new text_multiline([
        'file of this type is protected',
        'go to <a href="/">front page</a>'
      ], [], br.br));
    }
  }

  static function on_process_demotype($event, $file_info, &$data) {
    $user = user::get_current();
    $data = 'dirs: '.$file_info->dirs.nl;
    $data.= 'name: '.$file_info->name.nl;
    $data.= 'type: '.$file_info->type.nl;
    $data.= 'call \\effcore\\modules\\demo\\events_file::on_process_demotype'.nl;
    $data.= 'current user: '.$user->nickname.nl;
    $data.= 'current user roles: '.implode(', ', $user->roles);
  }

}}