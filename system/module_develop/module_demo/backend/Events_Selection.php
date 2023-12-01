<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\demo;

use effcore\Markup;
use effcore\Text;
use stdClass;

abstract class Events_Selection {

    static function on_selection_build_before_demo_selection_field_types($event, $selection) {
        # field 'handler' with 'checkbox' example
        $selection->fields['handlers']['handler__code1__checkbox_select'] = new stdClass;
        $selection->fields['handlers']['handler__code1__checkbox_select']->title = new Text('Type "%%_type" from code', ['type' => 'handler:checkbox_1']);
        $selection->fields['handlers']['handler__code1__checkbox_select']->settings = ['name' => 'is_checked_code1[]', 'instance_id' => 300];
        $selection->fields['handlers']['handler__code1__checkbox_select']->handler = '\\effcore\\modules\\page\\Events_Selection::handler__any__checkbox_select';
        $selection->fields['handlers']['handler__code1__checkbox_select']->weight = +500;
        $selection->fields['handlers']['handler__code2__checkbox_select'] = new stdClass;
        $selection->fields['handlers']['handler__code2__checkbox_select']->title = new Text('Type "%%_type" from code', ['type' => 'handler:checkbox_2']);
        $selection->fields['handlers']['handler__code2__checkbox_select']->settings = ['name' => 'is_checked_code2[]', 'instance_id' => 400];
        $selection->fields['handlers']['handler__code2__checkbox_select']->handler = '\\effcore\\modules\\page\\Events_Selection::handler__any__checkbox_select';
        $selection->fields['handlers']['handler__code2__checkbox_select']->weight = +500;
        # field (main entity) example
        $selection->fields['main']['main__code__data'] = new stdClass;
        $selection->fields['main']['main__code__data']->title = new Text('Type "%%_type" from code', ['type' => 'field']);
        $selection->fields['main']['main__code__data']->entity_field_name = 'id';
        $selection->fields['main']['main__code__data']->weight = +370;
        # field 'join' example
        $selection->fields['join']['join__code__data'] = new stdClass;
        $selection->fields['join']['join__code__data']->type = 'left outer join';
        $selection->fields['join']['join__code__data']->entity_name = 'demo_join';
        $selection->fields['join']['join__code__data']->entity_field_name = 'id_data';
        $selection->fields['join']['join__code__data']->on_entity_name = 'demo_data';
        $selection->fields['join']['join__code__data']->on_entity_field_name = 'id';
        $selection->fields['join']['join__code__data']->fields = [
            'email' => (object)[
                'entity_field_name' => 'email',
                'title'             => new Text('Type "%%_type" from code', ['type' => 'join field']),
                'weight'            => 350,
            ]
        ];
        # field 'text' examples
        $selection->fields['texts']['text__code__with_translation'] = new stdClass;
        $selection->fields['texts']['text__code__with_translation']->title = new Text('Type "%%_type" from code', ['type' => 'text + translation']);
        $selection->fields['texts']['text__code__with_translation']->text = 'text with translation';
        $selection->fields['texts']['text__code__with_translation']->is_apply_translation = true;
        $selection->fields['texts']['text__code__with_translation']->weight = +330;
        $selection->fields['texts']['text__code__with_translation_and_token'] = new stdClass;
        $selection->fields['texts']['text__code__with_translation_and_token']->title = new Text('Type "%%_type" from code', ['type' => 'text + translation + token']);
        $selection->fields['texts']['text__code__with_translation_and_token']->text = 'text with translation and token demo_text = "%%_demo_text"';
        $selection->fields['texts']['text__code__with_translation_and_token']->is_apply_translation = true;
        $selection->fields['texts']['text__code__with_translation_and_token']->is_apply_tokens = true;
        $selection->fields['texts']['text__code__with_translation_and_token']->weight = +310;
        $selection->fields['texts']['text__code__with_token'] = new stdClass;
        $selection->fields['texts']['text__code__with_token']->title = new Text('Type "%%_type" from code', ['type' => 'text + token']);
        $selection->fields['texts']['text__code__with_token']->text = 'text with token demo_text = "%%_demo_text"';
        $selection->fields['texts']['text__code__with_token']->is_apply_tokens = true;
        $selection->fields['texts']['text__code__with_token']->weight = +290;
        # field 'markup' example
        $selection->fields['markup']['markup__code__data'] = new stdClass;
        $selection->fields['markup']['markup__code__data']->title = new Text('Type "%%_type" from code', ['type' => 'markup']);
        $selection->fields['markup']['markup__code__data']->markup = new Markup('span', [], 'markup');
        $selection->fields['markup']['markup__code__data']->weight = +270;
        # field 'handler' example
        $selection->fields['handlers']['handler__code__text1'] = new stdClass;
        $selection->fields['handlers']['handler__code__text1']->title = new Text('Type "%%_type" from code', ['type' => 'handler']);
        $selection->fields['handlers']['handler__code__text1']->settings = ['demo_value' => 'text with translation'];
        $selection->fields['handlers']['handler__code__text1']->handler = '\\effcore\\modules\\demo\\Events_Selection::handler__demo';
        $selection->fields['handlers']['handler__code__text1']->weight = +250;
        $selection->fields['handlers']['handler__code__text2'] = new stdClass;
        $selection->fields['handlers']['handler__code__text2']->title = new Text('Type "%%_type" from code', ['type' => 'handler+is_apply_translation=false']);
        $selection->fields['handlers']['handler__code__text2']->settings = ['demo_value' => 'text with translation'];
        $selection->fields['handlers']['handler__code__text2']->handler = '\\effcore\\modules\\demo\\Events_Selection::handler__demo';
        $selection->fields['handlers']['handler__code__text2']->is_is_apply_translation = false;
        $selection->fields['handlers']['handler__code__text2']->weight = +250;
        # field 'code' example
        $selection->fields['code']['code__data'] = new stdClass;
        $selection->fields['code']['code__data']->title = new Text('Type "%%_type" from code', ['type' => 'code']);
        $selection->fields['code']['code__data']->settings = ['demo_value' => 'text with translation'];
        $selection->fields['code']['code__data']->weight = +100;
        $selection->fields['code']['code__data']->closure = function ($c_cell_id, $c_row, $c_instance, $origin) {
            return new Markup('span', [], $origin->settings['demo_value'] ?? []);
        };
    }

    static function handler__demo($c_cell_id, $c_row, $c_instance, $origin) {
        return new Markup('span', [],
            new Text($origin->settings['demo_value'], [], $origin->is_apply_translation ?? true)
        );
    }

}
