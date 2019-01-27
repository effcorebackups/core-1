<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use const \effcore\nl;
          use \effcore\console;
          use \effcore\session;
          use \effcore\user;
          abstract class events_file {

  static function process_demotype($file_info) {
    $session = session::select();
    if ($session &&
        $session->id_user) {
      $user = user::current_get();
      if (!isset($user->roles['registered'])) {
        user::init($session->id_user, false); # false - do not load roles from the storage
        $user = user::current_get();
      }
      if (isset($user->roles['registered'])) {
        $result = 'dirs: '.$file_info->dirs.nl;
        $result.= 'name: '.$file_info->name.nl;
        $result.= 'type: '.$file_info->type.nl;
        $result.= 'call \effcore\modules\demo\events_file::process_demotype'.nl;
        $result.= 'current user: '.$user->nick.nl;
        $result.= 'current user roles: '.implode(', ', $user->roles).nl;
        header('Content-Length: '.strlen($result));
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=demo.txt');
        header('Cache-Control: private, no-cache, no-store, must-revalidate');
        header('Expires: 0');
        print $result;
      }
    }
    console::log_store();
    exit();
  }

}}