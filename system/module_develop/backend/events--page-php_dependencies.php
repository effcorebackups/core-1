<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use const \effcore\dir_root;
          use \effcore\block;
          use \effcore\core;
          use \effcore\file;
          use \effcore\markup;
          use \effcore\module;
          use \effcore\table_body_row_cell;
          use \effcore\table_body_row;
          use \effcore\table;
          use \effcore\text_multiline;
          use \effcore\text_simple;
          abstract class events_page_php_dependencies {

  static function on_show_block_php_dependencies_list($page) {
    $modules_path = module::all_get('path');
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
      $c_path_relative = $c_file->path_relative_get();
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
    $thead_mod = [['Module', 'PHP extension']];
    $tbody_mod = [];
    foreach ($statistic_by_mod as $c_module_id => $c_extensions) {
      if ($c_module_id) {
        ksort($c_extensions);
        $tbody_mod[] = new table_body_row([], [
          new table_body_row_cell(['class' => ['module'    => 'module'   ]], new text_simple($c_module_id)),
          new table_body_row_cell(['class' => ['extension' => 'extension']], new text_simple(implode(', ', array_keys($c_extensions))))
        ]);
      }
    }
  # ─────────────────────────────────────────────────────────────────────
  # prepare report by functions
  # ─────────────────────────────────────────────────────────────────────
    $thead_fnc = [['Function', 'Usage frequency']];
    $tbody_fnc = [];
    foreach ($statistic_by_fnc as $c_function => $c_positions) {
      $tbody_fnc[] = new table_body_row([], [
        new table_body_row_cell(['class' => ['function' => 'function']], new text_simple($c_function)),
        new table_body_row_cell(['class' => ['usage'    => 'usage'   ]], new text_simple(count($c_positions)))
      ]);
    }
  # ─────────────────────────────────────────────────────────────────────
  # prepare full report
  # ─────────────────────────────────────────────────────────────────────
    $thead_ext = [['PHP ext.', 'Module', 'Function', 'File', 'Pos.']];
    $tbody_ext = [];
    foreach ($statistic_by_ext as $c_extension => $c_functions) {
      foreach ($c_functions as $c_function => $c_positions) {
        foreach ($c_positions as $c_position_info) {
          $tbody_ext[] = new table_body_row([], [
            new table_body_row_cell(['class' => ['extension' => 'extension']], new text_simple($c_extension                   )),
            new table_body_row_cell(['class' => ['module'    => 'module'   ]], new text_simple($c_position_info->module ?: '-')),
            new table_body_row_cell(['class' => ['function'  => 'function' ]], new text_simple($c_function                    )),
            new table_body_row_cell(['class' => ['file'      => 'file'     ]], new text_simple($c_position_info->file         )),
            new table_body_row_cell(['class' => ['position'  => 'position' ]], new text_simple($c_position_info->position     ))
          ]);
        }
      }
    }
  # return result
    return new block('', ['class' => ['php-dependencies' => 'php-dependencies']], [
      new markup('p',  [], new text_multiline(['The report was generated in real time.', 'The system can search for the used functions only for enabled PHP modules!'])),
      new markup('h2', [], 'Dependency of modules by PHP extensions'), new table(['class' => ['report-mod' => 'report-mod', 'compact' => 'compact']], $tbody_mod, $thead_mod),
      new markup('h2', [], 'PHP functions usage'),                     new table(['class' => ['report-fnc' => 'report-fnc', 'compact' => 'compact']], $tbody_fnc, $thead_fnc),
      new markup('h2', [], 'Full report'),                             new table(['class' => ['report-ext' => 'report-ext', 'compact' => 'compact']], $tbody_ext, $thead_ext)
    ]);
  }

}}