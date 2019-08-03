<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use const \effcore\br;
          use \effcore\block;
          use \effcore\core;
          use \effcore\decorator;
          use \effcore\event;
          use \effcore\file;
          use \effcore\language;
          use \effcore\markup;
          use \effcore\node;
          use \effcore\page;
          use \effcore\tabs_item;
          use \effcore\template;
          use \effcore\text_simple;
          use \effcore\token;
          use \effcore\translation;
          use \effcore\tree_item;
          use \effcore\tree;
          use \effcore\url;
          abstract class events_page_nosql_data {

  static function on_tab_build_before($event, $tab) {
    $type = page::get_current()->args_get('type');
    $id   = page::get_current()->args_get('id'  );
    if ($type == null) url::go(page::get_current()->args_get('base').'/trees');
    if (strpos($type, 'trees') === 0) {
      $trees = tree::select_all('nosql');
      core::array_sort_by_text_property($trees);
      if (!isset($trees[$id])) url::go(page::get_current()->args_get('base').'/trees/'.reset($trees)->id);
      foreach ($trees as $c_tree) {
        tabs_item::insert($c_tree->title,
           'nosql_trees_'.$c_tree->id,
           'nosql_trees', 'nosql_data', 'trees/'.$c_tree->id
        );
      }
    }
  }

  static function on_show_block_tree($page) {
    $id = $page->args_get('id');
    $trees = tree::select_all('nosql');
    if ($id && isset($trees[$id])) {
      $tree = tree::select($id);
      $tree_managing_id = 'managed-'.$id;
      $tree_managing = tree::insert($tree->title ?? '', $tree_managing_id);
      $tree_managing->managing_mode = 'simple';
      $tree_managing->title_state = 'cutted';
      foreach (tree_item::select_all_by_id_tree($id) as $c_item) {
        $c_tree_item = tree_item::insert($c_item->title,
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

  static function on_show_block_events($page) {
    $targets = new markup('x-targets');
    $report = new node();
    $events = event::get_all();
    ksort($events);
    foreach ($events as $c_event_type => $c_events) {
      $targets->child_insert(new markup('a', ['href' => '#type_'.$c_event_type], $c_event_type));
      $c_decorator = new decorator('table');
      $c_decorator->id = 'events_registered_handlers_'.$c_event_type;
      $c_decorator->result_attributes = ['data-is-compact' => 'true'];
      $report->child_insert(new markup('h2', ['id' => 'type_'.$c_event_type], $c_event_type), $c_event_type.'_header'   );
      $report->child_insert($c_decorator,                                                     $c_event_type.'_decorator');
      foreach ($c_events as $c_event) {
        $c_decorator->data[] = [
          'module_id' => ['value' => new text_simple($c_event->module_id), 'title' => 'Module ID'],
          'for_id'    => ['value' => new text_simple($c_event->for      ), 'title' => 'For ID'   ],
          'handler'   => ['value' => new text_simple($c_event->handler  ), 'title' => 'Handler'  ],
          'weight'    => ['value' => new text_simple($c_event->weight   ), 'title' => 'Weight'   ]
        ];
      }
    }
    return new block('', ['data-id' => 'events_registered'], [
      $targets,
      $report
    ]);
  }

  static function on_show_block_file_types($page) {
    $decorator = new decorator('table');
    $decorator->id = 'file_types_registered';
    $file_types = file::types_get();
    ksort($file_types);
    foreach ($file_types as $c_type) {
      $decorator->data[] = [
        'type'      => ['value' => new text_simple(      $c_type->type                                         ), 'title' => 'Type'     ],
        'kind'      => ['value' => new text_simple(      $c_type->kind ?? ''                                   ), 'title' => 'Kind'     ],
        'module_id' => ['value' => new text_simple(      $c_type->module_id                                    ), 'title' => 'Module ID'],
        'headers'   => ['value' => new text_simple(isset($c_type->headers) ? implode(br, $c_type->headers) : ''), 'title' => 'Headers'  ]
      ];
    }
    return new block('', ['data-id' => 'file_types_registered'], [
      $decorator
    ]);
  }

  static function on_show_block_templates($page) {
    $decorator = new decorator('table');
    $decorator->id = 'templates_registered';
    $templates = template::get_all();
    ksort($templates);
    foreach ($templates as $c_template) {
      $decorator->data[] = [
        'name'      => ['value' => new text_simple($c_template->name     ), 'title' => 'Name'     ],
        'type'      => ['value' => new text_simple($c_template->type     ), 'title' => 'Type'     ],
        'module_id' => ['value' => new text_simple($c_template->module_id), 'title' => 'Module ID'],
      ];
    }
    return new block('', ['data-id' => 'templates_registered'], [
      $decorator
    ]);
  }

  static function on_show_block_tokens($page) {
    $decorator = new decorator('table');
    $decorator->id = 'tokens_registered';
    $tokens = token::get_all();
    ksort($tokens);
    foreach ($tokens as $c_row_id => $c_token) {
      $decorator->data[] = [
        'rowid'     => ['value' => new text_simple($c_row_id          ), 'title' => 'Row ID'   ],
        'match'     => ['value' => new text_simple($c_token->match    ), 'title' => 'Match'    ],
        'type'      => ['value' => new text_simple($c_token->type     ), 'title' => 'Type'     ],
        'module_id' => ['value' => new text_simple($c_token->module_id), 'title' => 'Module ID']
      ];
    }
    return new block('', ['data-id' => 'tokens_registered'], [
      $decorator
    ]);
  }

  static function on_show_block_translations($page) {
    $decorator = new decorator('table');
    $decorator->id = 'translations_registered';
    $decorator->view_type = 'ul';
    $decorator->result_attributes = ['data-is-compact' => 'true'];
    $translations = translation::get_all_by_code();
    if ($translations) {
      ksort($translations);
      foreach ($translations as $c_orig => $c_tran) {
        $decorator->data[] = [
          'orig' => ['value' => new text_simple($c_orig), 'title' => 'Original'   ],
          'tran' => ['value' => new text_simple($c_tran), 'title' => 'Translation']
        ];
      }
    }
    return new block('', ['data-id' => 'translations_registered'], [
      $decorator
    ]);
  }

}}
