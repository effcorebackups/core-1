<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\menu;

use effcore\Core;
use effcore\Page;
use effcore\Tab_item;
use effcore\Text_multiline;
use effcore\Tree;
use effcore\Url;

abstract class Events_Page_Instance_select_multiple {

    static function on_redirect($event, $page) {
        $entity_name = Page::get_current()->args_get('entity_name');
        $category_id = Page::get_current()->args_get('category_id');
        if ($entity_name === 'tree_item') {
            $trees = Tree::select_all('sql');
            Core::array_sort_by_string($trees);
            if (empty($trees[$category_id])) {
                Url::go(Page::get_current()->args_get('base').'/menu/tree_item///'.reset($trees)->id);
            }
        }
    }

    static function on_tab_build_before($event, $tab) {
        $entity_name = Page::get_current()->args_get('entity_name');
        $category_id = Page::get_current()->args_get('category_id');
        if ($entity_name === 'tree_item') {
            $trees = Tree::select_all('sql');
            Core::array_sort_by_string($trees);
            foreach ($trees as $c_tree) {
                $c_tree_item_title = (new Text_multiline(['title' => $c_tree->title, 'id' => '('.$c_tree->id.')'], [], ' '))->render();
                Tab_item::insert(                                      $c_tree_item_title,
                    'data_menu_tree_item_'                            .$c_tree->id,
                    'data_menu_tree_item', 'data', 'menu/tree_item///'.$c_tree->id, null, [], [], false, 0, 'menu'
                );
            }
        }
    }

}
