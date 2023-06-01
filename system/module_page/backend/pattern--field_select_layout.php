<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class field_select_layout extends field_select {

    public $title = 'Layout';
    public $title__not_selected = '- select -';
    public $attributes = ['data-type' => 'layout'];
    public $element_attributes = [
        'name'     => 'layout',
        'required' => true
    ];

    function build() {
        if (!$this->is_builded) {
            parent::build();
            $items = [];
            foreach (layout::select_all() as $c_layout) {
                $c_text_object = new text_multiline(['title' => $c_layout->title, 'id' => '('.$c_layout->id.')'], [], ' ');
                $c_text_object->_text_translated = $c_text_object->render();
                $items[$c_layout->id] = $c_text_object; }
            core::array_sort_by_string($items, '_text_translated', 'd', false);
            $this->items = ['not_selected' => $this->title__not_selected] + $items;
            $this->is_builded = false;
            parent::build();
        }
    }

}
