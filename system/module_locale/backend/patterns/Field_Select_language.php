<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Field_Select_language extends Field_Select {

    public $title = 'Language';
    public $title__not_selected = '- select -';
    public $attributes = ['data-type' => 'language'];
    public $element_attributes = [
        'name'     => 'lang_code',
        'required' => true
    ];

    function build() {
        if (!$this->is_builded) {
            parent::build();
            $items = [];
            $languages = Language::get_all();
            Core::array_sort_by_string($languages, 'title_en', Core::SORT_DSC, false);
            $languages = ['en' => $languages['en']] + $languages;
            foreach ($languages as $c_code => $c_info)
                $items[$c_code] = new Text_simple(
                    $c_code !== 'en' ? $c_info->title_en.' / '.$c_info->title_native.' ('.$c_code.')' :
                                       $c_info->title_en.                            ' ('.$c_code.')');
            $this->items = ['not_selected' => $this->title__not_selected] + $items;
            $this->is_builded = false;
            parent::build();
        }
    }

}
