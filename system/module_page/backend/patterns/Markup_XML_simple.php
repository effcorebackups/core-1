<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Markup_XML_simple extends Markup_simple {

    public $template = 'markup_xml_simple';

    function render_attributes() {
        return Template_markup::attributes_render($this->attributes_select(), true);
    }

}
