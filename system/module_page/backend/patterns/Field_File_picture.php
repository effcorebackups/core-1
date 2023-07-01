<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Field_File_picture extends Field_File {

    public $title = 'Picture';
    public $item_title = 'Picture';
    public $attributes = ['data-type' => 'file-picture'];
    public $element_attributes = [
        'type' => 'file',
        'name' => 'picture'];
    public $max_file_size = '500K';
    public $types_allowed = [
        'jpg'  => 'jpg',
        'jpeg' => 'jpeg',
        'png'  => 'png',
        'gif'  => 'gif'];
    public $thumbnails_is_allowed = true;
    public $thumbnails = [];

    function items_set($scope, $items) {
        if ($this->thumbnails_is_allowed)
            if (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[1]['function'] === 'on_button_click_insert')
                foreach ($items as $c_item)
                    if (Media::media_class_get($c_item->type) === 'picture')
                        if (Media::is_type_for_thumbnail($c_item->type))
                            if ($c_item->get_current_state() === 'pre')
                                $c_item->container_picture_make($this->thumbnails);
        parent::items_set($scope, $items);
    }

    ###########################
    ### static declarations ###
    ###########################

    static function widget_manage_action_text_get($field, $item, $id, $scope) {
        if ($field->thumbnails_is_allowed) {
            $thumbnail_markup = new Markup_simple('img', ['src' => '/'.$item->get_current_path(true).'?thumb=small', 'alt' => new Text('thumbnail'), 'width' => '44', 'height' => '44', 'data-type' => 'thumbnail'], +450);
               return new Node([], [$thumbnail_markup, new Text('picture "%%_picture"', ['picture' => $item->file])]);
        } else return new Node([], [                   new Text('picture "%%_picture"', ['picture' => $item->file])]);
    }

}
