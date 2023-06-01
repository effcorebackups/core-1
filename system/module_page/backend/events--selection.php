<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\page;

use effcore\markup;

abstract class events_selection {

    static function handler_page_link($c_row_id, $c_row, $c_instance, $settings = []) {
        if (strpos($c_row['url']['value'], '%%_') === false)
             return new markup('a', ['href' => $c_row['url']['value'], 'target' => '_blank'], $c_row['url']['value']);
        else return                                                                           $c_row['url']['value'];
    }

}
