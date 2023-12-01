<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\page;

use effcore\Entity;
use effcore\Field_Checkbox;
use effcore\Markup_simple;
use effcore\Markup;
use effcore\Module;
use effcore\Template;
use effcore\Text_simple;
use effcore\Text;
use effcore\Url;
use effcore\Widget_Attributes;

abstract class Events_Selection {

    ###############################
    ### handlers for any entity ###
    ###############################

    static $cache_ids = [];

    static function handler__any__checkbox_select($c_cell_id, $c_row, $c_instance, $origin) {
        if (isset($origin->settings['instance_id'])) {
            $checkbox = new Field_Checkbox;
            $checkbox->build();
            $checkbox-> name_set($origin->settings['name'] ?? 'is_checked[]');
            $checkbox->value_set($origin->settings['instance_id']);
            return $checkbox;
        }
        if ($c_instance) {
            $entity = Entity::get($c_instance->entity_name);
            if (!array_key_exists($c_instance->entity_name, static::$cache_ids) && $entity === null) static::$cache_ids[$c_instance->entity_name] = null;
            if (!array_key_exists($c_instance->entity_name, static::$cache_ids) && $entity !== null) static::$cache_ids[$c_instance->entity_name] = $entity->id_get();
            $ids = static::$cache_ids[$c_instance->entity_name];
            if ($ids !== null) {
                $values = $c_instance->values_get();
                foreach ($ids as $c_id)
                    if (!array_key_exists($c_id, $values))
                        return new Text('FIELD "%%_name" IS REQUIRED', ['name' => $c_id]);
                $checkbox = new Field_Checkbox;
                $checkbox->build();
                $checkbox-> name_set($origin->settings['name'] ?? 'is_checked[]');
                $checkbox->value_set(implode('+', $c_instance->values_id_get()));
                   return $checkbox;
            } else return new Text('Entity "%%_name" is not available.', ['name' => $c_instance->entity_name]);
        }
    }

