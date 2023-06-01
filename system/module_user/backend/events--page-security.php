<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\user;

use effcore\url;

abstract class events_page_security {

    static function on_redirect($event, $page) {
        $type = $page->args_get('type');
        if ($type === null) url::go($page->args_get('base').'/settings');
    }

}
