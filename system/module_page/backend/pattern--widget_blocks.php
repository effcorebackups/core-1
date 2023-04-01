<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_blocks extends widget_items {

  public $title;
  public $title__not_selected__widget_insert = '- select -';
  public $item_title = 'Block';
  public $content_tag_name = null;
  public $attributes = ['data-type' => 'items-blocks'];
  public $name_complex = 'widget_blocks';
  public $id_area;

  function __construct($id_area, $attributes = [], $weight = 0) {
    $this->id_area = $id_area;
    parent::__construct($attributes, $weight);
  }

  ###########################
  ### static declarations ###
  ###########################

  static function widget_manage_get($widget, $item, $c_row_id) {
    $result = parent::widget_manage_get($widget, $item, $c_row_id);
  # info markup
    $presets = block_preset::select_all($widget->id_area);
    $title_markup = isset($presets[$item->id]) ?
                         [$presets[$item->id]->managing_group, ': ',
                          $presets[$item->id]->managing_title] : 'ORPHANED BLOCK';
    $info_markup = new markup('x-info',  [], [
        'title' => new markup('x-title', [], $title_markup),
        'id'    => new markup('x-id',    [], new text_simple($item->id) ) ]);
  # create widget_settings and prepare item (copy properties from block_preset to block_preset_link)
    if ($item instanceof block_preset_link) {
      if (!isset($item->title) ||
          !isset($item->attributes)) {
        $preset = $item->preset_make();
        $item->title            = $preset->title;
        $item->title_is_visible = $preset->title_is_visible;
        $item->attributes       = $preset->attributes; }}
    $widget_settings = new widget_block_settings($widget, $item, $c_row_id);
    $widget_settings->build();
  # relate new controls with the widget
    $result->child_insert($info_markup, 'info');
    $result->child_insert($widget_settings, 'settings');
    return $result;
  }

  static function widget_insert_get($widget) {
    $result = new markup('x-widget', ['data-type' => 'insert']);
  # control with type of new item
    $field_select_block_preset = new field_select_block_preset('Insert block');
    $field_select_block_preset->title__not_selected = $widget->title__not_selected__widget_insert;
    $field_select_block_preset->id_area = $widget->id_area;
    $field_select_block_preset->cform = $widget->cform;
    $field_select_block_preset->build();
    $field_select_block_preset->name_set($widget->name_get_complex().'__insert');
    $field_select_block_preset->required_set(false);
  # button for insertion of the new item
    $button_insert = new button(null, ['data-style' => 'insert', 'title' => new text('insert')]);
    $button_insert->break_on_validate = true;
    $button_insert->build();
    $button_insert->value_set($widget->name_get_complex().'__insert');
    $button_insert->_type = 'insert';
  # relate new controls with the widget
    $widget->controls['#insert'] = $field_select_block_preset;
    $widget->controls['~insert'] = $button_insert;
    $result->child_insert($field_select_block_preset, 'field_select_block_preset');
    $result->child_insert($button_insert, 'button_insert');
    return $result;
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  static function on_button_click_insert($widget, $form, $npath, $button) {
    $widget->controls['#insert']->required_set(true);
    $result_validation = field_select::on_validate($widget->controls['#insert'], $form, $npath);
    $widget->controls['#insert']->required_set(false);
    if ($result_validation) {
      $min_weight = 0;
      $items = $widget->items_get();
      foreach ($items as $c_row_id => $c_item)
        $min_weight = min($min_weight, $c_item->weight);
      $new_item = new block_preset_link($widget->controls['#insert']->value_get());
      $new_item->weight = count($items) ? $min_weight - 5 : 0;
      $items[] = $new_item;
      $widget->items_set($items);
      $widget->controls['#insert']->value_set('');
      message::insert(new text_multiline([
        'Item of type "%%_type" with ID = "%%_id" was inserted.',
        'Do not forget to save the changes!'], [
        'type' => (new text($widget->item_title))->render(),
        'id'   => $new_item->id ]));
      return true;
    }
  }

}}