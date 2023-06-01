<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class table_body_row extends markup {

    public $tag_name = 'tr';

    function __construct($attributes = [], $children = [], $weight = 0) {
        parent::__construct(null, $attributes, $children, $weight);
    }

    function child_insert($child, $id = null) {
        if ($child instanceof table_body_row_cell)
             return parent::child_insert(                            $child,  $id);
        else return parent::child_insert(new table_body_row_cell([], $child), $id);
    }

}
