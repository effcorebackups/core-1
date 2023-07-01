<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\page;

use effcore\Area;
use effcore\Core;
use effcore\Layout;
use effcore\Message;
use effcore\Page;

abstract class Events_Form_Layout {

    static function on_init($event, $form, $items) {
        $id = Page::get_current()->args_get('id');
        if (Layout::select($id)) {
            $layout = Core::deep_clone(Layout::select($id));
            foreach ($layout->children_select_recursive() as $c_child)
                if ($c_child instanceof Area)
                    $c_child->managing_enable();
            $form->child_select('layout_manager')->child_insert($layout);
        }
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'save':
                Message::insert('Changes was saved.');
                break;
        }
    }

}
