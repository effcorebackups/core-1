<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\locale;

use effcore\language;
use effcore\message;
use effcore\page;
use effcore\storage;

abstract class events_form_locale_by_language {

    static function on_init($event, $form, $items) {
        $formats = language::get(page::get_current()->args_get('lang_code'))->formats_get();
        $items['#date'               ]->value_set($formats['date']);
        $items['#time'               ]->value_set($formats['time']);
        $items['#datetime'           ]->value_set($formats['datetime']);
        $items['#thousands_separator']->value_set($formats['thousands_separator']);
        $items['#decimal_point'      ]->value_set($formats['decimal_point']);
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'save':
                $result = storage::get('data')->changes_insert('locale', 'update', 'settings/locale/formats/'.page::get_current()->args_get('lang_code'), [
                    'date'                => $items['#date'               ]->value_get(),
                    'time'                => $items['#time'               ]->value_get(),
                    'datetime'            => $items['#datetime'           ]->value_get(),
                    'thousands_separator' => $items['#thousands_separator']->value_get(),
                    'decimal_point'       => $items['#decimal_point'      ]->value_get() ]);
                if ($result) message::insert('Changes was saved.'             );
                else         message::insert('Changes was not saved!', 'error');
                break;
            case 'reset':
                $result = storage::get('data')->changes_delete('locale', 'update', 'settings/locale/formats/'.page::get_current()->args_get('lang_code'));
                if ($result) static::on_init(null, $form, $items);
                if ($result) message::insert('Changes was deleted.'             );
                else         message::insert('Changes was not deleted!', 'error');
                break;
        }
    }

}
