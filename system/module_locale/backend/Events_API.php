<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\locale;

use effcore\Language;
use effcore\Request;
use effcore\Response;
use effcore\Translation;

abstract class Events_API {

    const TEXT_WRONG_LANG_CODE = 'wrong lang code';

    static function on_translations_get($page, $args = []) {
        $lang_code = $page->args_get('key');
        $format = Request::value_get('format', 0, '_GET', Response::FORMAT_JSON);
        if (Language::get($lang_code)) {
            Response::send_and_exit(
                Translation::select_all_by_code($lang_code),
                Response::EXIT_STATE_OK,
                $format
            );
        } else {
            Response::send_and_exit(
                static::TEXT_WRONG_LANG_CODE,
                Response::EXIT_STATE_ERROR,
                $format
            );
        }
    }

}
