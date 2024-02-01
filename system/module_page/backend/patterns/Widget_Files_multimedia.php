<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Widget_Files_multimedia extends Widget_Files {

    use Widget_Files_pictures__Shared;
    use Widget_Files_videos__Shared;
    use Widget_Files_audios__Shared;

    public $title = 'Multimedia';
    public $item_title = 'Multimedia';
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

    static function value_to_markup($value) {
        $decorator = new Decorator;
        $decorator->id = 'widget_files-multimedia-items';
        $decorator->view_type = 'template';
        $decorator->template = 'gallery';
        $decorator->template_item = 'gallery_item';
        $decorator->mapping = Core::array_keys_map(['num', 'type', 'children']);
        if ($value) {
            Core::array_sort_by_number($value);
            foreach ($value as $c_row_id => $c_item) {
                if (Core::in_array(Media::media_class_get($c_item->object->type), ['picture', 'audio', 'video'])) {
                    $decorator->data[$c_row_id] = [
                        'type'     => ['value' => Media::media_class_get($c_item->object->type), 'is_apply_translation' => false],
                        'num'      => ['value' => $c_row_id                                    , 'is_apply_translation' => false],
                        'children' => ['value' => static::render_item($c_item, $c_row_id)]
                    ];
                }
            }
        }
        return $decorator;
    }

    static function render_item($item, $row_id) {
        if (Media::media_class_get($item->object->type) === 'picture') return Widget_Files_pictures::render_item($item, $row_id);
        if (Media::media_class_get($item->object->type) === 'audio'  ) return Widget_Files_audios  ::render_item($item, $row_id);
        if (Media::media_class_get($item->object->type) === 'video'  ) return Widget_Files_videos  ::render_item($item, $row_id);
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function widget_manage_get($widget, $item, $c_row_id) {
        if (Media::media_class_get($item->object->type) === 'picture') return Widget_Files_pictures::widget_manage_get($widget, $item, $c_row_id);
        if (Media::media_class_get($item->object->type) === 'audio'  ) return Widget_Files_audios  ::widget_manage_get($widget, $item, $c_row_id);
        if (Media::media_class_get($item->object->type) === 'video'  ) return Widget_Files_videos  ::widget_manage_get($widget, $item, $c_row_id);
        return parent::widget_manage_get($widget, $item, $c_row_id);
    }

    static function widget_insert_get($widget) {
        $result = new Markup('x-widget', ['data-type' => 'insert']);
        $media_type = new Micro_tabs;
        $media_type->element_attributes['name'] = 'media_type';
        $media_type->checked['1'] = '1';
        $media_type->items_set(['1' => 'Pictures', '2' => 'Audio', '3' => 'Video']);
        $fieldset_pictures = new Fieldset(null, null, ['data-micro_tabs-content-id' => '1', 'data-type' => 'pictures']);
        $fieldset_audio    = new Fieldset(null, null, ['data-micro_tabs-content-id' => '2', 'data-type' => 'audio'   ]);
        $fieldset_video    = new Fieldset(null, null, ['data-micro_tabs-content-id' => '3', 'data-type' => 'video'   ]);
        $fieldset_pictures->state = 'closed';
        $fieldset_audio   ->state = 'closed';
        $fieldset_video   ->state = 'closed';
        $fieldset_pictures->children_update(Widget_Files_pictures::widget_insert_get($widget, 'picture')->children_select());
        $fieldset_audio   ->children_update(Widget_Files_audios  ::widget_insert_get($widget, 'audio'  )->children_select());
        $fieldset_video   ->children_update(Widget_Files_videos  ::widget_insert_get($widget, 'video'  )->children_select());
        $widget->controls['*fieldset_pictures'] = $fieldset_pictures;
        $widget->controls['*fieldset_audio'   ] = $fieldset_audio;
        $widget->controls['*fieldset_video'   ] = $fieldset_video;
        $result->child_insert($media_type       , 'media_type');
        $result->child_insert($fieldset_pictures, 'pictures');
        $result->child_insert($fieldset_audio   , 'audio');
        $result->child_insert($fieldset_video   , 'video');
        return $result;
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function on_file_prepare($widget, $form, $npath, $button, &$items, &$new_item) {
        if ($button->_kind === 'picture') {$widget->controls['#file'] = $widget->controls['#file_picture']; return Widget_Files_pictures::on_file_prepare($widget, $form, $npath, $button, $items, $new_item);}
        if ($button->_kind === 'audio'  ) {$widget->controls['#file'] = $widget->controls['#file_audio'  ]; return Widget_Files_audios  ::on_file_prepare($widget, $form, $npath, $button, $items, $new_item);}
        if ($button->_kind === 'video'  ) {$widget->controls['#file'] = $widget->controls['#file_video'  ]; return Widget_Files_videos  ::on_file_prepare($widget, $form, $npath, $button, $items, $new_item);}
    }

    static function on_button_click_insert($widget, $form, $npath, $button) {
        if ($button->_kind === 'picture') {$widget->item_title = 'Picture'; $widget->controls['#file'] = $widget->controls['#file_picture']; return Widget_Files_pictures::on_button_click_insert($widget, $form, $npath, $button);}
        if ($button->_kind === 'audio'  ) {$widget->item_title = 'Audio';   $widget->controls['#file'] = $widget->controls['#file_audio'  ]; return Widget_Files_audios  ::on_button_click_insert($widget, $form, $npath, $button);}
        if ($button->_kind === 'video'  ) {$widget->item_title = 'Video';   $widget->controls['#file'] = $widget->controls['#file_video'  ]; return Widget_Files_videos  ::on_button_click_insert($widget, $form, $npath, $button);}
    }

    static function on_button_click_delete($widget, $form, $npath, $button) {
        if ($button->_kind === 'picture') {$widget->item_title = 'Picture'; return parent::on_button_click_delete($widget, $form, $npath, $button);}
        if ($button->_kind === 'audio'  ) {$widget->item_title = 'Audio';   return parent::on_button_click_delete($widget, $form, $npath, $button);}
        if ($button->_kind === 'video'  ) {$widget->item_title = 'Video';   return parent::on_button_click_delete($widget, $form, $npath, $button);}
    }

}
