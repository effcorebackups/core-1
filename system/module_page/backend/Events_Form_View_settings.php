<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\page;

use effcore\Message;
use effcore\Module;
use effcore\Page;

abstract class Events_Form_View_settings {

    static function on_init($event, $form, $items) {
        $settings = Module::settings_get('page');
        $items['#width_min'    ]->value_set($settings->page_width_min    );
        $items['#width_mobile' ]->value_set($settings->page_width_mobile );
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
                if (!$form->has_error()) {
                    if ($items['#width_mobile']->value_get() < $items['#width_min']->value_get() ||
                        $items['#width_mobile']->value_get() > $items['#width_max']->value_get()) {
                        $items['#width_mobile']->error_set();
                        $form->error_set('The width for transition to mobile view must not be less than the "Minimum Width" and greater than the "Maximum Width"!');
                    }
                }
                break;
        }
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'save':
                $result = Page::changes_store([
                    'page_width_min'     => (int)$items['#width_min'    ]->value_get(),
                    'page_width_mobile'  => (int)$items['#width_mobile' ]->value_get(),
                    'page_width_max'     => (int)$items['#width_max'    ]->value_get(),
                    'page_meta_viewport' =>      $items['#meta_viewport']->value_get() ]);
                if ($result) Message::insert('Changes was saved.'             );
                else         Message::insert('Changes was not saved!', 'error');
                break;
            case 'reset':
                $result = Page::changes_store([
                    'page_width_min'     => null,
                    'page_width_mobile'  => null,
                    'page_width_max'     => null,
                    'page_meta_viewport' => null ]);
                if ($result) Message::insert('Changes was deleted.'             );
                else         Message::insert('Changes was not deleted!', 'error');
                $form->components_init();
                break;
        }
    }

}
