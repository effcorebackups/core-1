<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Block_preset_link {

    public $id;
    public $weight = 0;

    function __construct($id = null, $weight = 0) {
        if ($id    ) $this->id     = $id;
        if ($weight) $this->weight = $weight;
    }

    function preset_make() {
        $preset = Core::deep_clone(Block_preset::select($this->id));
        if ($preset) {
            foreach ($this as $c_key => $c_value)
                $preset->{$c_key} =
                $this  ->{$c_key};
            return $preset;
        }
    }

    function block_make() {
        $preset = $this->preset_make();
        if ($preset) {
            return $preset->block_make();
        }
    }

}
