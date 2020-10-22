<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\access;
          use \effcore\block_preset;
          use \effcore\entity;
          use \effcore\instance;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\selection;
          use \effcore\text;
          use \effcore\url;
          abstract class events_page {

  static function block_markup___messages($page) {
    return new message;
  }

  static function block_markup___title($page) {
    return new markup('h1', ['id' => 'title'],
      new text($page->title, [], true, true)
    );
  }

  static function block_markup___page_actions($page) {
    if ($page->origin === 'sql' && access::check((object)['roles' => ['admins' => 'admins']])) {
      return new markup('x-admin-actions', [],
        new markup('a', ['data-id' => 'update', 'href' => '/manage/data/content/page/'.$page->id.'/update?'.url::back_part_make()], 'edit page')
      );
    }
  }

  static function on_block_presets_dynamic_build($event, $id = null) {
    if ($id === null                                      ) {foreach (entity::get('audio'  )->instances_select() as $c_item) block_preset::insert(  'audio_sql__'.$c_item->id, 'Audios',    $c_item->description ?: 'NO TITLE', [ /* no areas */ ], /* display = */ null, 'code', '\\effcore\\modules\\page\\events_page::block_markup__selection_make', [ /* no properties */ ], ['instance_id' => $c_item->id, 'entity_name' => 'audio'  ], 0, 'page');}
    if ($id === null                                      ) {foreach (entity::get('gallery')->instances_select() as $c_item) block_preset::insert('gallery_sql__'.$c_item->id, 'Galleries', $c_item->title       ?: 'NO TITLE', [ /* no areas */ ], /* display = */ null, 'code', '\\effcore\\modules\\page\\events_page::block_markup__selection_make', [ /* no properties */ ], ['instance_id' => $c_item->id, 'entity_name' => 'gallery'], 0, 'page');}
    if ($id === null                                      ) {foreach (entity::get('picture')->instances_select() as $c_item) block_preset::insert('picture_sql__'.$c_item->id, 'Pictures',  $c_item->description ?: 'NO TITLE', [ /* no areas */ ], /* display = */ null, 'code', '\\effcore\\modules\\page\\events_page::block_markup__selection_make', [ /* no properties */ ], ['instance_id' => $c_item->id, 'entity_name' => 'picture'], 0, 'page');}
    if ($id === null                                      ) {foreach (entity::get('text'   )->instances_select() as $c_item) block_preset::insert(   'text_sql__'.$c_item->id, 'Texts',     $c_item->description ?: 'NO TITLE', [ /* no areas */ ], /* display = */ null, 'code', '\\effcore\\modules\\page\\events_page::block_markup__selection_make', [ /* no properties */ ], ['instance_id' => $c_item->id, 'entity_name' => 'text'   ], 0, 'page');}
    if ($id === null                                      ) {foreach (entity::get('video'  )->instances_select() as $c_item) block_preset::insert(  'video_sql__'.$c_item->id, 'Videos',    $c_item->description ?: 'NO TITLE', [ /* no areas */ ], /* display = */ null, 'code', '\\effcore\\modules\\page\\events_page::block_markup__selection_make', [ /* no properties */ ], ['instance_id' => $c_item->id, 'entity_name' => 'video'  ], 0, 'page');}
    if ($id !== null && strpos($id,   'audio_sql__') === 0) {$c_item__id = substr($id, strlen(  'audio_sql__'));             block_preset::insert(  'audio_sql__'.$c_item__id, 'Audios',                            'NO TITLE', [ /* no areas */ ], /* display = */ null, 'code', '\\effcore\\modules\\page\\events_page::block_markup__selection_make', [ /* no properties */ ], ['instance_id' => $c_item__id, 'entity_name' => 'audio'  ], 0, 'page');}
    if ($id !== null && strpos($id, 'gallery_sql__') === 0) {$c_item__id = substr($id, strlen('gallery_sql__'));             block_preset::insert('gallery_sql__'.$c_item__id, 'Galleries',                         'NO TITLE', [ /* no areas */ ], /* display = */ null, 'code', '\\effcore\\modules\\page\\events_page::block_markup__selection_make', [ /* no properties */ ], ['instance_id' => $c_item__id, 'entity_name' => 'gallery'], 0, 'page');}
    if ($id !== null && strpos($id, 'picture_sql__') === 0) {$c_item__id = substr($id, strlen('picture_sql__'));             block_preset::insert('picture_sql__'.$c_item__id, 'Pictures',                          'NO TITLE', [ /* no areas */ ], /* display = */ null, 'code', '\\effcore\\modules\\page\\events_page::block_markup__selection_make', [ /* no properties */ ], ['instance_id' => $c_item__id, 'entity_name' => 'picture'], 0, 'page');}
    if ($id !== null && strpos($id,    'text_sql__') === 0) {$c_item__id = substr($id, strlen(   'text_sql__'));             block_preset::insert(   'text_sql__'.$c_item__id, 'Texts',                             'NO TITLE', [ /* no areas */ ], /* display = */ null, 'code', '\\effcore\\modules\\page\\events_page::block_markup__selection_make', [ /* no properties */ ], ['instance_id' => $c_item__id, 'entity_name' => 'text'   ], 0, 'page');}
    if ($id !== null && strpos($id,   'video_sql__') === 0) {$c_item__id = substr($id, strlen(  'video_sql__'));             block_preset::insert(  'video_sql__'.$c_item__id, 'Videos',                            'NO TITLE', [ /* no areas */ ], /* display = */ null, 'code', '\\effcore\\modules\\page\\events_page::block_markup__selection_make', [ /* no properties */ ], ['instance_id' => $c_item__id, 'entity_name' => 'video'  ], 0, 'page');}
  }

  static function block_markup__selection_make($page, $args) {
    if (!empty($args['instance_id']) &&
        !empty($args['entity_name'])) {
      $entity = entity::get($args['entity_name']);
      $selection = new selection;
      $selection->id = $args['entity_name'].'_'.$args['instance_id'];
      $selection->template = 'content';
      foreach ($entity->selection_params_default ?? [] as $c_key => $c_value)
        $selection                                     ->{$c_key} = $c_value;
      $selection->query_params['conditions'] = ['id_!f' => '~'.$args['entity_name'].'.id', 'operator' => '=', 'id_!v' => $args['instance_id']];
      foreach ($entity->fields as $c_name => $c_field) {
        if (!empty($c_field->managing_on_select_is_enabled)) {
          $selection->field_insert_entity(null,
            $entity->name, $c_name, $c_field->selection_params_default ?? []
          );
        }
      }
      $selection->build();
      return $selection;
    }
  }

}}