<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\access;
          use \effcore\entity;
          use \effcore\instance;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\part_preset;
          use \effcore\selection;
          use \effcore\text;
          use \effcore\url;
          abstract class events_page {

  static function block_messages($page) {
    return new message;
  }

  static function block_title($page) {
    return new markup('h1', ['id' => 'title'],
      new text($page->title, [], true, true)
    );
  }

  static function block_page_actions($page) {
    if ($page->origin == 'sql' && access::check((object)['roles' => ['admins' => 'admins']])) {
      return new markup('x-page-actions', [],
        new markup('a', ['data-id' => 'update', 'href' => '/manage/data/content/page/'.$page->id.'/update?'.url::back_part_make()], 'update this page')
      );
    }
  }

  static function on_part_presets_dynamic_build($event, $id = null) {
    if ($id === null                                  ) {foreach (entity::get('text')->instances_select() as $c_text)                                                                                             part_preset::insert('text_sql_'.$c_text->id, 'Texts', $c_text->description ?: 'NO TITLE', [], null, 'code', '\\effcore\\modules\\page\\events_page::block_text_sql', [], ['id' => $c_text->id], 0, 'page');}
    if ($id !== null && strpos($id, 'text_sql_') === 0) {                                                    $c_text = (new instance('text', ['id' => substr($id, strlen('text_sql_'))]))->select(); if ($c_text) part_preset::insert('text_sql_'.$c_text->id, 'Texts', $c_text->description ?: 'NO TITLE', [], null, 'code', '\\effcore\\modules\\page\\events_page::block_text_sql', [], ['id' => $c_text->id], 0, 'page');}
    if ($id === null                                      ) {foreach (entity::get('logotype')->instances_select() as $c_logotype)                                                                                                         part_preset::insert('logotype_sql_'.$c_logotype->id, 'Logotypes', $c_logotype->title ?: 'NO TITLE', [], null, 'code', '\\effcore\\modules\\page\\events_page::block_logotype_sql', [], ['id' => $c_logotype->id], 0, 'page');}
    if ($id !== null && strpos($id, 'logotype_sql_') === 0) {                                                        $c_logotype = (new instance('logotype', ['id' => substr($id, strlen('logotype_sql_'))]))->select(); if ($c_logotype) part_preset::insert('logotype_sql_'.$c_logotype->id, 'Logotypes', $c_logotype->title ?: 'NO TITLE', [], null, 'code', '\\effcore\\modules\\page\\events_page::block_logotype_sql', [], ['id' => $c_logotype->id], 0, 'page');}
  }

  static function block_text_sql($page, $args) {
    if (!empty($args['id'])) {
      $entity = entity::get('text');
      $selection = new selection;
      $selection->id = 'text_'.$args['id'];
      $selection->template = 'content';
      $selection->decorator_params = $entity->decorator_params;
      $selection->query_params['conditions'] = ['id_!f' => '~text.id', 'operator' => '=', 'id_!v' => $args['id']];
      foreach ($entity->fields as $c_name => $c_field) {
        if (!empty($c_field->managing_on_select_is_enabled)) {
          $selection->field_insert_entity(null,
            $entity->name, $c_name, $c_field->managing_selection_params ?? []
          );
        }
      }
      $selection->build();
      return $selection;
    }
  }

  static function block_logotype_sql($page, $args) {
    if (!empty($args['id'])) {
      $entity = entity::get('logotype');
      $selection = new selection;
      $selection->id = 'logotype_'.$args['id'];
      $selection->template = 'content';
      $selection->decorator_params = $entity->decorator_params;
      $selection->query_params['conditions'] = ['id_!f' => '~logotype.id', 'operator' => '=', 'id_!v' => $args['id']];
      foreach ($entity->fields as $c_name => $c_field) {
        if (!empty($c_field->managing_on_select_is_enabled)) {
          $selection->field_insert_entity(null,
            $entity->name, $c_name, $c_field->managing_selection_params ?? []
          );
        }
      }
      $selection->build();
      return $selection;
    }
  }

}}