<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Markup_XML extends Markup {

    public $template = 'markup_xml';

    function render_attributes() {
        return Template_markup::attributes_render($this->attributes_select(), true);
    }

}
