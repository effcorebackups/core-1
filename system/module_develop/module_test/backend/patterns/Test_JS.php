<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Test_JS extends Test {

    public $type = 'js';
    public $path;

    function prepare() {
        if ($this->path) {
            if (!Frontend::select('class_current__test')) {
                 Frontend::insert('class_current__test', 'class_current_path', null, 'scripts', [
                     'content' => 'window.test_current_path = "'.Frontend::path_resolve($this->path, $this->module_id, true).'";',
                     'weight' => +500], $this->module_id);
                 Frontend::insert('class_current__test', 'class_current', null, 'scripts', [
                     'path' => $this->path,
                     'attributes' => [
                         'type' => 'module'],
                     'weight' => +500], $this->module_id);
            }
        }
    }

}
