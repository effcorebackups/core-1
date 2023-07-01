<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\captcha;

use effcore\Canvas_SVG;
use effcore\Captcha;
use effcore\Core;
use effcore\Field_Checkbox;
use effcore\Frontend;
use effcore\Glyph;
use effcore\Markup;
use effcore\Message;
use effcore\Storage;
use effcore\Text;

abstract class Events_Form_Security_settings_captcha {

    static function on_init($event, $form, $items) {
        if (!Frontend::select('form_all__captcha'))
             Frontend::insert('form_all__captcha', null, 'styles', ['path' => 'frontend/captcha.css', 'attributes' => ['rel' => 'stylesheet', 'media' => 'all'], 'weight' => -300], 'form_style', 'captcha');
        $settings_captcha = Captcha::settings_get();
        $items['#length']->value_set($settings_captcha->length);
        $items['main/glyphs']->children_delete();
        $glyphs_all = Glyph::get_all();
        Core::array_sort_by_string($glyphs_all, 'character');
        foreach ($glyphs_all as $c_row_id => $c_item) {
            $c_sizes = Glyph::get_sizes($c_item->glyph);
            $c_canvas = new Canvas_SVG($c_sizes->width + 2, $c_sizes->height + 2, 6);
            $c_canvas->glyph_set($c_item->glyph, 1, 1);
            $c_field_is_enabled = new Field_Checkbox;
            $c_field_is_enabled->build();
            $c_field_is_enabled->name_set('is_enabled_glyph[]');
            $c_field_is_enabled->value_set($c_row_id);
            $c_field_is_enabled->checked_set(isset($settings_captcha->glyphs[$c_row_id]));
            $c_markup = new Markup('x-glyph-settings');
            $c_markup->child_insert($c_canvas, 'canvas');
            $c_markup->child_insert($c_field_is_enabled, 'field_is_enabled');
            $items['main/glyphs']->child_insert($c_markup, $c_row_id);
        }
    }

    static function on_validate($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'save':
                $has_choice = false;
                foreach (Glyph::get_all() as $c_row_id => $c_item)
                    if ($items['#is_enabled_glyph:'.$c_row_id]->checked_get())
                        $has_choice = true;
                if ($has_choice === false) {
                    $form->error_set('Group "%%_title" should contain at least one selected item!', ['title' => (new Text($items['main/glyphs']->title))->render() ]);
                }
                break;
        }
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'save':
                $row_ids = [];
                foreach (Glyph::get_all() as $c_row_id => $c_item)
                    if ($items['#is_enabled_glyph:'.$c_row_id]->checked_get())
                        $row_ids[$c_row_id] = $c_row_id;
                $result = Storage::get('data')->changes_insert('captcha', 'update', 'settings/captcha/captcha_length', (int)$items['#length']->value_get(), false);
                $result&= Storage::get('data')->changes_insert('captcha', 'update', 'settings/captcha/captcha_glyphs', $row_ids);
                if ($result) Message::insert('Changes was saved.'             );
                else         Message::insert('Changes was not saved!', 'error');
                if ($result) {
                    Captcha::delete_all();
                }
                break;
            case 'reset':
                $result = Storage::get('data')->changes_delete('captcha', 'update', 'settings/captcha/captcha_length', false);
                $result&= Storage::get('data')->changes_delete('captcha', 'update', 'settings/captcha/captcha_glyphs');
                if ($result) Message::insert('Changes was deleted.'             );
                else         Message::insert('Changes was not deleted!', 'error');
                if ($result) {
                    Captcha::delete_all();
                    static::on_init(null, $form, $items);
                }
                break;
        }
    }

}
