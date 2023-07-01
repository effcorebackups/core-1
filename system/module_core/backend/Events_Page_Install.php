<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\core;

use effcore\Language;
use effcore\Url;

abstract class Events_Page_Install {

    static function on_redirect($event, $page) {
        $languages = Language::get_all();
        $code = $page->args_get('lang_code');
        if (empty($languages[$code])) Url::go($page->args_get('base').'/en');
        Language::code_set_current($code);
    }

}
