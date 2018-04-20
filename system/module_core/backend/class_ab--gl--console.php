<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class console {

  static protected $data = [];
  static protected $information = [];

  static function add_log($object, $action, $description = '', $value = '', $time = 0, $args = []) {
    static::$data[] = [
      'object'      => $object,
      'action'      => $action,
      'description' => $description,
      'value'       => $value,
      'time'        => $time,
      'args'        => $args,
    ];
  }

  static function add_information($param, $value) {
    static::$information[$param] = $value;
  }

  static function select_all_logs()        {return static::$data;}
  static function select_all_information() {return static::$information;}

  static function render() {
    return (new markup('x-console', [], [
      new markup('h2', [], 'Current page information'), static::get_markup_information(),
      new markup('h2', [], 'Total load'),               static::get_markup_diagram_load(),
      new markup('h2', [], 'Execute plan'),             static::get_markup_logs()
    ]))->render();
  }

  static function get_markup_information() {
    $info = new markup('dl', ['class' => ['info' => 'info']]);
    foreach (static::select_all_information() as $c_param => $c_value) {
      $info->child_insert(new markup('dt', [], $c_param));
      $info->child_insert(new markup('dd', [], $c_value));
    }
    return $info;
  }

  static function get_markup_diagram_load() {
    $statistics = [];
    $total = 0;
    foreach (static::$data as $c_log) {
      if (floatval($c_log['time'])) {
        if (!isset($statistics[$c_log['object']]))
                   $statistics[$c_log['object']] = 0;
        $statistics[$c_log['object']] += floatval($c_log['time']);
        $total += floatval($c_log['time']);
      }
    }
    $diagram = new markup('dl', ['class' => ['diagram-load' => 'diagram-load']]);
    foreach ($statistics as $c_param => $c_value) {
      $diagram->child_insert(new markup('dt', [], $c_param));
      $diagram->child_insert(new markup('dd', [], [
        locale::format_msecond($c_value).' sec. ('.
        locale::format_persent($c_value / $total * 100, 1).')',
        new markup('x-scale', [
          'class' => ['scope' => factory::to_css_class($c_param)],
          'style' => ['width: '.(int)($c_value / $total * 100).'%']
        ])
      ]));
    }
    return $diagram;
  }

  static function get_markup_logs() {
    $thead = [['Time', 'Object', 'Action', 'Description', 'Val.']];
    $tbody = [];
    foreach (static::select_all_logs() as $c_log) {
      $c_row_class = factory::to_css_class($c_log['object']);
      $c_value_class = $c_log['value'] === 'error' ?
        ['value' => 'value', 'value-error' => 'value-error'] :
        ['value' => 'value'];
      $tbody[] = new table_body_row(['class' => [$c_row_class => $c_row_class]], [
        new table_body_row_cell(['class' => ['time'        => 'time']],        locale::format_msecond($c_log['time'])),
        new table_body_row_cell(['class' => ['object'      => 'object']],      translation::get($c_log['object'],      $c_log['args'])),
        new table_body_row_cell(['class' => ['action'      => 'action']],      translation::get($c_log['action'],      $c_log['args'])),
        new table_body_row_cell(['class' => ['description' => 'description']], translation::get($c_log['description'], $c_log['args'])),
        new table_body_row_cell(['class' => $c_value_class],                   translation::get($c_log['value']))
      ]);
    }
    return (
      new table(['class' => ['logs' => 'logs']], $tbody, $thead)
    );
  }

}}