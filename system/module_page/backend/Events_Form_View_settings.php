<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\page;

use effcore\Message;
use effcore\Module;
use effcore\Storage;

abstract class Events_Form_View_settings {

    static function on_init($event, $form, $items) {
        $settings = Module::settings_get('page');
        $items['#width_min'    ]->value_set($settings->page_width_min    );
        $items['#width_max'    ]->value_set($settings->page_width_max    );
        $items['#meta_viewport']->value_set($settings->page_meta_viewport);
    }

    static function on_validate($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'save':
                if (!$form->has_error()) {
                    if ($items['#width_min']->value_get() >
                        $items['#width_max']->value_get()) {
                        $items['#width_min']->error_set();
                        $items['#width_max']->error_set();
                        $form->error_set('The minimum value cannot be greater than the maximum!');
                    }
                }
                break;
        }
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'save':
                $result = Storage::get('data')->changes_insert('page', 'update', 'settings/page/page_width_min',     (int)$items['#width_min'    ]->value_get(), false);
                $result&= Storage::get('data')->changes_insert('page', 'update', 'settings/page/page_width_max',     (int)$items['#width_max'    ]->value_get(), false);
                $result&= Storage::get('data')->changes_insert('page', 'update', 'settings/page/page_meta_viewport',      $items['#meta_viewport']->value_get()       );
                if ($result) Message::insert('Changes was saved.'             );
                else         Message::insert('Changes was not saved!', 'error');
                break;
            case 'reset':
                $result = Storage::get('data')->changes_delete('page', 'update', 'settings/page/page_width_min', false);
                $result&= Storage::get('data')->changes_delete('page', 'update', 'settings/page/page_width_max', false);
                $result&= Storage::get('data')->changes_delete('page', 'update', 'settings/page/page_meta_viewport'   );
                if ($result) Message::insert('Changes was deleted.'             );
                else         Message::insert('Changes was not deleted!', 'error');
                static::on_init(null, $form, $items);
                break;
        }
    }

}
