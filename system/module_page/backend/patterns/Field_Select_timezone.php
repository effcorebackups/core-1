<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

use DateTimeZone;
use stdClass;

class Field_Select_timezone extends Field_Select {

    public $title = 'Time zone';
    public $title__not_selected = '- select -';
    public $sort = 'by_zones'; # by_zones | by_names
    public $attributes = ['data-type' => 'timezone'];
    public $element_attributes = [
        'name'     => 'timezone',
        'required' => true
    ];

    function build() {
        if (!$this->is_builded) {
            parent::build();
            if ($this->sort === 'by_zones') $this->items = ['not_selected' => $this->title__not_selected] + static::items_generate_by_zones();
            if ($this->sort === 'by_names') $this->items = ['not_selected' => $this->title__not_selected] + static::items_generate_by_names();
            $this->is_builded = false;
            parent::build();
        }
    }

    ###########################
    ### static declarations ###
    ###########################

    static function items_generate_by_zones() {
        $result = [];
        $buffer = [];
        foreach (DateTimeZone::listIdentifiers() as $c_name)
            $buffer[str_replace(':', '', Core::timezone_get_offset_tme($c_name))][$c_name] = $c_name;
        krsort($buffer, SORT_NUMERIC);
        foreach ($buffer as $c_time_offset => $c_items) {
            if (!isset($result[$c_time_offset])) {
                       $result[$c_time_offset] = new stdClass;
                       $result[$c_time_offset]->title = str_replace('-', '−', Core::timezone_get_offset_tme(reset($c_items))); }
            foreach ($c_items as $c_name) {
                $c_parts = explode('/', $c_name, 2);
                if (count($c_parts) === 2) $c_text_object = new Text_multiline(['region' => $c_parts[0], 'delimiter' => '/', 'country' => str_replace(['_', '/'], ['-', ' / '], $c_parts[1])], [], ' ');
                if (count($c_parts) === 1) $c_text_object = new Text_multiline([                                             'country' => str_replace(['_', '/'], ['-', ' / '], $c_parts[0])], [], ' ');
                $c_text_object->_text_translated = $c_text_object->render();
                $result[$c_time_offset]->items[$c_name] = $c_text_object;
            }
            Core::array_sort_by_string(
                $result[$c_time_offset]->items, '_text_translated', Core::SORT_DSC, false
            );
        }
        return $result;
    }

    static function items_generate_by_names() {
        $result = [];
        foreach (DateTimeZone::listIdentifiers() as $c_name) {
            $c_offset = str_replace('-', '−', Core::timezone_get_offset_tme($c_name));
            $c_parts = explode('/', $c_name, 2);
            if (!isset($result[$c_parts[0]])) {
                       $result[$c_parts[0]] = new stdClass;
                       $result[$c_parts[0]]->title = $c_parts[0];
                       $result[$c_parts[0]]->_text_translated = (new Text($c_parts[0]))->render(); }
            if (count($c_parts) === 2) $c_text_object = new Text_multiline(['country' => str_replace(['_', '/'], ['-', ' / '], $c_parts[1]), 'offset' => '('.$c_offset.')'], [], ' ');
            if (count($c_parts) === 1) $c_text_object = new Text_multiline(['country' => str_replace(['_', '/'], ['-', ' / '], $c_parts[0]), 'offset' => '('.$c_offset.')'], [], ' ');
            $c_text_object->_text_translated = $c_text_object->render();
            $result[$c_parts[0]]->items[$c_name] = $c_text_object;
        }
        foreach ($result as $c_id => $c_group) {
            Core::array_sort_by_string(
                $result[$c_id]->items, '_text_translated', Core::SORT_DSC, false
            );
        }
        Core::array_sort_by_string(
            $result, '_text_translated', Core::SORT_DSC, false
        );
        return $result;
    }

    static function value_to_markup($value) {
        if ($value) {
            $c_parts = explode('/', $value, 2);
            if (count($c_parts) === 2) return new Text_multiline(['region' => $c_parts[0], 'delimiter' => '/', 'country' => str_replace(['_', '/'], ['-', ' / '], $c_parts[1])], [], ' ');
            if (count($c_parts) === 1) return new Text_multiline([                                             'country' => str_replace(['_', '/'], ['-', ' / '], $c_parts[0])], [], ' ');
        }
    }

}
