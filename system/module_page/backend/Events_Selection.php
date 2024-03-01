<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\page;

use effcore\Core;
use effcore\Decorator;
use effcore\Entity;
use effcore\Field_Checkbox;
use effcore\Markup_simple;
use effcore\Markup;
use effcore\Media;
use effcore\Module;
use effcore\Text_simple;
use effcore\Text;
use effcore\URL;
use effcore\Widget_Attributes;
use effcore\Widget_Files_audios;
use effcore\Widget_Files_pictures;
use effcore\Widget_Files_videos;

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
                   return new Markup('a', ['href' => Core::to_url_from_path($c_instance->path), 'target' => '_blank'], new Text_simple(Core::to_url_from_path($c_instance->path)));
            } else return '';
        }     else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'path']);
    }

    static function handler__any__paths_as_links($c_cell_id, $c_row, $c_instance, $origin) {
        if (array_key_exists('path', $c_instance->values_get())) {
            if ($c_instance->path) {
                $url = Core::to_url_from_path($c_instance->path);
                $result = new Markup('ul', ['data-type' => 'paths']);
                $result->child_insert(new Markup('li', ['data-name' => 'original'], new Markup('a', ['href' => $url                , 'target' => '_blank'], new Text_simple($url                ))), 'original');
                $result->child_insert(new Markup('li', ['data-name' => 'small'   ], new Markup('a', ['href' => $url.'?thumb=small' , 'target' => '_blank'], new Text_simple($url.'?thumb=small' ))), 'small'   );
                $result->child_insert(new Markup('li', ['data-name' => 'middle'  ], new Markup('a', ['href' => $url.'?thumb=middle', 'target' => '_blank'], new Text_simple($url.'?thumb=middle'))), 'middle'  );
                $result->child_insert(new Markup('li', ['data-name' => 'big'     ], new Markup('a', ['href' => $url.'?thumb=big'   , 'target' => '_blank'], new Text_simple($url.'?thumb=big'   ))), 'big'     );
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
                $url_absolute = (new URL($c_instance->url))->absolute_get();
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
                    $attributes['src'] = Core::to_url_from_path($c_instance->path);
                       return new Markup('audio', $attributes);
                } else return '';
            }     else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'attributes']);
        }         else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'path']);
    }

    static function handler__audio__cover_paths_as_links($c_cell_id, $c_row, $c_instance, $origin) {
        if (array_key_exists('cover_path', $c_instance->values_get())) {
            if ($c_instance->cover_path) {
                $url = Core::to_url_from_path($c_instance->cover_path);
                $result = new Markup('ul', ['data-type' => 'paths']);
                $result->child_insert(new Markup('li', ['data-name' => 'original'], new Markup('a', ['href' => $url                , 'target' => '_blank'], new Text_simple($url                ))), 'original');
                $result->child_insert(new Markup('li', ['data-name' => 'small'   ], new Markup('a', ['href' => $url.'?thumb=small' , 'target' => '_blank'], new Text_simple($url.'?thumb=small' ))), 'small'   );
                $result->child_insert(new Markup('li', ['data-name' => 'middle'  ], new Markup('a', ['href' => $url.'?thumb=middle', 'target' => '_blank'], new Text_simple($url.'?thumb=middle'))), 'middle'  );
                $result->child_insert(new Markup('li', ['data-name' => 'big'     ], new Markup('a', ['href' => $url.'?thumb=big'   , 'target' => '_blank'], new Text_simple($url.'?thumb=big'   ))), 'big'     );
                   return $result;
            } else return '';
        }     else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'cover_path']);
    }

    static function handler__video__poster_paths_as_links($c_cell_id, $c_row, $c_instance, $origin) {
        if (array_key_exists('poster_path', $c_instance->values_get())) {
            if ($c_instance->poster_path) {
                $url = Core::to_url_from_path($c_instance->poster_path);
                $result = new Markup('ul', ['data-type' => 'paths']);
                $result->child_insert(new Markup('li', ['data-name' => 'original'], new Markup('a', ['href' => $url                , 'target' => '_blank'], new Text_simple($url                ))), 'original');
                $result->child_insert(new Markup('li', ['data-name' => 'small'   ], new Markup('a', ['href' => $url.'?thumb=small' , 'target' => '_blank'], new Text_simple($url.'?thumb=small' ))), 'small'   );
                $result->child_insert(new Markup('li', ['data-name' => 'middle'  ], new Markup('a', ['href' => $url.'?thumb=middle', 'target' => '_blank'], new Text_simple($url.'?thumb=middle'))), 'middle'  );
                $result->child_insert(new Markup('li', ['data-name' => 'big'     ], new Markup('a', ['href' => $url.'?thumb=big'   , 'target' => '_blank'], new Text_simple($url.'?thumb=big'   ))), 'big'     );
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
                         $attributes['poster'] = Core::to_url_from_path($c_instance->poster_path);
                    else $attributes['poster'] = Core::to_url_from_path($settings->thumbnail_path_poster_default);
                    $attributes['src'] = Core::to_url_from_path($c_instance->path);
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
                $this_attributes['src'] = Core::to_url_from_path($c_instance->path);
                if (isset($c_instance->url)) {
                       $link_attributes[ 'href' ] = (new URL($c_instance->url))->absolute_get();
                       $link_attributes['target'] = '_blank';
                       return new Markup('a', $link_attributes, new Markup_simple('img', $this_attributes));
                } else return                                   new Markup_simple('img', $this_attributes);
            }     else return '';
        }         else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'path']);
    }

    static function handler__gallery__items_manage($c_cell_id, $c_row, $c_instance, $origin) {
        if (array_key_exists('items', $c_instance->values_get())) {
            $value = Core::deep_clone($c_instance->items);
            $decorator = new Decorator;
            $decorator->id = 'widget_files-multimedia-items';
            $decorator->view_type = 'template';
            $decorator->template = 'gallery';
            $decorator->template_item = 'gallery_item';
            $decorator->mapping = Core::array_keys_map(['num', 'type', 'children']);
            if ($value) {
                Core::array_sort_by_number($value);
                foreach ($value as $c_row_id => $c_item) {
                    if (Core::in_array(Media::media_class_get($c_item->object->type), ['picture', 'audio', 'video'])) {
                        if (Media::media_class_get($c_item->object->type) === 'picture') {
                            $c_url = Core::to_url_from_path($c_item->object->get_current_path(true));
                            $c_urls = new Markup('ul', ['data-type' => 'paths']);
                            $c_urls->child_insert(new Markup('li', ['data-name' => 'small' ], new Markup('a', ['href' => $c_url.'?thumb=small' , 'target' => '_blank'], 'small' )), 'small' );
                            $c_urls->child_insert(new Markup('li', ['data-name' => 'middle'], new Markup('a', ['href' => $c_url.'?thumb=middle', 'target' => '_blank'], 'middle')), 'middle');
                            $c_urls->child_insert(new Markup('li', ['data-name' => 'big'   ], new Markup('a', ['href' => $c_url.'?thumb=big'   , 'target' => '_blank'], 'big'   )), 'big'   );
                            $decorator->data[$c_row_id] = [
                                'type'     => ['value' => 'picture', 'is_apply_translation' => false],
                                'num'      => ['value' => $c_row_id, 'is_apply_translation' => false],
                                'children' => ['value' => Widget_Files_pictures::render_item($c_item, $c_row_id).$c_urls->render()]
                            ];
                        }
                        if (Media::media_class_get($c_item->object->type) === 'video') {
                            $c_url = Core::to_url_from_path($c_item->object->get_current_path(true));
                            $c_urls = new Markup('ul', ['data-type' => 'paths']);
                            if ($c_item->settings['data-poster-is-embedded']) $c_urls->child_insert(new Markup('li', ['data-name' => 'original'], new Markup('a', ['href' => $c_url.'?poster'       , 'target' => '_blank'], 'poster original')), 'original');
                            if ($c_item->settings['data-poster-is-embedded']) $c_urls->child_insert(new Markup('li', ['data-name' => 'small'   ], new Markup('a', ['href' => $c_url.'?poster=small' , 'target' => '_blank'], 'poster small'   )), 'small'   );
                            if ($c_item->settings['data-poster-is-embedded']) $c_urls->child_insert(new Markup('li', ['data-name' => 'middle'  ], new Markup('a', ['href' => $c_url.'?poster=middle', 'target' => '_blank'], 'poster middle'  )), 'middle'  );
                            if ($c_item->settings['data-poster-is-embedded']) $c_urls->child_insert(new Markup('li', ['data-name' => 'big'     ], new Markup('a', ['href' => $c_url.'?poster=big'   , 'target' => '_blank'], 'poster big'     )), 'big'     );
                                                                              $c_urls->child_insert(new Markup('li', ['data-name' => 'main'    ], new Markup('a', ['href' => $c_url                 , 'target' => '_blank'], 'video'          )), 'main'    );
                            $decorator->data[$c_row_id] = [
                                'type'     => ['value' => 'video'  , 'is_apply_translation' => false],
                                'num'      => ['value' => $c_row_id, 'is_apply_translation' => false],
                                'children' => ['value' => Widget_Files_videos::render_item($c_item, $c_row_id).$c_urls->render()]
                            ];
                        }
                        if (Media::media_class_get($c_item->object->type) === 'audio') {
                            $c_url = Core::to_url_from_path($c_item->object->get_current_path(true));
                            $c_urls = new Markup('ul', ['data-type' => 'paths']);
                            if ($c_item->settings['data-cover-is-embedded']) $c_urls->child_insert(new Markup('li', ['data-name' => 'original'], new Markup('a', ['href' => $c_url.'?cover'       , 'target' => '_blank'], 'cover original')), 'original');
                            if ($c_item->settings['data-cover-is-embedded']) $c_urls->child_insert(new Markup('li', ['data-name' => 'small'   ], new Markup('a', ['href' => $c_url.'?cover=small' , 'target' => '_blank'], 'cover small'   )), 'small'   );
                            if ($c_item->settings['data-cover-is-embedded']) $c_urls->child_insert(new Markup('li', ['data-name' => 'middle'  ], new Markup('a', ['href' => $c_url.'?cover=middle', 'target' => '_blank'], 'cover middle'  )), 'middle'  );
                            if ($c_item->settings['data-cover-is-embedded']) $c_urls->child_insert(new Markup('li', ['data-name' => 'big'     ], new Markup('a', ['href' => $c_url.'?cover=big'   , 'target' => '_blank'], 'cover big'     )), 'big'     );
                                                                             $c_urls->child_insert(new Markup('li', ['data-name' => 'main'    ], new Markup('a', ['href' => $c_url                , 'target' => '_blank'], 'audio'         )), 'main'    );
                            $decorator->data[$c_row_id] = [
                                'type'     => ['value' => 'audio'  , 'is_apply_translation' => false],
                                'num'      => ['value' => $c_row_id, 'is_apply_translation' => false],
                                'children' => ['value' => Widget_Files_audios::render_item($c_item, $c_row_id).$c_urls->render()]
                            ];
                        }
                    }
                }
            }
               return $decorator;
        } else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'items']);
    }

}
