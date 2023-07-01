<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\develop;

use const effcore\BR;
use effcore\Core;
use effcore\Decorator;
use effcore\Event;
use effcore\File;
use effcore\Language;
use effcore\Markup;
use effcore\Node;
use effcore\Page;
use effcore\Selection;
use effcore\Tab_item;
use effcore\Template;
use effcore\Text_simple;
use effcore\Text;
use effcore\Token;
use effcore\Translation;
use effcore\Tree_item;
use effcore\Tree;
use effcore\Url;

abstract class Events_Page_Data_NoSQL {

    static function on_redirect($event, $page) {
        $type = Page::get_current()->args_get('type');
        $id   = Page::get_current()->args_get('id');
        if ($type === null) Url::go($page->args_get('base').'/events');
        if ($type === 'trees') {
            $trees = Tree::select_all('nosql');
            Core::array_sort_by_string($trees);
            if (empty($trees[$id])) {
                Url::go($page->args_get('base').'/trees/'.reset($trees)->id);
            }
        }
        if ($type === 'translations') {
            $languages = Language::get_all();
            Core::array_sort_by_string($languages, 'title_en', Core::SORT_DSC, false);
            unset($languages['en']);
            if (count($languages) === 0 && $id !== null          ) Url::go($page->args_get('base').'/translations/'                        );
            if (count($languages) !== 0 && empty($languages[$id])) Url::go($page->args_get('base').'/translations/'.reset($languages)->code);
        }
    }

    static function on_tab_build_before($event, $tab) {
        $type = Page::get_current()->args_get('type');
        if ($type === 'trees') {
            $trees = Tree::select_all('nosql');
            Core::array_sort_by_string($trees);
            foreach ($trees as $c_tree) {
                Tab_item::insert(                         $c_tree->title,
                    'nosql_trees_'                       .$c_tree->id,
                    'nosql_trees', 'data_nosql', 'trees/'.$c_tree->id, null, [], [], false, 0, 'develop'
                );
            }
        }
        if ($type === 'translations') {
            $languages = Language::get_all();
            Core::array_sort_by_string($languages, 'title_en', Core::SORT_DSC, false);
            foreach ($languages as $c_language) {
                if ($c_language->code !== 'en') {
                    Tab_item::insert(                                       $c_language->title_en,
                        'nosql_translations_'                              .$c_language->code,
                        'nosql_translations', 'data_nosql', 'translations/'.$c_language->code, null, [], [], false, 0, 'develop'
                    );
                }
            }
        }
    }

    static function block_markup__nosql_events($page, $args = []) {
        $targets = new Markup('x-targets');
        $report = new Node;
        $events = Event::get_all();
        ksort($events);
        foreach ($events as $c_event_type => $c_events) {
            $targets->child_insert(new Markup('a', ['href' => '#type_'.$c_event_type, 'title' => new Text('go to section "%%_title"', ['title' => $c_event_type])], $c_event_type));
            $c_decorator = new Decorator('table-adaptive');
            $c_decorator->id = 'nosql_events_handlers__'.$c_event_type;
            $c_decorator->result_attributes = ['data-style' => 'compact'];
            $report->child_insert(new Markup('h2', ['id' => 'type_'.$c_event_type, 'title' => new Text('Section "%%_title"', ['title' => $c_event_type])], $c_event_type), $c_event_type.'_header'   );
            $report->child_insert($c_decorator,                                                                                                                            $c_event_type.'_decorator');
            foreach ($c_events as $c_event) {
                $c_decorator->data[] = [
                    'module_id' => ['value' => new Text_simple($c_event->module_id), 'title' => 'Module ID'],
                    'for_id'    => ['value' => new Text_simple($c_event->for      ), 'title' => 'For ID'   ],
                    'handler'   => ['value' => new Text_simple($c_event->handler  ), 'title' => 'Handler'  ],
                    'weight'    => ['value' => new Text_simple($c_event->weight   ), 'title' => 'Weight'   ]
                ];
            }
        }
        return new Node([], [
            $targets,
            $report
        ]);
    }

