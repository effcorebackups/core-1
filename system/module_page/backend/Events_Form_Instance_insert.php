<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\page;

use effcore\Access;
use effcore\Entity;
use effcore\Language;
use effcore\Page;
use effcore\Text_multiline;
use effcore\Text;

abstract class Events_Form_Instance_insert {

    static function on_init($event, $form, $items) {
        if ($form->has_error_on_build === false &&
            $form->has_no_fields      === false) {
            $entity = Entity::get($form->entity_name);
            if ($entity->name === 'page') {
                # field 'lang_code'
                $items['#lang_code']->value_set(Language::code_get_current());
                # meta
                if (!Access::check((object)['roles'       => ['admins'      => 'admins'     ],
                                            'permissions' => ['manage__seo' => 'manage__seo']])) {
                    $items['#meta'              ]->disabled_set(true);
                    $items['#is_use_global_meta']->disabled_set(true);
                }
            }
            if ($entity->name === 'audio') {
                # widget 'attributes'
                $items['*attributes']->value_set([
                    (object)['weight' =>   0, 'name' => 'controls',                        'value' => ''        ],
                    (object)['weight' =>  -5, 'name' => 'preload',                         'value' => 'metadata'],
                    (object)['weight' => -10, 'name' => 'data-player-name',                'value' => 'default' ],
                    (object)['weight' => -15, 'name' => 'data-player-timeline-is-visible', 'value' => 'true'    ]
                ], ['once' => true]);
            }
            if ($entity->name === 'video') {
                # widget 'attributes'
                $items['*attributes']->value_set([
                    (object)['weight' =>   0, 'name' => 'controls', 'value' => ''        ],
                    (object)['weight' =>  -5, 'name' => 'preload',  'value' => 'metadata']
                ], ['once' => true]);
            }
            if ($entity->name === 'picture') {
                # widget 'attributes'
                $items['*attributes']->value_set([
                    (object)['weight' => 0, 'name' => 'alt', 'value' => 'some alternative text to support markup validity']
                ], ['once' => true]);
            }
            if ($entity->name === 'gallery') {
                # widget 'attributes'
                $items['*attributes']->value_set([
                    (object)['weight' => 0, 'name' => 'data-player-name', 'value' => 'default']
                ], ['once' => true]);
            }
        }
    }

    static function on_validate($event, $form, $items) {
        $entity = Entity::get($form->entity_name);
        switch ($form->clicked_button->value_get()) {
            case 'insert':
            case 'insert_and_update':
                if ($entity->name === 'page') {
                    # field 'id' (check the uniqueness of SQL + NoSQL data)
                    if ($items['#id']->value_get()) {
                        if (Page::get_by_id($items['#id']->value_get())) {
                            $items['#id']->error_set(new Text_multiline([
                                'Field "%%_title" contains an error!',
                                'Previously used value was specified.',
                                'Only unique value is allowed.'], ['title' => (new Text($items['#id']->title))->render() ]
                            ));
                        }
                    }
                    # field 'url' (check the uniqueness of SQL + NoSQL data)
                    if ($items['#url']->value_get()) {
                        if (Page::get_by_url($items['#url']->value_get())) {
                            $items['#url']->error_set(new Text_multiline([
                                'Field "%%_title" contains an error!',
                                'Previously used value was specified.',
                                'Only unique value is allowed.'], ['title' => (new Text($items['#url']->title))->render() ]
                            ));
                        }
                    }
                }
                break;
        }
    }

}
