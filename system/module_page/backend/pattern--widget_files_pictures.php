<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_files_pictures extends widget_files {

  use widget_files_pictures__shared;

  public $title = 'Pictures';
  public $item_title = 'Picture';
  public $attributes = ['data-type' => 'items-files-pictures'];
  public $name_complex = 'widget_files_pictures';
# ─────────────────────────────────────────────────────────────────────
  public $upload_dir = 'pictures/';
  public $fixed_name = 'picture-multiple-%%_item_id_context';
# ─────────────────────────────────────────────────────────────────────
  public $max_file_size = '1M';
  public $types_allowed = [
    'png'  => 'png',
    'gif'  => 'gif',
    'jpg'  => 'jpg',
    'jpeg' => 'jpeg'
  ];

  function on_file_prepare($form, $npath, $button, &$items, &$new_item) {
    return $this->on_file_prepare_picture($form, $npath, $button, $items, $new_item);
  }

  ###########################
  ### static declarations ###
  ###########################

  static function complex_value_to_markup($complex) {
    $decorator = new decorator;
    $decorator->id = 'widget_files-pictures-items';
    $decorator->view_type = 'template';
    $decorator->template = 'content';
    $decorator->template_row = 'gallery_row';
    $decorator->template_row_mapping = core::array_kmap(['num', 'type', 'children']);
    if ($complex) {
      core::array_sort_by_weight($complex);
      foreach ($complex as $c_row_id => $c_item) {
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
    return new markup('a', ['data-type' => 'picture-wrapper', 'title' => new text($item->settings['title']), 'target' => $item->settings['target'], 'href' => '/'.$item->object->get_current_path(true).'?thumb=big'],
      new markup_simple('img', ['src' => '/'.$item->object->get_current_path(true).'?thumb=middle', 'alt' => new text($item->settings['alt'])])
    );
  }

  # ─────────────────────────────────────────────────────────────────────

  static function widget_manage_get(&$widget, $item, $c_row_id) {
    $result = parent::widget_manage_get($widget, $item, $c_row_id);
    $result->attribute_insert('data-is-new', $item->object->get_current_state() === 'pre' ? 'true' : 'false');
    static::widget_manage_picture_item_make($result, $item, $c_row_id, $widget);
    return $result;
  }

  static function widget_insert_get(&$widget, $group = '') {
    $result = new markup('x-widget', ['data-type' => 'insert']);
  # control for upload new picture
    $field_file_picture = new field_file_picture;
    $field_file_picture->title            = 'Picture';
    $field_file_picture->max_file_size    = $widget->{($group ? $group.'_' : '').'max_file_size'};
    $field_file_picture->types_allowed    = $widget->{($group ? $group.'_' : '').'types_allowed'};
    $field_file_picture->cform            = $widget->cform;
    $field_file_picture->min_files_number = null;
    $field_file_picture->max_files_number = null;
    $field_file_picture->has_on_validate  = false;
    $field_file_picture->build();
    $field_file_picture->multiple_set();
    $field_file_picture->name_set($widget->name_get_complex().'__file'.($group ? '_'.$group : '').'[]');
  # button for insertion of the new item
    $button = new button(null, ['data-style' => 'narrow-insert', 'title' => new text('insert')]);
    $button->break_on_validate = true;
    $button->build();
    $button->value_set($widget->name_get_complex().'__insert'.($group ? '_'.$group : ''));
    $button->_type = 'insert';
    $button->_kind = 'picture';
  # relate new controls with the widget
    $widget->controls[  '#file'.($group ? '_'.$group : '')] = $field_file_picture;
    $widget->controls['~insert'.($group ? '_'.$group : '')] = $button;
    $result->child_insert($field_file_picture, 'file');
    $result->child_insert($button, 'button');
    return $result;
  }

}}