    static function block_markup__nosql_tree($page, $args = []) {
        $id = $page->args_get('id');
        $trees = Tree::select_all('nosql');
        if ($id && isset($trees[$id])) {
            $tree = Tree::select($id);
            $tree_managing_id = 'managed-'.$id;
            $tree_managing = Tree::insert($tree->title ?? null, $tree_managing_id, null, [], 0, 'develop');
            $tree_managing->visualization_mode = 'decorated';
            foreach (Tree_item::select_all_by_id_tree($id) as $c_item) {
                $c_tree_item = Tree_item::insert($c_item->title,
                    $tree_managing_id.'-'.$c_item->id, $c_item->id_parent !== null ?
                    $tree_managing_id.'-'.$c_item->id_parent : null,
                    $tree_managing_id,
                    $c_item->url, null,
                    $c_item->attributes,
                    $c_item->element_attributes,
                    $c_item->weight, 'develop'
                );
            }
            return $tree_managing;
        }
    }

    static function block_markup__nosql_selections($page, $args = []) {
        $selection = Selection::get_all('nosql');
        $decorator = new Decorator('table-adaptive');
        $decorator->id = 'nosql_selections';
        foreach ($selection as $c_selection) {
            $decorator->data[] = [
                'id'    => ['value' =>                                                             new Text_simple($c_selection->id   ), 'title' => 'ID'   ],
                'title' => ['value' => $c_selection->title instanceof Text ? $c_selection->title : new Text       ($c_selection->title), 'title' => 'Title']
            ];
        }
        return $decorator;
    }

    static function block_markup__nosql_file_types($page, $args = []) {
        $decorator = new Decorator('table-adaptive');
        $decorator->id = 'nosql_file_types';
        $file_types = File::types_get();
        ksort($file_types);
        foreach ($file_types as $c_type) {
            $decorator->data[] = [
                'type'      => ['value' => new Text_simple(      $c_type->type                                           ), 'title' => 'Type'     ],
                'kind'      => ['value' => new Text_simple(      $c_type->kind ?? null                                   ), 'title' => 'Kind'     ],
                'module_id' => ['value' => new Text_simple(      $c_type->module_id                                      ), 'title' => 'Module ID'],
                'headers'   => ['value' => new Text_simple(isset($c_type->headers) ? implode(BR, $c_type->headers) : null), 'title' => 'Headers'  ]
            ];
        }
        return $decorator;
    }

    static function block_markup__nosql_templates($page, $args = []) {
        $decorator = new Decorator('table-adaptive');
        $decorator->id = 'nosql_templates';
        $templates = Template::get_all();
        ksort($templates);
        foreach ($templates as $c_template) {
            $decorator->data[] = [
                'name'      => ['value' => new Text_simple($c_template->name     ), 'title' => 'Name'     ],
                'type'      => ['value' => new Text_simple($c_template->type     ), 'title' => 'Type'     ],
                'module_id' => ['value' => new Text_simple($c_template->module_id), 'title' => 'Module ID'],
            ];
        }
        return $decorator;
    }

    static function block_markup__nosql_tokens($page, $args = []) {
        $decorator = new Decorator('table-adaptive');
        $decorator->id = 'nosql_tokens';
        $tokens = Token::select_all();
        ksort($tokens);
        foreach ($tokens as $c_row_id => $c_token) {
            $decorator->data[] = [
                'row_id'    => ['value' => new Text_simple($c_row_id          ), 'title' => 'Row ID'   ],
                'type'      => ['value' => new Text_simple($c_token->type     ), 'title' => 'Type'     ],
                'module_id' => ['value' => new Text_simple($c_token->module_id), 'title' => 'Module ID']
            ];
        }
        return $decorator;
    }

    static function block_markup__nosql_translations($page, $args = []) {
        $id = Page::get_current()->args_get('id');
        $decorator = new Decorator('table-adaptive');
        $decorator->id = 'nosql_translations';
        $decorator->result_attributes = ['data-style' => 'compact'];
        $translations = Translation::select_all_by_code($id);
        if ($translations) {
            ksort($translations);
            foreach ($translations as $c_english => $c_translated) {
                $decorator->data[] = [
                    'english'     => ['value' => new Text_simple($c_english   ), 'title' => 'English'    ],
                    'translation' => ['value' => new Text_simple($c_translated), 'title' => 'Translation']
                ];
            }
        }
        return $decorator;
    }

}
