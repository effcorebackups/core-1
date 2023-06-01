<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class field_file_video extends field_file {

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
        return new text('video "%%_video"', ['video' => $item->file]);
    }

}
