<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class console {

  const directory = dir_dynamic.'logs/';
  static protected $data = [];

  static function logs_select() {
    return static::$data;
  }

  static function log_insert($object, $action, $description = '', $value = '', $time = 0, $args = []) {
    static::$data[] = (object)[
      'object'      => $object,
      'action'      => $action,
      'description' => $description,
      'value'       => $value,
      'time'        => $time,
      'args'        => $args,
    ];
  }

  static function log_about_duplicate_insert($type, $id, $module_id = null) {
    return $module_id ? static::log_insert('storage', 'load', 'duplicate of %%_type "%%_id" was found in module "%%_module_id"', 'error', 0, ['type' => $type, 'id' => $id, 'module_id' => $module_id]) :
                        static::log_insert('storage', 'load', 'duplicate of %%_type "%%_id" was found',                          'error', 0, ['type' => $type, 'id' => $id]);
  }

  static function log_store($log_level = 'error') {
    $file = new file(static::directory.core::date_get().'/'.
                       $log_level.'--'.core::date_get().'.log');
    foreach (static::$data as $c_log) {
      if ($c_log->value == $log_level) {
        $c_info = $c_log->description;
        foreach ($c_log->args as $c_key => $c_value) {
          $c_info = str_replace('%%_'.$c_key, $c_value, $c_info);
        }
        if (!$file->append_direct(core::time_get().' | '.
                                    $c_log->object.' | '.
                                    $c_log->action.' | '.$c_info.nl)) {
          message::insert(
            translation::get('Can not insert or update file "%%_file" in the directory "%%_directory"!', ['file' => $file->file_get(), 'directory' => $file->dirs_relative_get()]).br.
            translation::get('Check file (if exists) and directory permissions.'), 'error'
          );
        }
      }
    }
  }

  static function markup_get() {
    return new markup('x-console', [], [
      static::markup_block_information_get(),
      static::markup_block_diagram_load_get(),
      static::markup_block_logs_get()
    ]);
  }

  static function markup_block_information_get() {
    $user = user::current_get();
    $information = [];
    $information['Total generation time'] = locale::msecond_format(timer::period_get('total', 0, 1));
    $information['Memory for php (bytes)'] = locale::number_format(memory_get_usage(true));
    $information['Current language'] = language::current_code_get();
    $information['User roles'] = implode(', ', $user->roles);
    $info = new markup('dl');
    foreach ($information as $c_param => $c_value) {
      $info->child_insert(new markup('dt', [], $c_param));
      $info->child_insert(new markup('dd', [], $c_value));
    }
    return new block('Current page information', ['class' => ['info' => 'info']], [
      $info
    ]);
  }

  static function markup_block_diagram_load_get() {
    $statistics = [];
    $total = 0;
    foreach (static::$data as $c_log) {
      if (floatval($c_log->time)) {
        if (!isset($statistics[$c_log->object]))
                   $statistics[$c_log->object] = 0;
        $statistics[$c_log->object] += floatval($c_log->time);
        $total += floatval($c_log->time);
      }
    }
    $diagram = new diagram('', 'radial');
    $colors = ['#216ce4', '#30c432', '#fd9a1e', '#fc5740', 'darkcyan', 'lightseagreen', 'springgreen', 'yellowgreen', 'gold', 'crimson', 'lightcoral', 'thistle', 'moccasin', 'paleturquoise'];
    foreach ($statistics as $c_param => $c_value) {
      $diagram->slice_add($c_param, $c_value / $total * 100, locale::msecond_format($c_value).' sec.', array_shift($colors));
    }
    return new block('Total load', ['class' => ['diagram-load' => 'diagram-load']], [
      $diagram
    ]);
  }

  static function markup_block_logs_get() {
    $thead = [['Time', 'Object', 'Action', 'Description', 'Val.']];
    $tbody = [];
    $logs_all = static::logs_select();
    foreach (static::logs_select() as $c_log) {
      $c_row_class = core::to_css_class($c_log->object);
      $c_value_class = $c_log->value === 'error' ?
        ['value' => 'value', 'value-error' => 'value-error'] :
        ['value' => 'value'];
      $tbody[] = new table_body_row(['class' => [$c_row_class => $c_row_class]], [
        new table_body_row_cell(['class' => ['time'        => 'time'       ]], locale::msecond_format($c_log->time)),
        new table_body_row_cell(['class' => ['object'      => 'object'     ]], translation::get($c_log->object,      $c_log->args)),
        new table_body_row_cell(['class' => ['action'      => 'action'     ]], translation::get($c_log->action,      $c_log->args)),
        new table_body_row_cell(['class' => ['description' => 'description']], translation::get($c_log->description, $c_log->args)),
        new table_body_row_cell(['class' => $c_value_class                  ], translation::get($c_log->value                    ))
      ]);
    }
    return new block('Execute plan', ['data-styled-title' => 'no', 'class' => ['logs' => 'logs']], [
      new table(['class' => ['compact' => 'compact']], $tbody, $thead),
      new markup('x-total', [], [
      new markup('x-label', [], ['Total', ': ']),
      new markup('x-value', [], count($logs_all))])
    ]);
  }

  static function text_get() {
    return static::text_block_information_get().
           static::text_block_diagram_load_get().
           static::text_block_logs_get();
  }

  static function text_block_information_get() {
    $information = [];
    $information['Total generation time'] = locale::msecond_format(timer::period_get('total', 0, 1));
    $information['Memory for php (bytes)'] = locale::number_format(memory_get_usage(true));
    $result = '  CURRENT PAGE INFORMATION'.nl.nl;
    foreach ($information as $c_param => $c_value) {
      $result.= '  '.str_pad($c_param, 60, ' ', STR_PAD_LEFT).' : ';
      $result.=      $c_value.nl;
    }
    return nl.$result.nl;
  }

  static function text_block_diagram_load_get() {
    $statistics = [];
    $total = 0;
    foreach (static::$data as $c_log) {
      if (floatval($c_log->time)) {
        if (!isset($statistics[$c_log->object]))
                   $statistics[$c_log->object] = 0;
        $statistics[$c_log->object] += floatval($c_log->time);
        $total += floatval($c_log->time);
      }
    }
    $result = '  TOTAL LOAD'.nl.nl;
    foreach ($statistics as $c_param => $c_value) {
      $c_percent = $c_value / $total * 100;
      $result.= '  '.str_pad($c_param, 34, ' ', STR_PAD_LEFT).                           ' | ';
      $result.=      str_pad(str_repeat('#', (int)($c_percent / 10)), 10, '-').          ' | ';
      $result.=      str_pad(core::number_format($c_percent, 2), 5, ' ', STR_PAD_LEFT).' % | ';
      $result.=      locale::msecond_format($c_value).' sec.'.nl;
    }
    return nl.$result.nl;
  }

  static function text_block_logs_get() {
    $result = '  EXECUTE PLAN'.nl.nl;
    $result.= '  ------------------------------------------------------------'.nl;
    $result.= '  Time     | Object     | Action     | Value | Description    '.nl;
    $result.= '  ------------------------------------------------------------'.nl;
    $logs_all = static::logs_select();
    foreach (static::logs_select() as $c_log) {
      $result.= '  '.str_pad(locale::msecond_format($c_log->time), 8).' | ';
      $result.=      str_pad($c_log->object, 10).                     ' | ';
      $result.=      str_pad($c_log->action, 10).                     ' | ';
      $result.=      str_pad($c_log->value,   5).                     ' | ';
      $result.=    (new text($c_log->description, $c_log->args, false))->render().nl;
    }
    $result.= '  ------------------------------------------------------------'.nl;
    $result.= nl.str_repeat(' ', 26).'Total: '.count($logs_all);
    return nl.$result.nl;
  }

}}