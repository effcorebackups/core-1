<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Markup_Table_body_row extends Markup {

    public $tag_name = 'tr';

    function __construct($attributes = [], $children = [], $weight = +0) {
        parent::__construct(null, $attributes, $children, $weight);
    }

    function child_insert($child, $id = null) {
        if ($child instanceof Markup_Table_body_row_cell)
             return parent::child_insert(                                   $child , $id);
        else return parent::child_insert(new Markup_Table_body_row_cell([], $child), $id);
    }

}
