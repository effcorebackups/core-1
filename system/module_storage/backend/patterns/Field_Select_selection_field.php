<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

use stdClass;

#[\AllowDynamicProperties]

class Field_Select_selection_field extends Field_Select {

    public $title = 'Selection field';
    public $title__not_selected = '- select -';
    public $attributes = ['data-type' => 'selection_field'];
    public $element_attributes = [
        'name'     => 'selection_field',
        'required' => true
    ];

    function build($filter = null) {
        if (!$this->is_builded) {
            $this->items = ['not_selected' => $this->title__not_selected] + static::items_generate($filter);
            parent::build();
        }
    }

    function value_get_parsed() {
        return static::parse_value($this->value_get());
    }

    ###########################
    ### static declarations ###
    ###########################

    static function items_generate($filter) {
        $result = [];
        # universal handlers
        $result['*'] = new stdClass;
        $result['*']->title = 'universal handlers';
        foreach (Selection::get_handlers('*') as $c_row_id => $c_handler) {
            $result['*']->items['handler:'.$c_row_id] = (new Text_multiline([
                'H: ', $c_handler->title ?? $c_row_id
            ], [], ''))->render();
        }
        # fields by entity
        $entities = Entity::get_all();
        Core::array_sort_by_string($entities);
        foreach ($entities as $c_entity) {
            if (!empty($c_entity->managing_is_enabled)) {
                if ($filter === null ||
                    $filter === $c_entity->name) {
                    foreach ($c_entity->fields as $c_name => $c_field) {
                        if (!isset($result[$c_entity->name])) {
                                   $result[$c_entity->name] = new stdClass;
                                   $result[$c_entity->name]->title = $c_entity->title; }
                        $result[$c_entity->name]->items['main:'.$c_entity->name.'.'.$c_name] = (new Text_multiline([
                            'title' => $c_field->title, 'id' => '(~'.$c_entity->name.'.'.$c_name.')'
                        ], [], ' '))->render();
                    }
                    Core::array_sort(
                        $result[$c_entity->name]->items, Core::SORT_DSC, false
                    );
                    # handlers by entity
                    foreach (Selection::get_handlers($c_entity->name) as $c_row_id => $c_handler) {
                        $result[$c_entity->name]->items['handler:'.$c_row_id] = (new Text_multiline([
                            'H: ', $c_handler->title ?? $c_row_id
                        ], [], ''))->render();
                    }
                }
            }
        }
        return $result;
    }

    static function parse_value($value) {
        if (is_string($value) && strlen($value)) {
            $matches = [];
            preg_match('%^(?<type_m>main)'   .'[:]'.'(?<entity_name>[a-z_]+)[.](?<entity_field_name>[a-z_]+)'.'$|'.
                        '^(?<type_h>handler)'.'[:]'.'(?<handler_row_id>[a-z_]+)'.'$%', $value, $matches);
            $result = new stdClass;
            if (strlen($matches['type_m'])) {
                $result->type = 'main';
                $result->entity_name       = $matches['entity_name'];
                $result->entity_field_name = $matches['entity_field_name'];
                return $result;
            }
            if (strlen($matches['type_h'])) {
                $result->type = 'handler';
                $result->handler_row_id = $matches['handler_row_id'];
                return $result;
            }
        }
        return null;
    }

}
