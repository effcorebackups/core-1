<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\locale;

use effcore\Data;
use effcore\Language;
use effcore\Locale;
use effcore\Message;
use effcore\Page;
use effcore\Translation;

abstract class Events_Form_Locale_by_language {

    static function on_build($event, $form) {
        $form->_lang_code = Page::get_current()->args_get('lang_code');
        if (Page::get_current()->args_get('lang_code') === 'en') {
            $form->child_delete('translations');
        }
    }

    static function on_init($event, $form, $items) {
        $formats = Language::get($form->_lang_code)->formats_get();
        $items['#date'               ]->value_set($formats['date']);
        $items['#time'               ]->value_set($formats['time']);
        $items['#datetime'           ]->value_set($formats['datetime']);
        $items['#thousands_separator']->value_set($formats['thousands_separator']);
        $items['#decimal_point'      ]->value_set($formats['decimal_point']);
        if ($form->_lang_code !== 'en') {
            $translations = Data::select('translations--'.$form->_lang_code);
            if ($translations instanceof Translation) {
                $items['#translations']->value_data_set(
                    $translations->data, 'data'
                );
            }
        }
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'save':
                $result = Locale::changes_store(['formats' => [Page::get_current()->args_get('lang_code') => [
                    'date'                => $items['#date'               ]->value_get(),
                    'time'                => $items['#time'               ]->value_get(),
                    'datetime'            => $items['#datetime'           ]->value_get(),
                    'thousands_separator' => $items['#thousands_separator']->value_get(),
                    'decimal_point'       => $items['#decimal_point'      ]->value_get() ]]]);
                if ($form->_lang_code !== 'en') {
                    $translations = $items['#translations']->value_data_get()->data ?? [];
                    if ($translations) {
                        $data = new Translation;
                        $data->code = $form->_lang_code;
                        $data->data = $translations;
                        $result&= Data::update('translations--'.$form->_lang_code, $data);
                    } else        Data::delete('translations--'.$form->_lang_code);
                }
                if ($result) Message::insert('Changes was saved.'             );
                else         Message::insert('Changes was not saved!', 'error');
                break;
            case 'reset':
                $result = Locale::changes_store(['formats' => [Page::get_current()->args_get('lang_code') => null]]);
                if ($result) Message::insert('Changes was deleted.'             );
                else         Message::insert('Changes was not deleted!', 'error');
                $form->components_init();
                break;
        }
    }

}
