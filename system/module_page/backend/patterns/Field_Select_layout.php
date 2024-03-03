<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Field_Select_layout extends Field_Select {

    public $title = 'Layout';
    public $title__not_selected = '- select -';
    public $attributes = [
        'data-type' => 'layout'];
    public $element_attributes = [
        'name'     => 'layout',
        'required' => true
    ];

    function build() {
        if (!$this->is_builded) {
            $this->items = ['not_selected' => $this->title__not_selected] + static::items_generate();
            parent::build();
        }
    }

    ###########################
    ### static declarations ###
    ###########################

    static function items_generate() {
        $result = [];
        $layouts = Layout::select_all();
        Core::array_sort_by_string($layouts);
        foreach ($layouts as $c_layout) {
            $result[$c_layout->id] = (new Text_multiline([
                'title' => $c_layout->title, 'id' => '('.$c_layout->id.')'], [], ' '
            ))->render(); }
        return $result;
    }

}
