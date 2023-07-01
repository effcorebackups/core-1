<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\page;

use effcore\Markup;

abstract class Events_Selection {

    static function handler_page_link($c_row_id, $c_row, $c_instance, $settings = []) {
        if (strpos($c_row['url']['value'], '%%_') === false)
             return new Markup('a', ['href' => $c_row['url']['value'], 'target' => '_blank'], $c_row['url']['value']);
        else return                                                                           $c_row['url']['value'];
    }

}
