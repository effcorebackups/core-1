<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\menu;

use effcore\core;
use effcore\page;
use effcore\tab_item;
use effcore\text_multiline;
use effcore\tree;
use effcore\url;

abstract class events_page_instance_select_multiple {

    static function on_redirect($event, $page) {
        $entity_name = page::get_current()->args_get('entity_name');
        $category_id = page::get_current()->args_get('category_id');
        if ($entity_name === 'tree_item') {
            $trees = tree::select_all('sql');
            core::array_sort_by_string($trees);
            if (empty($trees[$category_id])) {
                url::go(page::get_current()->args_get('base').'/menu/tree_item///'.reset($trees)->id);
            }
        }
    }

    static function on_tab_build_before($event, $tab) {
        $entity_name = page::get_current()->args_get('entity_name');
        $category_id = page::get_current()->args_get('category_id');
        if ($entity_name === 'tree_item') {
            $trees = tree::select_all('sql');
            core::array_sort_by_string($trees);
            foreach ($trees as $c_tree) {
                $c_tree_item_title = (new text_multiline(['title' => $c_tree->title, 'id' => '('.$c_tree->id.')'], [], ' '))->render();
                tab_item::insert(                                      $c_tree_item_title,
                    'data_menu_tree_item_'                            .$c_tree->id,
                    'data_menu_tree_item', 'data', 'menu/tree_item///'.$c_tree->id, null, [], [], false, 0, 'menu'
                );
            }
        }
    }

}
