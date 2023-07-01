<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Page_hybrid extends Page {

    const IS_LOADING_WAS_NOT            = 0b00;
    const IS_LOADING_WAS_NOT_SUCCESSFUL = 0b01;
    const IS_LOADING_WAS_____SUCCESSFUL = 0b10;

    public $origin = 'hybrid';
    public $is_loaded = self::IS_LOADING_WAS_NOT;

    function load_from___sql_storage() {
        if (!$this->is_loaded) {
            $instance = (new Instance('page', ['id' => $this->id]))->select();
            if ($instance) {
                foreach ($instance->values_get() as $c_key => $c_value)
                   $this->                         {$c_key} = $c_value;
                   $this->is_loaded = static::IS_LOADING_WAS_____SUCCESSFUL;
            } else $this->is_loaded = static::IS_LOADING_WAS_NOT_SUCCESSFUL;
        }
        return $this;
    }

}
