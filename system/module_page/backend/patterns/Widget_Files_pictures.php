<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Widget_Files_pictures extends Widget_Files {

    use Widget_Files_pictures__Shared;

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
        $decorator = new Decorator;
        $decorator->id = 'widget_files-pictures-items';
        $decorator->view_type = 'template';
        $decorator->template = 'content';
        $decorator->template_item = 'gallery_item';
        $decorator->mapping = Core::array_keys_map(['num', 'type', 'children']);
        if ($value) {
            Core::array_sort_by_number($value);
            foreach ($value as $c_row_id => $c_item) {
                if (Media::media_class_get($c_item->object->type) === 'picture') {
                    $decorator->data[$c_row_id] = [
                        'type'     => ['value' => 'picture', 'is_apply_translation' => false],
                        'num'      => ['value' => $c_row_id, 'is_apply_translation' => false],
                        'children' => ['value' => static::render_item($c_item, $c_row_id)]
                    ];
                }
            }
        }
        return $decorator;
    }

    static function render_item($item, $row_id) {
        $url = Core::to_url_from_path($item->object->get_current_path(true));
        return Template::make_new(Template::pick_name('picture_in_link'), [
            'id'  => $row_id,
            'url' => $url, /* link to original size if JS is disabled */
            'src' => $url ? $url.'?thumb=middle' : '',
            'link_attributes' => Core::data_to_attributes([
                'data-type' => 'picture-wrapper',
                'title'     => new Text($item->settings['title']),
                'target'    => $item->settings['target']]),
            'attributes' => Core::data_to_attributes([
                'alt'                    => new Text($item->settings['alt']),
                'data-path-thumb-small'  => $url ? $url.'?thumb=small'  : '',
                'data-path-thumb-middle' => $url ? $url.'?thumb=middle' : '',
                'data-path-thumb-big'    => $url ? $url.'?thumb=big'    : '',
            ])
        ])->render();
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function widget_manage_get($widget, $item, $c_row_id) {
        $result = parent::widget_manage_get($widget, $item, $c_row_id);
        $result->child_select('head')->child_select('button_delete')->_kind = 'picture';
        $result->attribute_insert('data-is-new', $item->object->get_current_state() === 'pre' ? 'true' : 'false');
        if (Media::media_class_get($item->object->type) === 'picture') {
            if (!empty($item->settings['data-thumbnails-is-embedded'])) {
                $result->child_select('body')->child_insert(new Markup_simple('img', [
                    'data-type' => 'thumbnail',
                    'src'       => Core::to_url_from_path($item->object->get_current_path(true)).'?thumb=small',
                    'alt'       => new Text('thumbnail'),
                    'width'     => '44',
                    'height'    => '44',
                ], +450), 'thumbnail');
            }
        }
        return $result;
    }

    static function widget_insert_get($widget, $group = '') {
        $result = new Markup('x-widget', ['data-type' => 'insert']);
        # control for upload new picture
        $field_file_picture = new Field_File_picture;
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
        $button_insert = new Button(null, ['data-style' => 'insert', 'title' => new Text('insert')]);
        $button_insert->break_on_validate = true;
        $button_insert->build();
        $button_insert->value_set($widget->name_get_complex().'__insert'.($group ? '_'.$group : ''));
        $button_insert->_type = 'insert';
        $button_insert->_kind = 'picture';
        # relate new controls with the widget
        $widget->controls[  '#file'.($group ? '_'.$group : '')] = $field_file_picture;
        $widget->controls['~insert'.($group ? '_'.$group : '')] = $button_insert;
        $result->child_insert($field_file_picture, 'field_file_picture');
        $result->child_insert($button_insert     , 'button_insert');
        return $result;
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function on_file_prepare($widget, $form, $npath, $button, &$items, &$new_item) {
        if (parent::on_file_prepare($widget, $form, $npath, $button,  $items,  $new_item)) {
            $new_item->settings = $widget->picture_default_settings;
            $new_item->settings['data-thumbnails-is-embedded'] = false;
            if ($widget->thumbnails_is_allowed) {
                if (Media::media_class_get($new_item->object->type) === 'picture') {
                    if (Media::is_type_for_thumbnail($new_item->object->type)) {
                        if ($new_item->object->container_picture_make($widget->thumbnails)) {
                            $new_item->settings['data-thumbnails-is-embedded'] = true;
                        }
                    }
                }
            }
            return true;
        }
    }

}
