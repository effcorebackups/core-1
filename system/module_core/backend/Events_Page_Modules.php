<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\core;

use effcore\URL;

abstract class Events_Page_Modules {

    static function on_redirect($event, $page) {
        $action = $page->args_get('action');
        if ($action === null) {
            URL::go($page->args_get('base').'/install');
        }
    }

}
