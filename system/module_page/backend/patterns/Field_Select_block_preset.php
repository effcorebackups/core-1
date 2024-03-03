<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

use stdClass;

#[\AllowDynamicProperties]

class Field_Select_block_preset extends Field_Select {

    public $title = 'Block preset';
    public $title__not_selected = '- select -';
    public $attributes = [
        'data-type' => 'block_preset'];
    public $element_attributes = [
        'name'     => 'block_preset',
        'required' => true];
    public $id_area = null;

    function build() {
        if (!$this->is_builded) {
            $this->items = ['not_selected' => $this->title__not_selected] + static::items_generate($this->id_area);
            parent::build();
        }
    }

    ###########################
    ### static declarations ###
    ###########################

    static function items_generate($id_area) {
        $result = [];
        $presets = Block_preset::select_all($id_area);
        Core::array_sort_by_string($presets, 'managing_group');
        foreach ($presets as $c_preset) {
            $c_group_id = Security::sanitize_id($c_preset->managing_group);
            if (!isset($result[$c_group_id])) {
                       $result[$c_group_id] = new stdClass;
                       $result[$c_group_id]->title = $c_preset->managing_group; }
            $result[$c_group_id]->items[$c_preset->id] = (new Text_multiline([
                'title' => $c_preset->managing_title, 'id' => '('.$c_preset->id.')'
            ], [], ' '))->render();
        }
        foreach ($result as $c_group) {
            if ($c_group instanceof stdClass) {
                Core::array_sort($c_group->items, Core::SORT_DSC, false);
            }
        }
        return $result;
    }

}
