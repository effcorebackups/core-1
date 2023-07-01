<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\poll;

use effcore\Core;
use effcore\Entity;
use effcore\Module;
use effcore\Poll;
use effcore\Text;
use effcore\Url;
use effcore\Widget_Texts;

abstract class Events_Form_Instance_insert {

    static function on_build($event, $form) {
        if ($form->has_error_on_build === false &&
            $form->has_no_fields      === false) {
            $entity = Entity::get($form->entity_name);
            if ($entity->name === 'poll') {
                $widget_answers = new Widget_Texts;
                $widget_answers->cform = $form;
                $widget_answers->name_complex = 'widget_answers';
                $widget_answers->title = 'Answers';
                $widget_answers->item_title = 'Answer';
                $widget_answers->weight = -500;
                $form->child_select('fields')->child_insert($widget_answers, 'answers');
            }
        }
    }

    static function on_init($event, $form, $items) {
        if ($form->has_error_on_build === false &&
            $form->has_no_fields      === false) {
            $entity = Entity::get($form->entity_name);
            if ($entity->name === 'poll') {
                $form->is_redirect_enabled = false;
                $items['#expired']->value_set(Core::datetime_get('+'.Core::DATE_PERIOD_W.' second'));
                $items['*widget_answers']->value_set([
                    (object)['weight' =>  0, 'id' => 0, 'text' => 'Answer 1'],
                    (object)['weight' => -5, 'id' => 0, 'text' => 'Answer 2']], ['once' => true]
                );
            }
        }
    }

    static function on_validate($event, $form, $items) {
        $entity = Entity::get($form->entity_name);
        switch ($form->clicked_button->value_get()) {
            case 'insert':
            case 'insert_and_update':
                if ($entity->name === 'poll') {
                    $settings = Module::settings_get('poll');
                    if (count($items['*widget_answers']->value_get()) < $settings->answers_min) $form->error_set('Group "%%_title" should contain a minimum %%_number item%%_plural(number|s)!', ['title' => (new Text($items['*widget_answers']->title))->render(), 'number' => $settings->answers_min]);
                    if (count($items['*widget_answers']->value_get()) > $settings->answers_max) $form->error_set('Group "%%_title" should contain a maximum %%_number item%%_plural(number|s)!', ['title' => (new Text($items['*widget_answers']->title))->render(), 'number' => $settings->answers_max]);
                }
                break;
        }
    }

    static function on_submit($event, $form, $items) {
        $entity = Entity::get($form->entity_name);
        if ($entity->name === 'poll') {
            switch ($form->clicked_button->value_get()) {
                case 'insert':
                case 'insert_and_update':
                    foreach ($items['*widget_answers']->value_get() as $c_item)
                        Poll::answer_insert($form->_instance->id, $c_item->text, $c_item->weight);
                    # reset not actual data
                    $items['*widget_answers']->items_reset();
                    static::on_init(null, $form, $items);
                    # ↓↓↓ no break ↓↓↓
                case 'cancel':
                    Url::go(Url::back_url_get() ?: $entity->make_url_for_select_multiple());
                    break;
            }
        }
    }

}
