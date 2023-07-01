<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Field_File_video extends Field_File {

    public $title = 'Video';
    public $attributes = ['data-type' => 'file-video'];
    public $element_attributes = [
        'type' => 'file',
        'name' => 'video'];
    public $max_file_size = '50M';
    public $types_allowed = [
        'mp4' => 'mp4'
    ];

    ###########################
    ### static declarations ###
    ###########################

    static function widget_manage_action_text_get($field, $item, $id, $scope) {
        return new Text('video "%%_video"', ['video' => $item->file]);
    }

}
