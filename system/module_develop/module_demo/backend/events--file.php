<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use const \effcore\br;
          use const \effcore\nl;
          use \effcore\access;
          use \effcore\console;
          use \effcore\response;
          use \effcore\text_multiline;
          use \effcore\user;
          abstract class events_file {

  static function on_load_virtual($event, &$type_info, &$file) {
    if ($type_info->type === 'demotype') {
      if (access::check((object)['roles' => ['admins' => 'admins']])) {
        $user = user::get_current();
      # note: be ready to clear the path from './', '../', '~/', '//' and etc (example: "http://example.com/дир/./../~/файл.demotype")
        $data = 'dirs: '.$file->dirs_get_relative().nl;
        $data.= 'name: '.$file->name.nl;
        $data.= 'type: '.$file->type.nl;
        $data.= 'current user: '.$user->nickname.nl;
        $data.= 'current user roles: '.implode(', ', $user->roles);
        header('Content-Length: '.strlen($data));
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.$file->name.'.txt');
        header('Cache-Control: private, no-cache, no-store, must-revalidate');
        header('Expires: 0');
        print $data;
        exit();
      } else {
        response::send_header_and_exit('access_forbidden', null, new text_multiline([
          'go to <a href="/">front page</a>'
        ], [], br.br));
      }
    }
  }

}}