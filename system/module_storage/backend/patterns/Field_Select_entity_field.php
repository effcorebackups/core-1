<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

use stdClass;

#[\AllowDynamicProperties]

class Field_Select_entity_field extends Field_Select {

    public $title = 'Entity field';
    public $title__not_selected = '- select -';
    public $attributes = ['data-type' => 'entity_field'];
    public $element_attributes = [
        'name'     => 'entity_field',
        'required' => true
    ];

    function build($filter = null) {
        if (!$this->is_builded) {
            parent::build();
            $items = [];
            $entities = Entity::get_all();
            Core::array_sort_by_string($entities);
            foreach ($entities as $c_entity) {
                if (!empty($c_entity->managing_is_enabled)) {
                    if ($filter === null ||
                        $filter === $c_entity->name) {
                        foreach ($c_entity->fields as $c_name => $c_field) {
                            if (!isset($items[$c_entity->name])) {
                                       $items[$c_entity->name] = new stdClass;
                                       $items[$c_entity->name]->title = $c_entity->title; }
                            $c_text_object = new Text_multiline(['title' => $c_field->title, 'id' => '(~'.$c_entity->name.'.'.$c_name.')'], [], ' ');
                            $c_text_object->_text_translated = $c_text_object->render();
                            $items[$c_entity->name]->items[$c_entity->name.'.'.$c_name] = $c_text_object;
                        }
                        Core::array_sort_by_string(
                            $items[$c_entity->name]->items, '_text_translated', Core::SORT_DSC, false
                        );
                    }
                }
            }
            $this->items = ['not_selected' => $this->title__not_selected] + $items;
            $this->is_builded = false;
            parent::build();
        }
    }

    function value_get_parsed() {
        return static::parse_value($this->value_get());
    }

    ###########################
    ### static declarations ###
    ###########################

    static function parse_value($value) {
        $parsed = is_string($value) && strlen($value) && str_contains($value, '.') ? explode('.', $value) : null;
        if (is_array($parsed) && count($parsed) === 2)
            return ['entity_name' => $parsed[0],
              'entity_field_name' => $parsed[1]];
        else return null;
    }

}
