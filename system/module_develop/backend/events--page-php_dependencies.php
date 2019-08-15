<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use const \effcore\dir_root;
          use \effcore\block;
          use \effcore\core;
          use \effcore\decorator;
          use \effcore\file;
          use \effcore\markup;
          use \effcore\module;
          use \effcore\text_multiline;
          use \effcore\text_simple;
          abstract class events_page_php_dependencies {

  static function on_show_block_php_dependencies_list($page) {
    $modules_path = module::get_all('path');
    arsort($modules_path);
    $statistic_by_mod = [];
    $statistic_by_fnc = [];
    $statistic_by_ext = [];
    $functions_by_ext = [];
    foreach (get_loaded_extensions() as $c_extension) {
      foreach (get_extension_funcs($c_extension) ?: [] as $c_function) {
        $functions_by_ext[$c_function] = $c_extension;
      }
    }
  # scan each php file on used functions
    foreach (file::select_recursive(dir_root, '%^.*\\.php$%') as $c_path => $c_file) {
      $c_matches = [];
      $c_path_relative = $c_file->path_get_relative();
      $c_module_id = key(core::in_array_inclusions_find($c_path_relative, $modules_path));
    # load file and search functions in it
      preg_match_all('%(?<![a-zA-Z0-9_])(?<name>[a-zA-Z0-9_]+)\\(%sS', $c_file->load(), $c_matches, PREG_OFFSET_CAPTURE);
      if ($c_matches) {
        foreach ($c_matches['name'] as $c_match) {
          if (isset($functions_by_ext[$c_match[0]])) {
            $c_extension = $functions_by_ext[$c_match[0]];
            $c_function = $c_match[0];
            $c_position = $c_match[1];
            $statistic_by_fnc[$c_function][] = $c_position; 
            $statistic_by_mod[$c_module_id][$c_extension][] = $c_position;
            $statistic_by_ext[$c_extension][$c_function][] = (object)[
              'file'     => $c_path_relative,
              'position' => $c_position,
              'module'   => $c_module_id
            ];
          }
        }
      }
    }
    ksort($statistic_by_mod);
    ksort($statistic_by_fnc);
    ksort($statistic_by_ext);
  # ─────────────────────────────────────────────────────────────────────
  # prepare report by modules
  # ─────────────────────────────────────────────────────────────────────
    $mod_title = new markup('h2', [], 'Dependency of modules by PHP extensions');
    $mod_decorator = new decorator('table');
    $mod_decorator->id = 'modules_dependency';
    foreach ($statistic_by_mod as $c_module_id => $c_extensions) {
      if ($c_module_id) {
        ksort($c_extensions);
        $mod_decorator->data[$c_module_id] = [
          'module'    => ['value' => new text_simple($c_module_id),                             'title' => 'Module'       ],
          'extension' => ['value' => new text_simple(implode(', ', array_keys($c_extensions))), 'title' => 'PHP extension']
        ];
      }
    }
  # ─────────────────────────────────────────────────────────────────────
  # prepare report by functions
  # ─────────────────────────────────────────────────────────────────────
    $fnc_title = new markup('h2', [], 'PHP functions usage');
    $fnc_decorator = new decorator('table');
    $fnc_decorator->id = 'functions_usage';
    $fnc_decorator->result_attributes = ['data-compact' => 'true'];
    foreach ($statistic_by_fnc as $c_function => $c_positions) {
      $fnc_decorator->data[$c_function] = [
        'function' => ['value' => new text_simple($c_function),         'title' => 'Function'       ],
        'usage'    => ['value' => new text_simple(count($c_positions)), 'title' => 'Usage frequency']
      ];
    }
  # ─────────────────────────────────────────────────────────────────────
  # prepare full report
  # ─────────────────────────────────────────────────────────────────────
    $ext_title = new markup('h2', [], 'Full report');
    $ext_decorator = new decorator('table');
    $ext_decorator->id = 'extensions_dependency';
    $ext_decorator->result_attributes = ['data-compact' => 'true'];
    foreach ($statistic_by_ext as $c_extension => $c_functions) {
      foreach ($c_functions as $c_function => $c_positions) {
        foreach ($c_positions as $c_position_info) {
          $ext_decorator->data[] = [
            'extension' => ['value' => new text_simple($c_extension                   ), 'title' => 'PHP ext.'],
            'module'    => ['value' => new text_simple($c_position_info->module ?: '-'), 'title' => 'Module'  ],
            'function'  => ['value' => new text_simple($c_function                    ), 'title' => 'Function'],
            'file'      => ['value' => new text_simple($c_position_info->file         ), 'title' => 'File'    ],
            'position'  => ['value' => new text_simple($c_position_info->position     ), 'title' => 'Pos.'    ]
          ];
        }
      }
    }
  # return result
    return new block('', ['data-id' => 'php_dependencies'], [
      new markup('p',  [], new text_multiline(['The report was generated in real time.', 'The system can search for the used functions only for enabled PHP modules!'])),
      $mod_title,
      $mod_decorator,
      $fnc_title,
      $fnc_decorator,
      $ext_title,
      $ext_decorator
    ]);
  }

}}