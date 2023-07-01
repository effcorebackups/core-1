<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\page;

use effcore\Color_preset;
use effcore\Field_Checkbox_color;
use effcore\Message;
use effcore\Page;

abstract class Events_Form_Colors_preset {

    static function on_init($event, $form, $items) {
        $id = Page::get_current()->args_get('id');
        $preset = Color_preset::get($id);
        if ($preset) {
            foreach ($items as $c_item) {
                if ($c_item instanceof Field_Checkbox_color) {
                    if (strpos($c_item->name_get(), 'color__') === 0) {
                        $c_item->color_set(
                            $preset->colors->{$c_item->name_get()}
                        );
                    }
                }
            }
        } else $items['~apply']->disabled_set(true);
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'apply':
                $changes = [];
                $id = Page::get_current()->args_get('id');
                $preset = Color_preset::get($id);
                if ($preset) {
                    foreach ($items as $c_item) {
                        if ($c_item instanceof Field_Checkbox_color) {
                            if (strpos($c_item->name_get(), 'color__') === 0) {
                                if ($c_item->checked_get()) {
                                    $changes[$c_item->name_get()] = true;
                                }
                            }
                        }
                    }
                    if (!count($changes)) {
                        Message::insert('No one item was selected!', 'warning');
                    } else {
                        $result = Color_preset::apply($id, $changes, true);
                        if ($result) Message::insert('Colors was applied.'             );
                        else         Message::insert('Colors was not applied!', 'error');
                        static::on_init(null, $form, $items);
                    }
                }
                break;
        }
    }

}
