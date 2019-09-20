<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class group_selection_field_insert extends fieldset {

  public $tag_name = 'x-selection_field-insert';
  public $content_tag_name = null;

  function build() {
    if (!$this->is_builded) {
      parent::build();
      $entities = entity::get_all();
      core::array_sort_by_text_property($entities);
      $options = ['not_selected' => '- no -'];
      foreach ($entities as $c_entity) {
        if (!empty($c_entity->managing_is_on)) {
          foreach ($c_entity->fields_get_title() as $c_name => $c_title) {
            if (!isset($options[$c_entity->name])) {
                       $options[$c_entity->name] = new \stdClass;
                       $options[$c_entity->name]->title = $c_entity->title;}
            $options[$c_entity->name]->values[ $c_entity->name.'.'.$c_name ] = new text_multiline([
              'title' => $c_title, 'id' => '('.$c_entity->name.'.'.$c_name.')'], [], ' '
            );
          }
        }
      }
      $select = new field_select('Insert field');
      $select->values = $options;
      $select->build();
      $select->name_set('field_insert');
      $select->required_set(false);
      $button = new button('', ['data-style' => 'narrow-insert']);
      $button->build();
      $button->value_set('button_field_insert');
      $this->child_insert($select, 'select');
      $this->child_insert($button, 'button');
      $this->is_builded = true;
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static function validate($group, $form, $npath) {
    $select = $group->child_select('select');
    $button = $group->child_select('button');
    if ($button->is_clicked() && !$select->value_get()) {
      $select->error_set(
        'Field "%%_title" must be selected!', ['title' => translation::get($select->title)]
      );
    } else {
      return true;
    }
  }

  static function submit(&$group, $form, $npath) {
    $select = $group->child_select('select');
    $button = $group->child_select('button');
    if ($button->is_clicked() && $select->value_get()) {
      $fields = $form->validation_cache_get('fields');
      $entity_info = explode('.', $select->value_get());
      $fields[$select->value_get()] = (object)[
        'type'              => 'field',
        'entity_name'       => $entity_info[0],
        'entity_field_name' => $entity_info[1]];
      $form->validation_cache_is_persistent = true;
      $form->validation_cache_set('fields', $fields);
      $entity = entity::get(             $entity_info[0]);
      $entity_field = $entity->field_get($entity_info[1]);
      message::insert(new text('Field "%%_name" was inserted.', ['name' => translation::get($entity->title).': '.translation::get($entity_field->title)]));
      message::insert(new text('Click the button "%%_name" to save your changes!', ['name' => translation::get('update')]), 'warning');
      return true;
    }
  }

}}