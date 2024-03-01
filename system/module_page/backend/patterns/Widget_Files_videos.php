<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Widget_Files_videos extends Widget_Files {

    use Widget_Files_videos__Shared;

    public $title = 'Videos';
    public $item_title = 'Video';
    public $attributes = ['data-type' => 'items-files-videos'];
    public $name_complex = 'widget_files_videos';
    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
    public $upload_dir = 'videos/';
    public $fixed_name = 'video-multiple-%%_item_id_context';
    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
    public $max_file_size = '50M';
    public $types_allowed = [
        'mp4' => 'mp4'
    ];

    ###########################
    ### static declarations ###
    ###########################

    static function value_to_markup($value) {
        $decorator = new Decorator;
        $decorator->id = 'widget_files-videos-items';
        $decorator->view_type = 'template';
        $decorator->template = 'content';
        $decorator->template_item = 'gallery_item';
        $decorator->mapping = Core::array_keys_map(['num', 'type', 'children']);
        if ($value) {
            Core::array_sort_by_number($value);
            foreach ($value as $c_row_id => $c_item) {
                if (Media::media_class_get($c_item->object->type) === 'video') {
                    $decorator->data[$c_row_id] = [
                        'type'     => ['value' => 'video'  , 'is_apply_translation' => false],
                        'num'      => ['value' => $c_row_id, 'is_apply_translation' => false],
                        'children' => ['value' => static::render_item($c_item, $c_row_id)]
                    ];
                }
            }
        }
        return $decorator;
    }

    static function render_item($item, $row_id) {
        $settings = Module::settings_get('page');
        $url = Core::to_url_from_path($item->object->get_current_path(true));
        if ($item->settings['data-poster-is-embedded']) {
            return Template::make_new(Template::pick_name('video'), [
                'id'         => $row_id,
                'src'        => $url,
                'poster'     => $url ? $url.'?poster=big' : '',
                'attributes' => Core::data_to_attributes([
                    'data-path-poster-small'  => $url ? $url.'?poster=small'  : '',
                    'data-path-poster-middle' => $url ? $url.'?poster=middle' : '',
                    'data-path-poster-big'    => $url ? $url.'?poster=big'    : '',
                ] + $item->settings)
            ])->render();
        } else {
            return Template::make_new(Template::pick_name('video'), [
                'id'         => $row_id,
                'src'        => $url,
                'poster'     => Core::to_url_from_path($settings->thumbnail_path_poster_default),
                'attributes' => Core::data_to_attributes($item->settings)
            ])->render();
        }
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function widget_manage_get($widget, $item, $c_row_id) {
        $result = parent::widget_manage_get($widget, $item, $c_row_id);
        $result->child_select('head')->child_select('button_delete')->_kind = 'video';
        $result->attribute_insert('data-is-new', $item->object->get_current_state() === 'pre' ? 'true' : 'false');
        if (Media::media_class_get($item->object->type) === 'video') {
            if (!empty($item->settings['data-poster-is-embedded'])) {
                $result->child_select('body')->child_select('info')->child_select('title')->child_select('text')->text_append(
                    ' + '.(new Text('poster'))->render());
                $result->child_select('body')->child_insert(new Markup_simple('img', [
                    'data-type' => 'thumbnail',
                    'src'       => Core::to_url_from_path($item->object->get_current_path(true)).'?poster=small',
                    'alt'       => new Text('thumbnail'),
                    'width'     => '44',
                    'height'    => '44'
                ], +450), 'thumbnail');
            }
        }
        return $result;
    }

    static function widget_insert_get($widget, $group = '') {
        $result = new Markup('x-widget', ['data-type' => 'insert']);
        # control for upload new video
        $field_file_video = new Field_File_video;
        $field_file_video->title             = 'Video';
        $field_file_video->max_file_size     = $widget->{($group ? $group.'_' : '').'max_file_size'};
        $field_file_video->types_allowed     = $widget->{($group ? $group.'_' : '').'types_allowed'};
        $field_file_video->cform             = $widget->cform;
        $field_file_video->min_files_number  = null;
        $field_file_video->max_files_number  = null;
        $field_file_video->has_widget_insert = false;
        $field_file_video->has_widget_manage = false;
        $field_file_video->build();
        $field_file_video->name_set($widget->name_get_complex().'__file'.($group ? '_'.$group : ''));
        # control for upload new video poster
        $field_file_picture_poster = new Field_File_picture;
        $field_file_picture_poster->title             = 'Poster';
        $field_file_picture_poster->max_file_size     = $widget->poster_max_file_size;
        $field_file_picture_poster->types_allowed     = $widget->poster_types_allowed;
        $field_file_picture_poster->cform             = $widget->cform;
        $field_file_picture_poster->min_files_number  = null;
        $field_file_picture_poster->max_files_number  = null;
        $field_file_picture_poster->has_widget_insert = false;
        $field_file_picture_poster->has_widget_manage = false;
        $field_file_picture_poster->build();
        $field_file_picture_poster->name_set($widget->name_get_complex().'__poster');
        # button for insertion of the new item
        $button_insert = new Button(null, ['data-style' => 'insert', 'title' => new Text('insert')]);
        $button_insert->break_on_validate = true;
        $button_insert->build();
        $button_insert->value_set($widget->name_get_complex().'__insert'.($group ? '_'.$group : ''));
        $button_insert->_type = 'insert';
        $button_insert->_kind = 'video';
        # relate new controls with the widget
        if (true                      ) $widget->controls[  '#file'.($group ? '_'.$group : '')] = $field_file_video;
        if ($widget->poster_is_allowed) $widget->controls['#poster'                           ] = $field_file_picture_poster;
        if (true                      ) $widget->controls['~insert'.($group ? '_'.$group : '')] = $button_insert;
        if (true                      ) $result->child_insert($field_file_video         , 'field_file_video');
        if ($widget->poster_is_allowed) $result->child_insert($field_file_picture_poster, 'field_file_picture_poster');
        if (true                      ) $result->child_insert($button_insert            , 'button_insert');
        return $result;
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function on_file_prepare($widget, $form, $npath, $button, &$items, &$new_item) {
        if (parent::on_file_prepare($widget, $form, $npath, $button,  $items,  $new_item)) {
            $new_item->settings = $widget->video_player_default_settings;
            $new_item->settings['data-poster-is-embedded'] = false;
            if ($widget->poster_is_allowed) {
                if (Media::media_class_get($new_item->object->type) === 'video') {
                    $values = Event::start_local('on_values_validate', $widget, ['form' => $form, 'npath' => $npath, 'button' => $button, 'name' => '#poster']);
                    $poster = reset($values);
                    if ($poster instanceof File_history) {
                        if (Media::media_class_get($poster->type) === 'picture') {
                            if (Media::is_type_for_thumbnail($poster->type)) {
                                $pre_path = $new_item->object->get_current_path();
                                if ($poster->move_tmp_to_pre($pre_path.'.'.$poster->type)) {
                                    if ($new_item->object->container_video_make($widget->poster_thumbnails, $poster->get_current_path())) {
                                        $new_item->settings['data-poster-is-embedded'] = true;
                                        File::delete($pre_path.'.'.$poster->type);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return true;
        }
    }

    static function on_button_click_insert($widget, $form, $npath, $button) {
        if ($widget->poster_is_allowed) {
            $values        = Event::start_local('on_values_validate', $widget, ['form' => $form, 'npath' => $npath, 'button' => $button, 'name' => '#file'  ]);
            $values_poster = Event::start_local('on_values_validate', $widget, ['form' => $form, 'npath' => $npath, 'button' => $button, 'name' => '#poster']);
            if (!$widget->controls['#file']->has_error() &&                                               count($values) === 0) {$widget->controls['#file']->error_set('Field "%%_title" cannot be blank!', ['title' => (new Text($widget->controls['#file']->title))->render() ]); return;}
            if (!$widget->controls['#file']->has_error() && !$widget->controls['#poster']->has_error() && count($values) !== 0)
               return Widget_Files::on_button_click_insert($widget, $form, $npath, $button);
        } else return Widget_Files::on_button_click_insert($widget, $form, $npath, $button);
    }

}