    static function handler__any__path_as_link($c_cell_id, $c_row, $c_instance, $origin) {
        if (array_key_exists('path', $c_instance->values_get())) {
            if ($c_instance->path) {
                   return new Markup('a', ['href' => '/'.$c_instance->path, 'target' => '_blank'], new Text_simple('/'.$c_instance->path));
            } else return '';
        }     else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'path']);
    }

    static function handler__any__paths_as_links($c_cell_id, $c_row, $c_instance, $origin) {
        if (array_key_exists('path', $c_instance->values_get())) {
            if ($c_instance->path) {
                $result = new Markup('ul', ['data-type' => 'paths']);
                $result->child_insert(new Markup('li', ['data-name' => 'original'], new Markup('a', ['href' => '/'.$c_instance->path                , 'target' => '_blank'], new Text_simple('/'.$c_instance->path                ))), 'original');
                $result->child_insert(new Markup('li', ['data-name' => 'small'   ], new Markup('a', ['href' => '/'.$c_instance->path.'?thumb=small' , 'target' => '_blank'], new Text_simple('/'.$c_instance->path.'?thumb=small' ))), 'small'   );
                $result->child_insert(new Markup('li', ['data-name' => 'middle'  ], new Markup('a', ['href' => '/'.$c_instance->path.'?thumb=middle', 'target' => '_blank'], new Text_simple('/'.$c_instance->path.'?thumb=middle'))), 'middle'  );
                $result->child_insert(new Markup('li', ['data-name' => 'big'     ], new Markup('a', ['href' => '/'.$c_instance->path.'?thumb=big'   , 'target' => '_blank'], new Text_simple('/'.$c_instance->path.'?thumb=big'   ))), 'big'     );
                   return $result;
            } else return '';
        }     else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'path']);
    }

    static function handler__any__url_as_link($c_cell_id, $c_row, $c_instance, $origin) {
        if (array_key_exists('url', $c_instance->values_get())) {
            if ($c_instance->url) {
                   return new Markup('a', ['href' => $c_instance->url, 'target' => '_blank'], new Text_simple($c_instance->url));
            } else return '';
        }     else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'url']);
    }

    static function handler__any__url_as_link_absolute($c_cell_id, $c_row, $c_instance, $origin) {
        if (array_key_exists('url', $c_instance->values_get())) {
            if ($c_instance->url) {
                $url_absolute = (new Url($c_instance->url))->absolute_get();
                   return new Markup('a', ['href' => $url_absolute, 'target' => '_blank'], new Text_simple($url_absolute));
            } else return '';
        }     else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'url']);
    }

    ##################################
    ### handlers for 'page' entity ###
    ##################################

    static function handler__page__url_as_link($c_cell_id, $c_row, $c_instance, $origin) {
        if (array_key_exists('url', $c_instance->values_get())) {
            if (!str_contains($c_instance->url, '%%_')) {
                   return new Markup('a', ['href' => $c_instance->url, 'target' => '_blank'],
                          new Text_simple($c_instance->url));
            } else return new Text_simple($c_instance->url);
        }     else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'url']);
    }

    static function handler__page__text_direction($c_cell_id, $c_row, $c_instance, $origin) {
        if (array_key_exists('text_direction', $c_instance->values_get())) {
            if ($c_instance->text_direction === 'ltr') return 'left to right (ltr)';
            if ($c_instance->text_direction === 'rtl') return 'right to left (rtl)';
        } else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'text_direction']);
    }

    #########################################################
    ### handlers for 'audio', 'video', 'picture' entities ###
    #########################################################

    static function handler__audio__pre_listening($c_cell_id, $c_row, $c_instance, $origin) {
        if (array_key_exists('path', $c_instance->values_get())) {
            if (array_key_exists('attributes', $c_instance->values_get())) {
                if ($c_instance->path) {
                    $attributes = Widget_Attributes::value_to_attributes($c_instance->attributes ?? [], $origin->is_apply_translation ?? true);
                    $attributes['src'] = '/'.$c_instance->path;
                       return new Markup('audio', $attributes);
                } else return '';
            }     else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'attributes']);
        }         else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'path']);
    }

    static function handler__audio__cover_paths_as_links($c_cell_id, $c_row, $c_instance, $origin) {
        if (array_key_exists('cover_path', $c_instance->values_get())) {
            if ($c_instance->cover_path) {
                $result = new Markup('ul', ['data-type' => 'paths']);
                $result->child_insert(new Markup('li', ['data-name' => 'original'], new Markup('a', ['href' => '/'.$c_instance->cover_path                , 'target' => '_blank'], new Text_simple('/'.$c_instance->cover_path                ))), 'original');
                $result->child_insert(new Markup('li', ['data-name' => 'small'   ], new Markup('a', ['href' => '/'.$c_instance->cover_path.'?thumb=small' , 'target' => '_blank'], new Text_simple('/'.$c_instance->cover_path.'?thumb=small' ))), 'small'   );
                $result->child_insert(new Markup('li', ['data-name' => 'middle'  ], new Markup('a', ['href' => '/'.$c_instance->cover_path.'?thumb=middle', 'target' => '_blank'], new Text_simple('/'.$c_instance->cover_path.'?thumb=middle'))), 'middle'  );
                $result->child_insert(new Markup('li', ['data-name' => 'big'     ], new Markup('a', ['href' => '/'.$c_instance->cover_path.'?thumb=big'   , 'target' => '_blank'], new Text_simple('/'.$c_instance->cover_path.'?thumb=big'   ))), 'big'     );
                   return $result;
            } else return '';
        }     else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'cover_path']);
    }

    static function handler__video__poster_paths_as_links($c_cell_id, $c_row, $c_instance, $origin) {
        if (array_key_exists('poster_path', $c_instance->values_get())) {
            if ($c_instance->poster_path) {
                $result = new Markup('ul', ['data-type' => 'paths']);
                $result->child_insert(new Markup('li', ['data-name' => 'original'], new Markup('a', ['href' => '/'.$c_instance->poster_path                , 'target' => '_blank'], new Text_simple('/'.$c_instance->poster_path                ))), 'original');
                $result->child_insert(new Markup('li', ['data-name' => 'small'   ], new Markup('a', ['href' => '/'.$c_instance->poster_path.'?thumb=small' , 'target' => '_blank'], new Text_simple('/'.$c_instance->poster_path.'?thumb=small' ))), 'small'   );
                $result->child_insert(new Markup('li', ['data-name' => 'middle'  ], new Markup('a', ['href' => '/'.$c_instance->poster_path.'?thumb=middle', 'target' => '_blank'], new Text_simple('/'.$c_instance->poster_path.'?thumb=middle'))), 'middle'  );
                $result->child_insert(new Markup('li', ['data-name' => 'big'     ], new Markup('a', ['href' => '/'.$c_instance->poster_path.'?thumb=big'   , 'target' => '_blank'], new Text_simple('/'.$c_instance->poster_path.'?thumb=big'   ))), 'big'     );
                   return $result;
            } else return '';
        }     else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'poster_path']);
    }

    static function handler__video__preview($c_cell_id, $c_row, $c_instance, $origin) {
        if (array_key_exists('path', $c_instance->values_get())) {
            if (array_key_exists('attributes', $c_instance->values_get())) {
                if ($c_instance->path) {
                    $settings = Module::settings_get('page');
                    $attributes = Widget_Attributes::value_to_attributes($c_instance->attributes ?? [], $origin->is_apply_translation ?? true);
                    if (!empty($c_instance->poster_path))
                         $attributes['poster'] = '/'.$c_instance->poster_path;
                    else $attributes['poster'] = '/'.$settings->thumbnail_path_poster_default;
                    $attributes['src'] = '/'.$c_instance->path;
                       return new Markup('video', $attributes);
                } else return '';
            }     else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'attributes']);
        }         else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'path']);
    }

    static function handler__picture__preview($c_cell_id, $c_row, $c_instance, $origin) {
        if (array_key_exists('path', $c_instance->values_get())) {
            if ($c_instance->path) {
                $link_attributes = Widget_Attributes::value_to_attributes($c_instance->link_attributes ?? [], $origin->is_apply_translation ?? true);
                $this_attributes = Widget_Attributes::value_to_attributes($c_instance->     attributes ?? [], $origin->is_apply_translation ?? true);
                $this_attributes['src'] = '/'.$c_instance->path;
                if (isset($c_instance->url)) {
                       $link_attributes[ 'href' ] = (new Url($c_instance->url))->absolute_get();
                       $link_attributes['target'] = '_blank';
                       return new Markup('a', $link_attributes, new Markup_simple('img', $this_attributes));
                } else return                                   new Markup_simple('img', $this_attributes);
            }     else return '';
        }         else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'path']);
    }

    #################
    ### TEMPLATES ###
    #################

    static function template__picture_or_picture_in_link_embedded($args, $args_raw) {
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
    }

}
