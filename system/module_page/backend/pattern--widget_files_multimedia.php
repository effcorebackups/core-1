<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_files_multimedia extends widget_files {

  use widget_files_pictures__shared;
  use widget_files_videos__shared;
  use widget_files_audios__shared;

  public $title = 'Multimedia';
  public $item_title = 'File';
  public $attributes = ['data-type' => 'items-files-multimedia'];
  public $name_complex = 'widget_files_multimedia';
# ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
  public $upload_dir = 'multimedia/';
  public $fixed_name = 'multimedia-multiple-%%_item_id_context';

  public $picture_max_file_size = '1M';
  public $picture_types_allowed = [
    'png'  => 'png',
    'gif'  => 'gif',
    'jpg'  => 'jpg',
    'jpeg' => 'jpeg'];
  public $video_max_file_size = '50M';
  public $video_types_allowed = [
    'mp4' => 'mp4'];
  public $audio_max_file_size = '10M';
  public $audio_types_allowed = [
    'mp3' => 'mp3'
  ];

  ###########################
  ### static declarations ###
  ###########################

  static function complex_value_to_markup($complex) {
    $decorator = new decorator;
    $decorator->id = 'widget_files-multimedia-items';
    $decorator->view_type = 'template';
    $decorator->template = 'content';
    $decorator->template_row = 'gallery_row';
    $decorator->template_row_mapping = core::array_keys_map(['num', 'type', 'children']);
    if ($complex) {
      core::array_sort_by_weight($complex);
      foreach ($complex as $c_row_id => $c_item) {
        if (in_array(media::media_class_get($c_item->object->type), ['picture', 'audio', 'video'])) {
          $decorator->data[$c_row_id] = [
            'type'     => ['value' => media::media_class_get($c_item->object->type)],
            'num'      => ['value' => $c_row_id],
            'children' => ['value' => static::item_markup_get($c_item, $c_row_id)]
          ];
        }
      }
    }
    return $decorator;
  }

  static function item_markup_get($item, $row_id) {
    if (media::media_class_get($item->object->type) === 'picture') return widget_files_pictures::item_markup_get($item, $row_id);
    if (media::media_class_get($item->object->type) === 'video'  ) return widget_files_videos  ::item_markup_get($item, $row_id);
    if (media::media_class_get($item->object->type) === 'audio'  ) return widget_files_audios  ::item_markup_get($item, $row_id);
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  static function widget_manage_get($widget, $item, $c_row_id) {
    if (media::media_class_get($item->object->type) === 'picture') return widget_files_pictures::widget_manage_get($widget, $item, $c_row_id);
    if (media::media_class_get($item->object->type) === 'video'  ) return widget_files_videos  ::widget_manage_get($widget, $item, $c_row_id);
    if (media::media_class_get($item->object->type) === 'audio'  ) return widget_files_audios  ::widget_manage_get($widget, $item, $c_row_id);
    return parent::widget_manage_get($widget, $item, $c_row_id);
  }

  static function widget_insert_get($widget) {
    $result = new markup('x-widget', ['data-type' => 'insert']);
    $media_type = new micro_tabs;
    $media_type->element_attributes['name'] = 'media_type';
    $media_type->checked['1'] = '1';
    $media_type->item_insert('Pictures', '1');
    $media_type->item_insert('Video',    '2');
    $media_type->item_insert('Audio',    '3');
    $fieldset_pictures = new fieldset(null, null, ['data-micro_tabs-content-id' => '1', 'data-type' => 'pictures']);
    $fieldset_video    = new fieldset(null, null, ['data-micro_tabs-content-id' => '2', 'data-type' => 'video'   ]);
    $fieldset_audio    = new fieldset(null, null, ['data-micro_tabs-content-id' => '3', 'data-type' => 'audio'   ]);
    $fieldset_pictures->state = 'closed';
    $fieldset_video   ->state = 'closed';
    $fieldset_audio   ->state = 'closed';
    $fieldset_pictures->children_update(widget_files_pictures::widget_insert_get($widget, 'picture')->children_select());
    $fieldset_video   ->children_update(widget_files_videos  ::widget_insert_get($widget, 'video'  )->children_select());
    $fieldset_audio   ->children_update(widget_files_audios  ::widget_insert_get($widget, 'audio'  )->children_select());
    $widget->controls['*fieldset_pictures'] = $fieldset_pictures;
    $widget->controls['*fieldset_video'   ] = $fieldset_video;
    $widget->controls['*fieldset_audio'   ] = $fieldset_audio;
    $result->child_insert($media_type, 'media_type');
    $result->child_insert($fieldset_pictures, 'pictures');
    $result->child_insert($fieldset_video, 'video');
    $result->child_insert($fieldset_audio, 'audio');
    return $result;
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  static function on_file_prepare($widget, $form, $npath, $button, &$items, &$new_item) {
    if ($button->_kind === 'picture') {$widget->controls['#file'] = $widget->controls['#file_picture']; return widget_files_pictures::on_file_prepare($widget, $form, $npath, $button, $items, $new_item);}
    if ($button->_kind === 'video'  ) {$widget->controls['#file'] = $widget->controls['#file_video'  ]; return widget_files_videos  ::on_file_prepare($widget, $form, $npath, $button, $items, $new_item);}
    if ($button->_kind === 'audio'  ) {$widget->controls['#file'] = $widget->controls['#file_audio'  ]; return widget_files_audios  ::on_file_prepare($widget, $form, $npath, $button, $items, $new_item);}
  }

  static function on_button_click_insert($widget, $form, $npath, $button) {
    if ($button->_kind === 'picture') {$widget->controls['#file'] = $widget->controls['#file_picture']; return widget_files_pictures::on_button_click_insert($widget, $form, $npath, $button);}
    if ($button->_kind === 'video'  ) {$widget->controls['#file'] = $widget->controls['#file_video'  ]; return widget_files_videos  ::on_button_click_insert($widget, $form, $npath, $button);}
    if ($button->_kind === 'audio'  ) {$widget->controls['#file'] = $widget->controls['#file_audio'  ]; return widget_files_audios  ::on_button_click_insert($widget, $form, $npath, $button);}
  }

}}