<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

use stdClass;

class Field_Select_block_preset extends Field_Select {

    public $title = 'Block preset';
    public $title__not_selected = '- select -';
    public $attributes = ['data-type' => 'block_preset'];
    public $element_attributes = [
        'name'     => 'block_preset',
        'required' => true];
    public $id_area = null;

    function build() {
        if (!$this->is_builded) {
            parent::build();
            $items = [];
            $presets = Block_preset::select_all($this->id_area);
            Core::array_sort_by_string($presets, 'managing_group');
            foreach ($presets as $c_preset) {
                $c_group_id = Core::sanitize_id($c_preset->managing_group);
                if (!isset($items[$c_group_id])) {
                           $items[$c_group_id] = new stdClass;
                           $items[$c_group_id]->title = $c_preset->managing_group; }
                $c_text_object = new Text_multiline(['title' => $c_preset->managing_title, 'id' => '('.$c_preset->id.')'], [], ' ');
                $c_text_object->_text_translated = $c_text_object->render();
                $items[$c_group_id]->items[$c_preset->id] = $c_text_object;
            }
            foreach ($items as $c_group) {
                if ($c_group instanceof stdClass) {
                    Core::array_sort_by_string($c_group->items, '_text_translated', Core::SORT_DSC, false);
                }
            }
            $this->items = ['not_selected' => $this->title__not_selected] + $items;
            $this->is_builded = false;
            parent::build();
        }
    }

}
