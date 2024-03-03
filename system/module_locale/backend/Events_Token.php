<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\locale;

use effcore\Language;
use effcore\Page;
use effcore\Translation;

abstract class Events_Token {

    static function on_apply($name, $args) {
        switch ($name) {
            case 'translation':
                $code = $args->get(-1) && strlen($args->get(-1)) === 2 ?
                        $args->get(-1) : '';
                if ($args->get_count() === 1                ) return Translation::apply($args->get(0));
                if ($args->get_count()  >  1 && $code === '') return Translation::apply($args->get(0), $args->get_named_all());
                if ($args->get_count()  >  1 && $code !== '') return Translation::apply($args->get(0), $args->get_named_all(), $code);
                break;
            case 'lang_code_global':
                return Language::code_get_current();
            case 'lang_code_page':
                if ($args->get_count() > 0) {
                    $page_id = $args->get(0);
                    $page = is_string($page_id) && strlen($page_id) ? Page::get_by_id($page_id, true) : null;
                    return !empty($page->lang_code) ?
                                  $page->lang_code : Language::code_get_current();
                }
                break;
        }
    }

}
