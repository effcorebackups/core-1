<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class console {

  const directory = dir_dynamic.'logs/';
  static protected $data = [];
  static protected $information = [];

  static function logs_all_select() {
    return static::$data;
  }

  static function log_add($object, $action, $description = '', $value = '', $time = 0, $args = []) {
    static::$data[] = (object)[
      'object'      => $object,
      'action'      => $action,
      'description' => $description,
      'value'       => $value,
      'time'        => $time,
      'args'        => $args,
    ];
  }

  static function log_about_duplicate_add($type, $id) {
    return static::log_add('storage', 'load',
       'duplicate of %%_type "%%_id" was found', 'error', 0, ['type' => $type, 'id' => $id]
    );
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
        if (!$file->direct_append(core::time_get().' | '.
                                    $c_log->object.' | '.
                                    $c_log->action.' | '.$c_info.nl)) {
          message::insert(
            translation::get('Can not write file "%%_file" to the directory "%%_directory"!', ['file' => $file->get_file(), 'directory' => $file->get_dirs_relative()]).br.
            translation::get('Check file (if exists) and directory permissions.'), 'error'
          );
        }
      }
    }
  }

  static function information_all_select() {return static::$information;}
  static function information_add($param, $value) {
    static::$information[$param] = $value;
  }

  static function render() {
    return (new markup('x-console', [], [
      new markup('h2', [], 'Current page information'), static::markup_information_get(),
      new markup('h2', [], 'Total load'),               static::markup_diagram_load_get(),
      new markup('h2', [], 'Execute plan'),             static::markup_logs_get()
    ]))->render();
  }

  static function markup_information_get() {
    $info = new markup('dl', ['class' => ['info' => 'info']]);
    foreach (static::information_all_select() as $c_param => $c_value) {
      $info->child_insert(new markup('dt', [], $c_param));
      $info->child_insert(new markup('dd', [], $c_value));
    }
    return $info;
  }

  static function markup_diagram_load_get() {
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
    $diagram = new markup('dl', ['class' => ['diagram-load' => 'diagram-load']]);
    foreach ($statistics as $c_param => $c_value) {
      $diagram->child_insert(new markup('dt', [], $c_param));
      $diagram->child_insert(new markup('dd', [], [
        locale::format_msecond($c_value).' sec. ('.
        locale::format_persent($c_value / $total * 100, 1).')',
        new markup('x-scale', [
          'class' => ['scope' => core::to_css_class($c_param)],
          'style' => ['width: '.(int)($c_value / $total * 100).'%']
        ])
      ]));
    }
    return $diagram;
  }

  static function markup_logs_get() {
    $thead = [['Time', 'Object', 'Action', 'Description', 'Val.']];
    $tbody = [];
    foreach (static::logs_all_select() as $c_log) {
      $c_row_class = core::to_css_class($c_log->object);
      $c_value_class = $c_log->value === 'error' ?
        ['value' => 'value', 'value-error' => 'value-error'] :
        ['value' => 'value'];
      $tbody[] = new table_body_row(['class' => [$c_row_class => $c_row_class]], [
        new table_body_row_cell(['class' => ['time'        => 'time']],        locale::format_msecond($c_log->time)),
        new table_body_row_cell(['class' => ['object'      => 'object']],      translation::get($c_log->object,      $c_log->args)),
        new table_body_row_cell(['class' => ['action'      => 'action']],      translation::get($c_log->action,      $c_log->args)),
        new table_body_row_cell(['class' => ['description' => 'description']], translation::get($c_log->description, $c_log->args)),
        new table_body_row_cell(['class' => $c_value_class],                   translation::get($c_log->value))
      ]);
    }
    return (
      new table(['class' => ['logs' => 'logs']], $tbody, $thead)
    );
  }

}}