<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class table_body_row_cell extends markup {

    public $tag_name = 'td';

    function __construct($attributes = [], $children = [], $weight = 0) {
        parent::__construct(null, $attributes, $children, $weight);
    }

    function child_insert($child, $id = null) {
        if (is_string($child) || is_numeric($child)) return parent::child_insert(new text($child), $id);
        else                                         return parent::child_insert(         $child,  $id);
    }

}
