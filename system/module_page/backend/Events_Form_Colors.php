<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\page;

use effcore\Color_preset;
use effcore\Group_Palette;
use effcore\Message;
use effcore\Module;

abstract class Events_Form_Colors {

    static function on_init($event, $form, $items) {
        $settings = Module::settings_get('page');
        foreach ($items as $c_item) {
            if ($c_item instanceof Group_Palette) {
                if (strpos($c_item->name_get_complex(), 'color__') === 0) {
                    $c_item->value_set(
                        $settings->{$c_item->name_get_complex()}
                    );
                }
            }
        }
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'save':
                $selected = [];
                foreach ($items as $c_item) {
                    if ($c_item instanceof Group_Palette) {
                        if ( strpos($c_item->name_get_complex(), 'color__') === 0 ) {
                            $selected[$c_item->name_get_complex()] = $c_item->value_get();
                        }
                    }
                }
                $result = Color_preset::apply_with_custom_ids($selected, true);
                if ($result) Message::insert('Changes was saved.'             );
                else         Message::insert('Changes was not saved!', 'error');
                break;
            case 'reset':
                $result = Color_preset::reset();
                if ($result) Message::insert('Changes was deleted.'             );
                else         Message::insert('Changes was not deleted!', 'error');
                static::on_init(null, $form, $items);
                break;
        }
    }

}
