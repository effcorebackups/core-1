<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Field_Select_entity extends Field_Select {

    public $title = 'Entity';
    public $title__not_selected = '- select -';
    public $attributes = ['data-type' => 'entity'];
    public $element_attributes = [
        'name'     => 'entity',
        'required' => true
    ];

    function build() {
        if (!$this->is_builded) {
            parent::build();
            $items = [];
            foreach (Entity::get_all() as $c_entity) {
                if (!empty($c_entity->managing_is_enabled)) {
                    $c_text_object = new Text_multiline(['title' => $c_entity->title, 'id' => '('.$c_entity->name.')'], [], ' ');
                    $c_text_object->_text_translated = $c_text_object->render();
                    $items[$c_entity->name] = $c_text_object; }}
            Core::array_sort_by_string($items, '_text_translated', Core::SORT_DSC, false);
            $this->items = ['not_selected' => $this->title__not_selected] + $items;
            $this->is_builded = false;
            parent::build();
        }
    }

}
