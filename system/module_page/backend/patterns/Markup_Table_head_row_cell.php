<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Markup_Table_head_row_cell extends Markup {

    public $tag_name = 'th';

    function __construct($attributes = [], $children = [], $weight = +0) {
        parent::__construct(null, $attributes, $children, $weight);
    }

    function child_insert($child, $id = null) {
        if (is_string($child) || is_numeric($child)) return parent::child_insert(new Text($child), $id);
        else                                         return parent::child_insert(         $child , $id);
    }

}
