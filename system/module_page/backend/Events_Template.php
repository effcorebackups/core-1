<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\page;

use effcore\Core;
use effcore\Template;
use effcore\Url;

abstract class Events_Template {

    static function template__picture_or_picture_in_link__embedded($args, $args_raw) {
        if (!empty($args['url'])) {
            return (Template::make_new(Template::pick_name('picture_in_link'), [
                'url'             => (new Url($args['url']))->absolute_get(),
                'src'             => $args['src']             ?? '',
                'id'              => $args['id']              ?? '',
                'description'     => $args['description']     ?? '',
                'attributes'      => $args['attributes']      ?? '',
                'link_attributes' => $args['link_attributes'] ?? '',
                'created'         => $args['created']         ?? '',
                'updated'         => $args['updated']         ?? '',
                'is_embedded'     => $args['is_embedded']     ?? '',
            ]))->render();
        } else {
            return (Template::make_new(Template::pick_name('picture'), [
                'src'             => $args['src']         ?? '',
                'id'              => $args['id']          ?? '',
                'description'     => $args['description'] ?? '',
                'attributes'      => $args['attributes']  ?? '',
                'created'         => $args['created']     ?? '',
                'updated'         => $args['updated']     ?? '',
                'is_embedded'     => $args['is_embedded'] ?? '',
            ]))->render();
        }
    }

}
