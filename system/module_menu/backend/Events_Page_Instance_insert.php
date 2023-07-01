<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\menu;

use const effcore\BR;
use effcore\Page;
use effcore\Response;
use effcore\Text_multiline;
use effcore\Tree;

abstract class Events_Page_Instance_insert {

    static function on_check_existence($event, $page) {
        $entity_name = Page::get_current()->args_get('entity_name');
        $category_id = Page::get_current()->args_get('category_id');
        if ($entity_name === 'tree_item') {
            $trees = Tree::select_all('sql');
            if (!$category_id || empty($trees[$category_id])) {
                Response::send_header_and_exit('page_not_found', null, new Text_multiline(['wrong category', 'go to <a href="/">front page</a>'], [], BR.BR));
            }
        }
    }

}
