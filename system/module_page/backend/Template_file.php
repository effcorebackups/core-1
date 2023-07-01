<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Template_file extends Template_text {

    public $path = '';

    function render() {
        $path = Module::get($this->module_id)->path.$this->path;
        $file = new File($path);
        $this->data = $file->load();
        return parent::render();
    }

    ###########################
    ### static declarations ###
    ###########################

    static function copied_properties_get() {
        return ['path' => 'path'] + parent::copied_properties_get();
    }

}
