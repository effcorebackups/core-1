<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use const \effcore\dir_root;
          use \effcore\block;
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
  # collect information about native php functions
    $used_funcs = [];
    foreach (get_loaded_extensions() as $c_extension) {
      foreach (get_extension_funcs($c_extension) ?: [] as $c_function) {
        $used_funcs[$c_function] = $c_extension;
      }
    }
  # get modules path
    $modules_path = module::all_get(true);
    arsort($modules_path);
  # scan each php file on used functions
    $statistic_by_ext = [];
    $statistic_by_mod = [];
    $php_files = file::select_recursive(dir_root, '%^.*\\.php$%');
    foreach ($php_files as $c_file) {
      $c_matches = [];
      $c_file_path = $c_file->path_relative_get();
    # define module id
      $c_module_id = '';
      foreach ($modules_path as $c_id => $c_path) {
        if (strpos($c_file_path, $c_path) === 0) {
          $c_module_id = $c_id;
          break;
        }
      }
    # load file and search functions in it
      preg_match_all('%(?<![a-z0-9_])(?<name>[a-z0-9_]+)\\(%isS', $c_file->load(), $c_matches, PREG_OFFSET_CAPTURE);
      if ($c_matches) {
        foreach ($c_matches['name'] as $c_match) {
          if (isset($used_funcs[$c_match[0]])) {
            $c_extension = $used_funcs[$c_match[0]];
            $c_function = $c_match[0];
            $c_position = $c_match[1];
            $statistic_by_mod[$c_module_id][$c_extension][] = $c_position;
            $statistic_by_ext[$c_extension][$c_function][] = (object)[
              'file'     => $c_file_path,
              'position' => $c_position,
              'module'   => $c_module_id
            ];
          }
        }
      }
    }
    ksort($statistic_by_ext);
  # ─────────────────────────────────────────────────────────────────────
  # prepare report by modules
  # ─────────────────────────────────────────────────────────────────────
    $thead_mod = [['Module', 'PHP Extension']];
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
  # prepare report by php extensions
  # ─────────────────────────────────────────────────────────────────────
    $thead_ext = [['PHP Ext.', 'Module', 'Function', 'File', 'Pos.']];
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
      new markup('p', [], new text_multiline(['The report was generated in real time.', 'The system can search for the used functions only for enabled PHP modules!'])),
      new table(['class' => ['report-mod' => 'report-mod', 'compact' => 'compact']], $tbody_mod, $thead_mod),
      new table(['class' => ['report-ext' => 'report-ext', 'compact' => 'compact']], $tbody_ext, $thead_ext)
    ]);
  }

}}