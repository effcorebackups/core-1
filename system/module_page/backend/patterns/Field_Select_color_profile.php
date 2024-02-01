<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Field_Select_color_profile extends Field_Select {

    public $title = 'Color profile';
    public $title__not_selected = '- no -';
    public $attributes = ['data-type' => 'color_profile'];
    public $element_attributes = [
        'name'     => 'color_profile',
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
        $profiles = Color_profile::get_all();
        Core::array_sort_by_string($profiles);
        foreach ($profiles as $c_profile) {
            if ($c_profile->is_user_selectable) {
                $result[$c_profile->id] = $c_profile->title;
            }
        }
        return $result;
    }

}
