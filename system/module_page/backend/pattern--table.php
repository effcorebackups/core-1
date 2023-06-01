<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class table extends markup {

    public $tag_name = 'table';

    function __construct($attributes = [], $tbody = [], $thead = [], $weight = 0) {
        parent::__construct(null, $attributes, [], $weight);
        if (is_object($thead) === true && $thead instanceof table_head) $this->child_insert(                   $thead,  'head');
        if (is_object($thead) !== true                                ) $this->child_insert(new table_head([], $thead), 'head');
        if (is_object($thead) === true && $tbody instanceof table_body) $this->child_insert(                   $tbody,  'body');
        if (is_object($thead) !== true                                ) $this->child_insert(new table_body([], $tbody), 'body');
    }

}
