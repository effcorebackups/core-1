<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Template_code extends Template {

    public $handler = '';

    function render() {
        return call_user_func($this->handler, $this->args, $this->args_raw);
    }

    ###########################
    ### static declarations ###
    ###########################

    static function copied_properties_get() {
        return ['handler' => 'handler'] + parent::copied_properties_get();
    }

}
