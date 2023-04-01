<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_files_pictures extends widget_files {

  use widget_files_pictures__shared;

  public $title = 'Pictures';
  public $item_title = 'Picture';
  public $attributes = ['data-type' => 'items-files-pictures'];
  public $name_complex = 'widget_files_pictures';
# ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
  public $upload_dir = 'pictures/';
  public $fixed_name = 'picture-multiple-%%_item_id_context';
# ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
  public $max_file_size = '1M';
  public $types_allowed = [
    'png'  => 'png',
    'gif'  => 'gif',
    'jpg'  => 'jpg',
    'jpeg' => 'jpeg'
  ];

  ###########################
  ### static declarations ###
  ###########################

  static function value_to_markup($value) {
    $decorator = new decorator;
    $decorator->id = 'widget_files-pictures-items';
    $decorator->view_type = 'template';
    $decorator->template = 'content';
    $decorator->template_item = 'gallery_item';
    $decorator->mapping = core::array_keys_map(['num', 'type', 'children']);
    if ($value) {
      core::array_sort_by_number($value);
      foreach ($value as $c_row_id => $c_item) {
        if (media::media_class_get($c_item->object->type) === 'picture') {
          $decorator->data[$c_row_id] = [
            'type'     => ['value' => 'picture'],
            'num'      => ['value' => $c_row_id],
            'children' => ['value' => static::item_markup_get($c_item, $c_row_id)]
          ];
        }
      }
    }
    return $decorator;
  }

  static function item_markup_get($item, $row_id) {
    $src = '/'.$item->object->get_current_path(true);
    return new markup('a', ['data-type' => 'picture-wrapper', 'href' => $src.'?thumb=big', 'title' => new text($item->settings['title']), 'target' => $item->settings['target']],
      new markup_simple('img', ['src' => $src.'?thumb=middle', 'alt' => new text($item->settings['alt'])])
    );
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  static function widget_manage_get($widget, $item, $c_row_id) {
    $result = parent::widget_manage_get($widget, $item, $c_row_id);
    $result->attribute_insert('data-is-new', $item->object->get_current_state() === 'pre' ? 'true' : 'false');
    if (media::media_class_get($item->object->type) === 'picture') {
      if (!empty($item->settings['data-thumbnails-is-embedded'])) {
        $result->child_insert(new markup_simple('img', ['src' => '/'.$item->object->get_current_path(true).'?thumb=small', 'alt' => new text('thumbnail'), 'width' => '44', 'height' => '44', 'data-type' => 'thumbnail'], +450), 'thumbnail');
      }
    }
    return $result;
  }

  static function widget_insert_get($widget, $group = '') {
    $result = new markup('x-widget', ['data-type' => 'insert']);
  # control for upload new picture
    $field_file_picture = new field_file_picture;
    $field_file_picture->title             = 'Picture';
    $field_file_picture->max_file_size     = $widget->{($group ? $group.'_' : '').'max_file_size'};
    $field_file_picture->types_allowed     = $widget->{($group ? $group.'_' : '').'types_allowed'};
    $field_file_picture->cform             = $widget->cform;
    $field_file_picture->min_files_number  = null;
    $field_file_picture->max_files_number  = null;
    $field_file_picture->has_widget_insert = false;
    $field_file_picture->has_widget_manage = false;
    $field_file_picture->build();
    $field_file_picture->multiple_set();
    $field_file_picture->name_set($widget->name_get_complex().'__file'.($group ? '_'.$group : '').'[]');
  # button for insertion of the new item
    $button_insert = new button(null, ['data-style' => 'insert', 'title' => new text('insert')]);
    $button_insert->break_on_validate = true;
    $button_insert->build();
    $button_insert->value_set($widget->name_get_complex().'__insert'.($group ? '_'.$group : ''));
    $button_insert->_type = 'insert';
    $button_insert->_kind = 'picture';
  # relate new controls with the widget
    $widget->controls[  '#file'.($group ? '_'.$group : '')] = $field_file_picture;
    $widget->controls['~insert'.($group ? '_'.$group : '')] = $button_insert;
    $result->child_insert($field_file_picture, 'field_file_picture');
    $result->child_insert($button_insert, 'button_insert');
    return $result;
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  static function on_file_prepare($widget, $form, $npath, $button, &$items, &$new_item) {
    $pre_path = temporary::directory.'validation/'.$form->validation_cache_date_get().'/'.$form->validation_id.'-'.$widget->name_get_complex().'-'.core::array_key_last($items).'.'.$new_item->object->type;
    if ($new_item->object->move_tmp_to_pre($pre_path)) {
      $new_item->settings = $widget->picture_default_settings;
      $new_item->settings['data-thumbnails-is-embedded'] = false;
      if ($widget->thumbnails_is_allowed) {
        if (media::media_class_get($new_item->object->type) === 'picture') {
          if (media::is_type_for_thumbnail($new_item->object->type)) {
            if ($new_item->object->container_picture_make($widget->thumbnails)) {
              $new_item->settings['data-thumbnails-is-embedded'] = true;
            }
          }
        }
      }
      return true;
    }
  }

}}