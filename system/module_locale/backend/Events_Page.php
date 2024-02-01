<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\locale;

use effcore\Core;
use effcore\Language;
use effcore\Markup;
use effcore\Page;
use effcore\Tab_item;
use effcore\Text_simple;
use effcore\Text;
use effcore\Url;

abstract class Events_Page {

    static function on_page_language_apply($event, $page) {
        if ($page->lang_code !== null && Language::get($page->lang_code)) {
            Language::code_set_current($page->lang_code);
        }
    }

    static function on_redirect($event, $page) {
        $section = $page->args_get('section');
        if ($section === null) Url::go($page->args_get('base').'/general');
        if ($section && str_starts_with($section, 'by_language')) {
            $languages = Language::get_all();
            $lang_code = $page->args_get('lang_code');
            if (!isset($languages[$lang_code])) {
                Url::go($page->args_get('base').'/by_language/en');
            }
        }
    }

    static function on_tab_build_before($event, $tab) {
        $section = Page::get_current()->args_get('section');
        if ($section && str_starts_with($section, 'by_language')) {
            $languages = Language::get_all();
            Core::array_sort_by_string($languages, 'title_en', Core::SORT_DSC, false);
            foreach ($languages as $c_language) {
                if ($c_language->code !== 'en') {
                    Tab_item::insert(                                  $c_language->title_en,
                        'locale_by_language_'                         .$c_language->code,
                        'locale_by_language', 'locale', 'by_language/'.$c_language->code, null, [], [], false, 0, 'locale'
                    );
                }
            }
        }
    }

    static function block_markup__tree_languages($page, $args = []) {
        $languages = Language::get_all();
        Core::array_sort_by_string($languages, 'title_en', Core::SORT_DSC, false);
        $languages = ['en' => $languages['en']] + $languages;
        $menu = new Markup('x-tree', ['role' => 'tree', 'data-id' => 'languages', 'data-style' => 'like-select']);
        $menu->child_insert(new Markup('h2', ['aria-hidden' => 'true'], 'Language selection menu'), 'title');
        $menu->child_insert(new Markup('ul'), 'container');
        foreach ($languages as $c_language) {
            $c_title = $c_language->code !== 'en' ?
                       $c_language->title_en.' / '.
                       $c_language->title_native :
                       $c_language->title_en;
            $c_href = $page->args_get('base').'/'.$c_language->code;
            if (Url::is_active($c_href, 'path'))
                 $menu->child_select('container')->child_insert(new Markup('li', ['data-id' => 'language_'.$c_language->code, 'aria-selected' => 'true'], new Markup('a', ['href' => $c_href, 'title' => new Text('go to %%_language language', ['language' => $c_language->title_en], false), 'aria-selected' => 'true'], new Text_simple($c_title)) ), $c_language->code);
            else $menu->child_select('container')->child_insert(new Markup('li', ['data-id' => 'language_'.$c_language->code                           ], new Markup('a', ['href' => $c_href, 'title' => new Text('go to %%_language language', ['language' => $c_language->title_en], false)                           ], new Text_simple($c_title)) ), $c_language->code);
        }
        return $menu;
    }

}
