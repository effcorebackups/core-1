<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

trait Widget_Files_videos__Shared {

    public $poster_is_allowed = true;
    public $poster_thumbnails = [
        'small'  => 'small',
        'middle' => 'middle'];
    public $poster_max_file_size = '1M';
    public $poster_types_allowed = [
        'png'  => 'png',
        'gif'  => 'gif',
        'jpg'  => 'jpg',
        'jpeg' => 'jpeg'];
    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
    public $video_player_default_settings = [
        'autoplay'    => null,
        'buffered'    => null,
        'controls'    => true,
        'crossorigin' => null,
        'loop'        => null,
        'muted'       => null,
        'played'      => null,
        'preload'     => 'metadata'
    ];

}
