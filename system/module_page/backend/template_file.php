<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class template_file extends template_text {

    public $path = '';

    function render() {
        $path = module::get($this->module_id)->path.$this->path;
        $file = new file($path);
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
