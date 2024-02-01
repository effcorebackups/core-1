<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Area_group extends Markup {

    public $tag_name = 'x-area-group';
    public $tag_name_real;
    public $attributes = [
        'data-area-group' => true
    ];

    public $id;
    public $title;
    public $type; # null | flex | grid-2x | grid-3x | grid-4x | grid-5x
    public $manage_mode; # null | block_filling | customization
    public $states = [];

    function build() {
        if (!$this->is_builded) {
            if ($this->id  ) $this->attribute_insert('data-id',   $this->id  );
            if ($this->type) $this->attribute_insert('data-type', $this->type);
            $this->is_builded = true;
        }
    }

    function render() {
        $this->build();
        return parent::render();
    }

    function states_get() {
        return $this->states;
    }

    function states_set($states, $rebuild = false) {
        $this->states = $states;
        if ($rebuild) {
            $this->is_builded = false;
            $this->build();
        }
    }

    function manage_mode_enable($mode, $tag_name = 'div') {
        $this->manage_mode   = $mode;
        $this->tag_name_real = $this->tag_name;
        $this->tag_name      = $tag_name;
    }

}
