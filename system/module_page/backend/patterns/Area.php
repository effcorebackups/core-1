<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Area extends Markup {

    public $tag_name = 'x-area';
    public $tag_name_real;
    public $attributes = [
        'data-area' => true
    ];

    public $id;
    public $title;
    public $manage_mode; # null | block_filling | customization
    public $render_weight = +0;
    public $states = [];

    function build() {
        if (!$this->is_builded) {
            if ($this->id) {
                $this->attribute_insert('data-id', $this->id);
                if ($this->manage_mode === 'block_filling' ||
                    $this->manage_mode === 'customization') {
                    $this->child_insert(
                        new Markup('x-area-info', [], [
                            'id'       => new Markup('x-area-id'      , [], new Text_simple($this->id)),
                            'tag_name' => new Markup('x-area-tag-name', [], new Text_simple($this->tag_name_real))
                        ]), 'id'
                    );
                }
            }
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
