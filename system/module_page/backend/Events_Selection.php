<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\page;

use effcore\Markup_simple;
use effcore\Markup;
use effcore\Module;
use effcore\Template;
use effcore\Url;
use effcore\Widget_Attributes;

abstract class Events_Selection {

    static function handler__any__url_absolute($c_row_id, $c_row, $c_instance, $settings = []) {
        if ($c_row['url']['value']) {
            return (new Url($c_row['url']['value']))->absolute_get();
        } else return '';
    }

    static function handler__page__link($c_row_id, $c_row, $c_instance, $settings = []) {
        if (strpos($c_row['url']['value'], '%%_') === false)
             return new Markup('a', ['href' => $c_row['url']['value'], 'target' => '_blank'], $c_row['url']['value']);
        else return                                                                           $c_row['url']['value'];
    }

    static function handler__page__text_direction($c_row_id, $c_row, $c_instance, $settings = []) {
        if ($c_row['text_direction']['value'] === 'ltr') return 'left to right (ltr)';
        if ($c_row['text_direction']['value'] === 'rtl') return 'right to left (rtl)';
    }

    static function handler__audio__pre_listening($c_row_id, $c_row, $c_instance, $settings = []) {
        if ($c_row['path']['value']) {
            $attributes = Widget_Attributes::value_to_attributes($c_row['this_attributes']['value']) ?? [];
            $attributes['src'] = '/'.$c_row['path']['value'];
            return new Markup('audio', $attributes);
        } else return '—';
    }

    static function handler__video__view($c_row_id, $c_row, $c_instance, $settings = []) {
        if ($c_row['path']['value']) {
            $settings = Module::settings_get('page');
            $attributes = Widget_Attributes::value_to_attributes($c_row['this_attributes']['value']) ?? [];
            if (!empty($c_row['poster_path']['value']))
                 $attributes['poster'] = '/'.$c_row['poster_path']['value'];
            else $attributes['poster'] = '/'.$settings->thumbnail_path_poster_default;
            $attributes['src'] = '/'.$c_row['path']['value'];
            return new Markup('video', $attributes);
        } else return '—';
    }

    static function handler__picture__preview($c_row_id, $c_row, $c_instance, $settings = []) {
        if ($c_row['path']['value']) {
            $link_attributes = Widget_Attributes::value_to_attributes($c_row['link_attributes']['value']) ?? [];
            $this_attributes = Widget_Attributes::value_to_attributes($c_row['this_attributes']['value']) ?? [];
            $this_attributes['src'] = '/'.$c_row['path']['value'];
            if ($c_row['url']['value']) {
                   $link_attributes[ 'href' ] = (new Url($c_row['url']['value']))->absolute_get();
                   $link_attributes['target'] = '_blank';
                   return new Markup('a', $link_attributes, new Markup_simple('img', $this_attributes));
            } else return                                   new Markup_simple('img', $this_attributes);
        }     else return '—';
    }

    static function template__picture_or_picture_in_link_embedded($args, $args_raw) {
        if ($args['path']) {
            if (!empty($args['url'])) {
                $args['url'] = (new Url($args['url']))->absolute_get();
                return (Template::make_new(Template::pick_name('picture_in_link'), [
                    'id'              => $args['id']              ?? '',
                    'description'     => $args['description']     ?? '',
                    'attributes'      => $args['attributes']      ?? '',
                    'path'            => $args['path']            ?? '',
                    'link_attributes' => $args['link_attributes'] ?? '',
                    'url'             => $args['url']             ?? '',
                    'created'         => $args['created']         ?? '',
                    'updated'         => $args['updated']         ?? '',
                    'is_embedded'     => $args['is_embedded']     ?? '',
                ]))->render();
            } else {
                return (Template::make_new(Template::pick_name('picture'), [
                    'id'              => $args['id']          ?? '',
                    'description'     => $args['description'] ?? '',
                    'attributes'      => $args['attributes']  ?? '',
                    'path'            => $args['path']        ?? '',
                    'created'         => $args['created']     ?? '',
                    'updated'         => $args['updated']     ?? '',
                    'is_embedded'     => $args['is_embedded'] ?? '',
                ]))->render();
            }
        } else return '';
    }

}
