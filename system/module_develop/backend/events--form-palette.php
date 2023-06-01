<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\develop;

use effcore\color;
use effcore\markup;
use effcore\message;
use effcore\storage_nosql_data;
use effcore\text;

abstract class events_form_palette {

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'generate':
                $base_color = new color(null, $items['#color']->value_get());
                $palette_markup = [];
                $palette_colors = [];
                for ($i = 0; $i < 21; $i++) {
                    $c_multiplier = $items['#multiplier_'.($i < 11 ? 'l' : 'r')]->value_get();
                    $c_offset = ($i - 10) * $c_multiplier;
                    if ($c_offset !== 0) $c_color_id = $items['#prefix']->value_get().($c_offset < 0 ? 'l' : 'r').abs($i - 10);
                    else                 $c_color_id = $items['#prefix']->value_get().'base';
                    $c_color_value = $base_color->filter_shift($c_offset, $c_offset, $c_offset, 1, color::RETURN_HEX);
                    $palette_colors[$c_color_id] = new color($c_color_id, $c_color_value, $items['#group']->value_get());
                    $palette_markup[$c_color_id] = new markup('x-color', ['style' => 'background-color: '.$c_color_value]); }
                $items['palette/report']->child_select('palette')->child_insert(new markup('x-palette', [], $palette_markup), 'palette');
                $items['palette/report']->child_select('data'   )->child_insert(new text(storage_nosql_data::data_to_text($palette_colors, 'colors')), 'data');
                message::insert('Generation done.');
                break;
        }
    }

}
