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

  static function log_insert_about_duplicate($type, $id, $module_id = null) {
    return $module_id ? static::log_insert('storage', 'load', 'duplicate of %%_type with id = "%%_id" was found in module "%%_module_id"', 'error', 0, ['type' => $type, 'id' => $id, 'module_id' => $module_id]) :
                        static::log_insert('storage', 'load', 'duplicate of %%_type with id = "%%_id" was found',                          'error', 0, ['type' => $type, 'id' => $id]);
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
          message::insert(new text_multiline([
            'Can not insert or update file "%%_file" in the directory "%%_directory"!',
            'Check file (if exists) and directory permissions.'], [
            'file'      => $file->file_get(),
            'directory' => $file->dirs_relative_get()]), 'error'
          );
        }
      }
    }
  }

  # ─────────────────────────────────────────────────────────────────────
  # console output as markup
  # ─────────────────────────────────────────────────────────────────────

  static function markup_get() {
    return new markup('x-console', [], [
      static::markup_block_information_get(),
      static::markup_block_diagram_load_get(),
      static::markup_block_logs_get()
    ]);
  }

  static function markup_block_information_get() {
    $user = user::current_get();
    $decorator = new decorator('dl');
    $decorator->data = [[
      'gen_time' => ['title' => 'Total generation time',  'value' => locale::format_msecond(timer::period_get('total', 0, 1))],
      'memory'   => ['title' => 'Memory for php (bytes)', 'value' => locale::format_number(memory_get_usage(true))           ],
      'language' => ['title' => 'Current language',       'value' => language::current_code_get()                            ],
      'roles'    => ['title' => 'User roles',             'value' => implode(', ', $user->roles)                             ]
    ]];
    return new block('Current page information', ['class' => ['info' => 'info']], [
      $decorator->build()
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
      $diagram->slice_add($c_param, $c_value / $total * 100, locale::format_msecond($c_value).' sec.', array_shift($colors));
    }
    return new block('Total load', ['class' => ['diagram-load' => 'diagram-load']], [
      $diagram
    ]);
  }

  static function markup_block_logs_get() {
    $logs = static::logs_select();
    $decorator = new decorator('table');
    $decorator->result_attributes = ['class' => ['compact' => 'compact']];
    foreach (static::logs_select() as $c_row_id => $c_log) {
      $c_row_attributes = ['class' => [
        core::sanitize_id($c_log->object) =>
        core::sanitize_id($c_log->object)
      ]];
      if ($c_log->value == 'error') {
        $c_row_attributes['aria-invalid'] = 'true';
      }
      $decorator->data[] = [
        'attributes'  => $c_row_attributes,
        'time'        => ['title' => 'Time',        'value' => locale::format_msecond($c_log->time)       ],
        'object'      => ['title' => 'Object',      'value' => new text($c_log->object,      $c_log->args)],
        'action'      => ['title' => 'Action',      'value' => new text($c_log->action,      $c_log->args)],
        'description' => ['title' => 'Description', 'value' => new text($c_log->description, $c_log->args)],
        'value'       => ['title' => 'Val.',        'value' => new text($c_log->value                    )]
      ];
    }
    return new block('Execute plan', ['data-styled-title' => 'no', 'class' => ['logs' => 'logs']], [
      $decorator, new markup('x-total', [], [
        new markup('x-label', [], 'Total'),
        new markup('x-value', [], count($logs))
      ])
    ]);
  }

  # ─────────────────────────────────────────────────────────────────────
  # console output as text (for *.jsd | *.cssd)
  # ─────────────────────────────────────────────────────────────────────

  static function text_get() {
    return static::text_block_information_get().
           static::text_block_diagram_load_get().
           static::text_block_logs_get();
  }

  static function text_block_information_get() {
    $information = [];
    $information['Total generation time'] = locale::format_msecond(timer::period_get('total', 0, 1));
    $information['Memory for php (bytes)'] = locale::format_number(memory_get_usage(true));
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
      $result.=      str_pad(core::format_number($c_percent, 2), 5, ' ', STR_PAD_LEFT).' % | ';
      $result.=      locale::format_msecond($c_value).' sec.'.nl;
    }
    return nl.$result.nl;
  }

  static function text_block_logs_get() {
    $logs = static::logs_select();
    $result = '  EXECUTE PLAN'.nl.nl;
    $result.= '  ------------------------------------------------------------'.nl;
    $result.= '  Time     | Object     | Action     | Value | Description    '.nl;
    $result.= '  ------------------------------------------------------------'.nl;
    foreach (static::logs_select() as $c_log) {
      $result.= '  '.str_pad(locale::format_msecond($c_log->time), 8).' | ';
      $result.=      str_pad($c_log->object, 10).                     ' | ';
      $result.=      str_pad($c_log->action, 10).                     ' | ';
      $result.=      str_pad($c_log->value,   5).                     ' | ';
      $result.=    (new text($c_log->description, $c_log->args, false))->render().nl;
    }
    $result.= '  ------------------------------------------------------------'.nl;
    $result.= nl.str_repeat(' ', 26).'Total: '.count($logs);
    return nl.$result.nl;
  }

}}