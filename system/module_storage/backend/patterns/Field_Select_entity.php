<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
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
            $this->items = ['not_selected' => $this->title__not_selected] + static::items_generate();
            parent::build();
        }
    }

    ###########################
    ### static declarations ###
    ###########################

    static function items_generate() {
        $result = [];
        foreach (Entity::get_all() as $c_entity) {
            if (!empty($c_entity->managing_is_enabled)) {
                $result[$c_entity->name] = (new Text_multiline([
                    'title' => $c_entity->title, 'id' => '('.$c_entity->name.')'
                ], [], ' '))->render(); }}
        Core::array_sort($result, Core::SORT_DSC, false);
        return $result;
    }

}
