<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class widget_files_audios extends widget_files {

    use widget_files_audios__shared;

    public $title = 'Audios';
    public $item_title = 'Audio';
    public $attributes = ['data-type' => 'items-files-audios'];
    public $name_complex = 'widget_files_audios';
    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
    public $upload_dir = 'audios/';
    public $fixed_name = 'audio-multiple-%%_item_id_context';
    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
    public $max_file_size = '10M';
    public $types_allowed = [
        'mp3' => 'mp3'
    ];

    ###########################
    ### static declarations ###
    ###########################

    static function value_to_markup($value) {
        $decorator = new decorator;
        $decorator->id = 'widget_files-audios-items';
        $decorator->view_type = 'template';
        $decorator->template = 'content';
        $decorator->template_item = 'gallery_item';
        $decorator->mapping = core::array_keys_map(['num', 'type', 'children']);
        if ($value) {
            core::array_sort_by_number($value);
            foreach ($value as $c_row_id => $c_item) {
                if (media::media_class_get($c_item->object->type) === 'audio') {
                    $decorator->data[$c_row_id] = [
                        'type'     => ['value' => 'audio'  ],
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
        if ($item->settings['data-cover-is-embedded'])
             return new node([], ['cover' => new markup_simple('img', ['src' => $src.'?cover=middle', 'alt' => new text('cover'), 'width' => '300', 'height' => '300', 'data-type' => 'cover']), 'audio' => new markup('audio', ['src' => $src, 'data-cover-thumbnail' => $src.'?cover=middle'] + $item->settings)]);
        else return new node([], [                                                                                                                                                               'audio' => new markup('audio', ['src' => $src                                                ] + $item->settings)]);
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function widget_manage_get($widget, $item, $c_row_id) {
        $result = parent::widget_manage_get($widget, $item, $c_row_id);
        $result->attribute_insert('data-is-new', $item->object->get_current_state() === 'pre' ? 'true' : 'false');
        if (media::media_class_get($item->object->type) === 'audio') {
            if (!empty($widget->audio_player_on_manage_is_visible)) $result->child_insert(new markup('audio',      ['src' => '/'.$item->object->get_current_path(true)] + $widget->audio_player_on_manage_settings, [], +500), 'player');
            if (!empty($item->settings['data-cover-is-embedded']))  $result->child_insert(new markup_simple('img', ['src' => '/'.$item->object->get_current_path(true).'?cover=small', 'alt' => new text('thumbnail'), 'width' => '44', 'height' => '44', 'data-type' => 'thumbnail'], +450), 'thumbnail');
        }
        return $result;
    }

    static function widget_insert_get($widget, $group = '') {
        $result = new markup('x-widget', ['data-type' => 'insert']);
        # control for upload new audio
        $field_file_audio = new field_file_audio;
        $field_file_audio->title             = 'Audio';
        $field_file_audio->max_file_size     = $widget->{($group ? $group.'_' : '').'max_file_size'};
        $field_file_audio->types_allowed     = $widget->{($group ? $group.'_' : '').'types_allowed'};
        $field_file_audio->cform             = $widget->cform;
        $field_file_audio->min_files_number  = null;
        $field_file_audio->max_files_number  = null;
        $field_file_audio->has_widget_insert = false;
        $field_file_audio->has_widget_manage = false;
        $field_file_audio->build();
        $field_file_audio->name_set($widget->name_get_complex().'__file'.($group ? '_'.$group : ''));
        # control for upload new audio cover
        $field_file_picture_cover = new field_file_picture;
        $field_file_picture_cover->title             = 'Cover';
        $field_file_picture_cover->max_file_size     = $widget->cover_max_file_size;
        $field_file_picture_cover->types_allowed     = $widget->cover_types_allowed;
        $field_file_picture_cover->cform             = $widget->cform;
        $field_file_picture_cover->min_files_number  = null;
        $field_file_picture_cover->max_files_number  = null;
        $field_file_picture_cover->has_widget_insert = false;
        $field_file_picture_cover->has_widget_manage = false;
        $field_file_picture_cover->build();
        $field_file_picture_cover->name_set($widget->name_get_complex().'__cover');
        # button for insertion of the new item
        $button_insert = new button(null, ['data-style' => 'insert', 'title' => new text('insert')]);
        $button_insert->break_on_validate = true;
        $button_insert->build();
        $button_insert->value_set($widget->name_get_complex().'__insert'.($group ? '_'.$group : ''));
        $button_insert->_type = 'insert';
        $button_insert->_kind = 'audio';
        # relate new controls with the widget
        if (true                     ) $widget->controls[  '#file'.($group ? '_'.$group : '')] = $field_file_audio;
        if ($widget->cover_is_allowed) $widget->controls['#cover'                            ] = $field_file_picture_cover;
        if (true                     ) $widget->controls['~insert'.($group ? '_'.$group : '')] = $button_insert;
        if (true                     ) $result->child_insert($field_file_audio,         'field_file_audio');
        if ($widget->cover_is_allowed) $result->child_insert($field_file_picture_cover, 'field_file_picture_cover');
        if (true                     ) $result->child_insert($button_insert,            'button_insert');
        return $result;
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function on_file_prepare($widget, $form, $npath, $button, &$items, &$new_item) {
        $pre_path = temporary::DIRECTORY.'validation/'.$form->validation_cache_date_get().'/'.$form->validation_id.'-'.$widget->name_get_complex().'-'.core::array_key_last($items).'.'.$new_item->object->type;
        if ($new_item->object->move_tmp_to_pre($pre_path)) {
            $new_item->settings = $widget->audio_player_default_settings;
            $new_item->settings['data-cover-is-embedded'] = false;
            if ($widget->cover_is_allowed) {
                if (media::media_class_get($new_item->object->type) === 'audio') {
                    $values = event::start_local('on_values_validate', $widget, ['form' => $form, 'npath' => $npath, 'button' => $button, 'name' => '#cover']);
                    $cover = reset($values);
                    if ($cover instanceof file_history) {
                        if (media::media_class_get($cover->type) === 'picture') {
                            if (media::is_type_for_thumbnail($cover->type)) {
                                if ($cover->move_tmp_to_pre($pre_path.'.'.$cover->type)) {
                                    if ($new_item->object->container_audio_make($widget->cover_thumbnails, $cover->get_current_path())) {
                                        $new_item->settings['data-cover-is-embedded'] = true;
                                        @unlink($pre_path.'.'.$cover->type);
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
        if ($widget->cover_is_allowed) {
            $values       = event::start_local('on_values_validate', $widget, ['form' => $form, 'npath' => $npath, 'button' => $button, 'name' => '#file' ]);
            $values_cover = event::start_local('on_values_validate', $widget, ['form' => $form, 'npath' => $npath, 'button' => $button, 'name' => '#cover']);
            if (!$widget->controls['#file']->has_error() &&                                              count($values) === 0) {$widget->controls['#file']->error_set('Field "%%_title" cannot be blank!', ['title' => (new text($widget->controls['#file']->title))->render() ]); return;}
            if (!$widget->controls['#file']->has_error() && !$widget->controls['#cover']->has_error() && count($values) !== 0)
               return widget_files::on_button_click_insert($widget, $form, $npath, $button);
        } else return widget_files::on_button_click_insert($widget, $form, $npath, $button);
    }

}
