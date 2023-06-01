<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\demo;

use effcore\markup;
use effcore\text;
use stdClass;

abstract class events_selection {

    static function on_selection_build_before_demo_selection_field_types($event, $selection) {
        # field 'checkbox' example
        $selection->fields['checkboxes']['type_checkbox_from_code'] = new stdClass;
        $selection->fields['checkboxes']['type_checkbox_from_code']->weight = 390;
        $selection->fields['checkboxes']['type_checkbox_from_code']->title = new text('Type "%%_type" from code', ['type' => 'checkbox']);
        $selection->fields['checkboxes']['type_checkbox_from_code']->settings = ['name' => 'is_checked[]'];
        # field (main entity) example
        $selection->fields['main']['type_field_from_code'] = new stdClass;
        $selection->fields['main']['type_field_from_code']->weight = 370;
        $selection->fields['main']['type_field_from_code']->title = new text('Type "%%_type" from code', ['type' => 'field']);
        $selection->fields['main']['type_field_from_code']->entity_field_name = 'id';
        # field 'join' example
        $selection->fields['join']['type_join_from_code'] = new stdClass;
        $selection->fields['join']['type_join_from_code']->type = 'left outer join';
        $selection->fields['join']['type_join_from_code']->entity_name          = 'demo_join';
        $selection->fields['join']['type_join_from_code']->entity_field_name    = 'id_data';
        $selection->fields['join']['type_join_from_code']->on_entity_name       = 'demo_data';
        $selection->fields['join']['type_join_from_code']->on_entity_field_name = 'id';
        $selection->fields['join']['type_join_from_code']->fields = [
            'email' => (object)[
                'entity_field_name' => 'email',
                'title'             => new text('Type "%%_type" from code', ['type' => 'join field']),
                'weight'            => 350,
            ]
        ];
        # field 'text' examples
        $selection->fields['texts']['type_text_with_translation_from_code'] = new stdClass;
        $selection->fields['texts']['type_text_with_translation_from_code']->weight = 330;
        $selection->fields['texts']['type_text_with_translation_from_code']->title = new text('Type "%%_type" from code', ['type' => 'text + translation']);
        $selection->fields['texts']['type_text_with_translation_from_code']->text = 'text with translation';
        $selection->fields['texts']['type_text_with_translation_from_code']->is_trimmed = true;
        $selection->fields['texts']['type_text_with_translation_from_code']->is_apply_translation = true;
        $selection->fields['texts']['type_text_with_translation_with_token_from_code'] = new stdClass;
        $selection->fields['texts']['type_text_with_translation_with_token_from_code']->weight = 310;
        $selection->fields['texts']['type_text_with_translation_with_token_from_code']->title = new text('Type "%%_type" from code', ['type' => 'text + translation + token']);
        $selection->fields['texts']['type_text_with_translation_with_token_from_code']->text = 'text with translation and token demo_text = "%%_demo_text"';
        $selection->fields['texts']['type_text_with_translation_with_token_from_code']->is_trimmed = true;
        $selection->fields['texts']['type_text_with_translation_with_token_from_code']->is_apply_translation = true;
        $selection->fields['texts']['type_text_with_translation_with_token_from_code']->is_apply_tokens = true;
        $selection->fields['texts']['type_text_with_token_from_code'] = new stdClass;
        $selection->fields['texts']['type_text_with_token_from_code']->weight = 290;
        $selection->fields['texts']['type_text_with_token_from_code']->title = new text('Type "%%_type" from code', ['type' => 'text + token']);
        $selection->fields['texts']['type_text_with_token_from_code']->text = 'text with token demo_text = "%%_demo_text"';
        $selection->fields['texts']['type_text_with_token_from_code']->is_trimmed = true;
        $selection->fields['texts']['type_text_with_token_from_code']->is_apply_tokens = true;
        # field 'markup' example
        $selection->fields['markup']['type_markup_from_code'] = new stdClass;
        $selection->fields['markup']['type_markup_from_code']->weight = 270;
        $selection->fields['markup']['type_markup_from_code']->title = new text('Type "%%_type" from code', ['type' => 'markup']);
        $selection->fields['markup']['type_markup_from_code']->markup = new markup('span', [], 'markup');
        # field 'handler' example
        $selection->fields['handlers']['type_handler_from_code'] = new stdClass;
        $selection->fields['handlers']['type_handler_from_code']->weight = 250;
        $selection->fields['handlers']['type_handler_from_code']->title = new text('Type "%%_type" from code', ['type' => 'handler']);
        $selection->fields['handlers']['type_handler_from_code']->settings = ['demo_value' => 'value from handler settings'];
        $selection->fields['handlers']['type_handler_from_code']->handler = '\\effcore\\modules\\demo\\events_selection::demo_handler';
        # field 'code' example
        $selection->fields['code']['type_code_from_code'] = new stdClass;
        $selection->fields['code']['type_code_from_code']->weight = 100;
        $selection->fields['code']['type_code_from_code']->title = new text('Type "%%_type" from code', ['type' => 'code']);
        $selection->fields['code']['type_code_from_code']->settings = ['demo_value' => 'value from code settings'];
        $selection->fields['code']['type_code_from_code']->closure = function ($c_row_id, $c_row, $c_instance, $settings = []) {
            return new markup('span', [], $settings['demo_value']);
        };
    }

    static function demo_handler($c_row_id, $c_row, $c_instance, $settings = []) {
        return new markup('span', [], $settings['demo_value']);
    }

}
