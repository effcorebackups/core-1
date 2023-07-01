<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\demo;

use const effcore\BR;
use const effcore\NL;
use effcore\Access;
use effcore\Response;
use effcore\Text_multiline;
use effcore\User;

abstract class Events_File {

    static function on_load_virtual($event, &$type_info, &$file) {
        if ($type_info->type === 'demotype') {
            if (Access::check((object)['roles' => ['admins' => 'admins']])) {
                $user = User::get_current();
                # note: be ready to clear the path from './', '../', '~/', '//' and etc (example: "http://example.com/дир/./../~/файл.demotype")
                $data = 'dirs: '.$file->dirs_get_relative().NL;
                $data.= 'name: '.$file->name.NL;
                $data.= 'type: '.$file->type.NL;
                $data.= 'current user: '.$user->nickname.NL;
                $data.= 'current user roles: '.implode(', ', $user->roles);
                header('Content-Length: '.strlen($data));
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename='.$file->name.'.txt');
                header('Cache-Control: private, no-cache, no-store, must-revalidate');
                header('Expires: 0');
                print $data;
                exit();
            } else {
                Response::send_header_and_exit('access_forbidden', null, new Text_multiline([
                    'go to <a href="/">front page</a>'
                ], [], BR.BR));
            }
        }
    }

}
