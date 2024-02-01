<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Test_feed__Core__Serialize {

    public $prop_string        = 'string';
    public $prop_integer       = 123;
    public $prop_float         = 0.000001;
    public $prop_boolean_true  = true;
    public $prop_boolean_false = false;
    public $prop_null          = null;
    public $prop_array         = [
        null                  => 'key null',
        'string'              => 'key string',
        123                   => 'key integer',
      # 0.000001              => 'key float',
      # true                  => 'key boolean:true',
      # false                 => 'key boolean:false',
        'value_string'        => 'string',
        'value_integer'       => 123,
        'value_float'         => 0.000001,
        'value_boolean_true'  => true,
        'value_boolean_false' => false,
        'value_null'          => null,
        'value_array_empty'   => []
    ];

}
