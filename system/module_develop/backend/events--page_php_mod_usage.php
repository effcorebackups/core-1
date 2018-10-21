<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use const \effcore\dir_root;
          use \effcore\block;
          use \effcore\file;
          use \effcore\markup;
          use \effcore\table_body_row_cell;
          use \effcore\table_body_row;
          use \effcore\table;
          use \effcore\text_multiline;
          use \effcore\text_simple;
          abstract class events_page_php_mod_usage {

  static function on_show_block_php_mod_usage_list($page) {
  # collect information about native php functions
    $funcs_by_mod = [];
    foreach (get_loaded_extensions() as $c_mod) {
      foreach (get_extension_funcs($c_mod) ?: [] as $c_func) {
        $funcs_by_mod[$c_func] = $c_mod;
      }
    }
  # scan each php file on used functions
    $statistic = [];
    $php_files = file::select_recursive(dir_root, '%^.*\\.php$%');
    foreach ($php_files as $c_file) {
      $c_matches = [];
      preg_match_all('%(?<![a-z0-9_])(?<name>[a-z0-9_]+)\\(%isS', $c_file->load(), $c_matches, PREG_OFFSET_CAPTURE);
      if ($c_matches) {
        foreach ($c_matches['name'] as $c_func) {
          if ( isset($funcs_by_mod[$c_func[0]]) ) {
            $c_mod = $funcs_by_mod[$c_func[0]];
            $c_fnc = $c_func[0];
            $c_pos = $c_func[1];
            $statistic[$c_mod][$c_fnc][] = (object)[
              'file'     => $c_file->path_relative_get(),
              'position' => $c_pos
            ];
          }
        }
      }
    }
    ksort($statistic);
  # prepare report
    $thead = [['Module', 'Function', 'File', 'Pos.']];
    $tbody = [];
    foreach ($statistic as $c_mod => $c_mod_info) {
      foreach ($c_mod_info as $c_fnc => $c_pos) {
        foreach ($c_pos as $c_pos_info) {
          $tbody[] = new table_body_row([], [
            new table_body_row_cell(['class' => ['module'   => 'module']],   new text_simple($c_mod)),
            new table_body_row_cell(['class' => ['function' => 'function']], new text_simple($c_fnc)),
            new table_body_row_cell(['class' => ['file'     => 'file']],     $c_pos_info->file),
            new table_body_row_cell(['class' => ['position' => 'position']], $c_pos_info->position)
          ]);
        }
      }
    }
    return new block('', ['class' => ['php-mod-usage-list' => 'php-mod-usage-list']], [
      new markup('p', [], new text_multiline(['The report was generated in real time.', 'The system can search for the used functions only for enabled PHP modules!'])),
      new table(['class' => ['compact' => 'compact']], $tbody, $thead)
    ]);
  }

}}