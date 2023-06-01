<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\locale;

use effcore\core;
use effcore\language;
use effcore\markup;
use effcore\page;
use effcore\tab_item;
use effcore\text_simple;
use effcore\text;
use effcore\url;

abstract class events_page {

    static function on_page_language_apply($event, $page) {
        if ($page->lang_code !== null) {
            language::code_set_current($page->lang_code);
        }
    }

    static function on_redirect($event, $page) {
        $section = $page->args_get('section');
        if ($section === null) url::go($page->args_get('base').'/general');
        if ($section && strpos($section, 'by_language') === 0) {
            $languages = language::get_all();
            $lang_code = $page->args_get('lang_code');
            if (!isset($languages[$lang_code])) {
                url::go($page->args_get('base').'/by_language/en');
            }
        }
    }

    static function on_tab_build_before($event, $tab) {
        $section = page::get_current()->args_get('section');
        if ($section && strpos($section, 'by_language') === 0) {
            $languages = language::get_all();
            core::array_sort_by_string($languages, 'title_en', 'd', false);
            foreach ($languages as $c_language) {
                if ($c_language->code !== 'en') {
                    tab_item::insert(                                $c_language->title_en,
                        'locale_by_language_'                         .$c_language->code,
                        'locale_by_language', 'locale', 'by_language/'.$c_language->code, null, [], [], false, 0, 'locale'
                    );
                }
            }
        }
    }

    static function block_markup__tree_languages($page, $args = []) {
        $languages = language::get_all();
        core::array_sort_by_string($languages, 'title_en', 'd', false);
        $languages = ['en' => $languages['en']] + $languages;
        $menu = new markup('x-tree', ['role' => 'tree', 'data-id' => 'languages', 'data-style' => 'linear']);
        $menu->child_insert(new markup('h2', ['data-tree-title' => true, 'aria-hidden' => 'true'], 'Language selection menu'), 'title');
        $menu->child_insert(new markup('ul'), 'container');
        foreach ($languages as $c_language) {
            $c_title = $c_language->code !== 'en' ?
                       $c_language->title_en.' / '.
                       $c_language->title_native :
                       $c_language->title_en;
            $c_href = $page->args_get('base').'/'.$c_language->code;
            if (url::is_active($c_href, 'path'))
                 $c_link = new markup('a', ['href' => $c_href, 'title' => new text('go to %%_language language', ['language' => $c_language->title_en], false), 'aria-selected' => 'true'], new text_simple($c_title));
            else $c_link = new markup('a', ['href' => $c_href, 'title' => new text('go to %%_language language', ['language' => $c_language->title_en], false)                           ], new text_simple($c_title));
            $menu->child_select('container')->child_insert(
                new markup('li', ['data-id' => 'language_'.$c_language->code], $c_link), $c_language->code
            );
        }
        return $menu;
    }

}
