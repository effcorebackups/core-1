<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class console {

  const directory = dir_dynamic.'logs/';
  const is_visible_for_nobody   = 0b00;
  const is_visible_for_admin    = 0b01;
  const is_visible_for_everyone = 0b10;

  static $is_ignore_duplicates = false;
  static $duplicates = [];
  static protected $data = [];
  static protected $file_log_err = null;
  static protected $file_log_wrn = null;
  static protected $is_write_to_file_log_wrn = false;
  static protected $is_init = false;
  static protected $visible_mode = self::is_visible_for_nobody;

  static function init($reset = false) {
    if (!static::$is_init || $reset) {
         static::$is_init = true;
      static::$data[] = (object)['object' => 'file', 'action' => 'insertion', 'description' => 'system/boot.php',                           'value' => 'ok', 'time' => 0, 'ram_dynamics' => memory_get_usage(true), 'args' => [], 'info' => []];
      static::$data[] = (object)['object' => 'file', 'action' => 'insertion', 'description' => 'system/module_core/backend/core.php',       'value' => 'ok', 'time' => 0, 'ram_dynamics' => memory_get_usage(true), 'args' => [], 'info' => []];
      static::$data[] = (object)['object' => 'file', 'action' => 'insertion', 'description' => 'system/module_storage/backend/markers.php', 'value' => 'ok', 'time' => 0, 'ram_dynamics' => memory_get_usage(true), 'args' => [], 'info' => []];
      static::$file_log_err = new file(static::directory.core::date_get().'/'.  'error--'.core::date_get().'.log');
      static::$file_log_wrn = new file(static::directory.core::date_get().'/'.'warning--'.core::date_get().'.log');
      static::$visible_mode = static::is_visible_for_nobody;
      if (module::is_enabled('develop')) {
        $settings = module::settings_get('page');
        if ($settings->console_visibility === static::is_visible_for_everyone                                                           ) static::$visible_mode = static::is_visible_for_everyone;
        if ($settings->console_visibility === static::is_visible_for_admin && access::check((object)['roles' => ['admins' => 'admins']])) static::$visible_mode = static::is_visible_for_admin;
      }
    }
  }

  static function visible_mode_get() {
    static::init();
    return static::$visible_mode;
  }

  static function logs_select() {
    static::init();
    return static::$data;
  }

  static function &log_insert($object, $action, $description = null, $value = '', $time = 0, $args = [], $info = []) {
    static::init();
    $new_log = new \stdClass;
    $new_log->object       = $object;
    $new_log->action       = $action;
    $new_log->description  = $description;
    $new_log->value        = $value;
    $new_log->time         = $time;
    $new_log->args         = $args;
    $new_log->info         = $info;
    $new_log->ram_dynamics = memory_get_usage(true);
    if (static::visible_mode_get()) {
      $stack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
      if ($stack[0]['function'] === 'log_insert'            ) array_shift($stack);
      if ($stack[0]['function'] === 'report_about_duplicate') array_shift($stack);
      $new_log->info['stack'] = static::render_stack($stack);
    }
    static::$data[] = $new_log;

  # store errors and warnings to the file
    if ($value === 'error' || ($value === 'warning' && static::$is_write_to_file_log_wrn === true)) {
      $c_info = $new_log->description;
      $c_file = $value === 'error' ? static::$file_log_err : static::$file_log_wrn;
      foreach ($new_log->args as $c_key => $c_value) $c_info = str_replace('%%_'.$c_key, $c_value, $c_info);
      $c_line = core::time_get().' | uid: '.(user::get_current()->id ?: 0).
                                 ' | '.$new_log->object.
                                 ' | '.$new_log->action.
                                 ' | '.str_replace(br, ' | ', $c_info).nl;
      if (!$c_file->append_direct($c_line)) {
        message::insert(new text_multiline([
          'File "%%_file" was not written to disc!',
          'File permissions (if the file exists) and directory permissions should be checked.'], [
          'file' => $c_file->path_get_relative()]), 'error'
        );
      }
    }
    return $new_log;
  }

  static function report_about_duplicate($type, $id, $module_id, $firstinit = null) {
    if ($firstinit !== null && !empty($firstinit->module_id))
      static::$duplicates[$type][$id][$firstinit->module_id] = $firstinit->module_id;
      static::$duplicates[$type][$id][           $module_id] =            $module_id;
  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
    if (static::$is_ignore_duplicates === false) {
      message::insert(new text(                    'Duplicate of type "%%_type" with ID = "%%_id" was found in module with ID = "%%_module_id"!',            ['type' => $type, 'id' => $id, 'module_id' => $module_id]), 'error');
      return static::log_insert('storage', 'load', 'duplicate of type "%%_type" with ID = "%%_id" was found in module with ID = "%%_module_id"', 'error', 0, ['type' => $type, 'id' => $id, 'module_id' => $module_id]);
    }
  }

  # ─────────────────────────────────────────────────────────────────────
  # console output as markup
  # ─────────────────────────────────────────────────────────────────────

  static function markup_get() {
    return new markup('x-console', [], [
      static::block_markup__information (),
      static::block_markup__diagram_load(),
      static::block_markup__logs        ()
    ]);
  }

  static function block_markup__information() {
    $user = user::get_current();
    $user_roles = $user->roles;
    $user_permissions = role::related_permissions_by_roles_select($user_roles);
    $decorator = new decorator('table-dl');
    $decorator->id = 'page_information';
    $decorator->data = [[
      'gen_time'    => ['title' => 'Total generation time', 'value' => locale::format_msecond(timer::period_get('total', 0, 1))  ],
      'memory'      => ['title' => 'Memory for PHP',        'value' => locale::format_bytes(memory_get_usage(true))              ],
      'language'    => ['title' => 'Current language',      'value' => language::code_get_current()                              ],
      'roles'       => ['title' => 'User roles',            'value' => $user_roles       ? implode(', ', $user_roles      ) : '—'],
      'permissions' => ['title' => 'User permissions',      'value' => $user_permissions ? implode(', ', $user_permissions) : '—'] ]];
    return new block('Current page information', ['data-id' => 'block__info', 'data-style' => 'title-is-simple'], [$decorator]);
  }

  static function block_markup__diagram_load() {
    $statistics = [];
    $total = 0;
    foreach (static::$data as $c_log) {
      if (floatval($c_log->time)) {
        if (!isset($statistics[$c_log->object]))
                   $statistics[$c_log->object] = 0;
        $statistics[$c_log->object] += floatval($c_log->time);
        $total += floatval($c_log->time); }}
    $diagram = new diagram(null, 'linear');
    foreach ($statistics as  $c_key => $c_value)
      $diagram->slice_insert($c_key,   $c_value / $total * 100, (new text('%%_number sec.', ['number' => locale::format_msecond($c_value)]))->render(), 'black', ['data-id' => $c_key]);
    return new block('CPU load time', ['data-id' => 'block__diagram_load'], [$diagram]);
  }

  static function block_markup__logs() {
    $total_sequence_hash = '';
    $total_data_hash     = '';
    $total_by_actions    = [];
    $logs = static::logs_select();
    $decorator = new decorator('table');
    $decorator->id = 'logs';
    $decorator->result_attributes = ['data-style' => 'compact'];
    foreach (static::logs_select() as $c_row_id => $c_log) {
      $c_sequence_hash      = core::hash_get(['time' => 0, 'args' => []] + (array)$c_log);
      $c_data_hash          = core::hash_get(['time' => 0              ] + (array)$c_log);
      $total_sequence_hash  = core::hash_get($total_sequence_hash.$c_sequence_hash);
      $total_data_hash      = core::hash_get($total_data_hash    .$c_data_hash    );
      if (isset($total_by_actions[$c_log->object][$c_log->action]))
                $total_by_actions[$c_log->object][$c_log->action]++;
      else      $total_by_actions[$c_log->object][$c_log->action] = 1;
      $c_row_attributes  = ['data-hash-sequence' => core::hash_get_mini($c_sequence_hash)];
      $c_row_attributes += ['data-hash-data'     => core::hash_get_mini($c_data_hash    )];
      $c_row_attributes += ['data-object'        => core::sanitize_id(trim($c_log->object, '.'))];
      $c_row_attributes += ['data-action'        => core::sanitize_id(trim($c_log->action, '.'))];
      $c_row_attributes += ['data-value'         => core::sanitize_id(trim($c_log->value,  '.'))];
      $c_info = !empty($c_log->info) ? static::render_info_markup($c_log->info) : '';
      if ($c_log->time  >= .000099) $c_row_attributes['data-loading-level'] = 1;
      if ($c_log->time  >=  .00099) $c_row_attributes['data-loading-level'] = 2;
      if ($c_log->time  >=   .0099) $c_row_attributes['data-loading-level'] = 3;
      if ($c_log->time  >=    .099) $c_row_attributes['data-loading-level'] = 4;
      if ($c_log->time  >=     .99) $c_row_attributes['data-loading-level'] = 5;
      $decorator->data[] = [
        'attributes'   => $c_row_attributes,
        'time'         => ['title' => 'Time',              'value' => locale::format_msecond($c_log->time)                                   ],
        'ram_dynamics' => ['title' => 'RAM load dynamics', 'value' => locale::format_bytes  ($c_log->ram_dynamics)                           ],
        'object'       => ['title' => 'Object',            'value' =>    new text           ($c_log->object)                                 ],
        'action'       => ['title' => 'Action',            'value' =>    new text           ($c_log->action)                                 ],
        'description'  => ['title' => 'Description',       'value' =>    new text_multiline([$c_log->description, $c_info], $c_log->args, '')],
        'value'        => ['title' => 'Val.',              'value' =>    new text           ($c_log->value)                                  ]
      ];
    }
    $markup_total = new markup('x-total', [], [
      new markup('x-param', ['data-id' => 'shash'], [new markup('x-title', [], 'Sequence hash'), new markup('x-value', [], $total_sequence_hash)]),
      new markup('x-param', ['data-id' => 'dhash'], [new markup('x-title', [], 'Data hash'    ), new markup('x-value', [], $total_data_hash    )]),
      new markup('x-param', ['data-id' => 'count'], [new markup('x-title', [], 'Total'        ), new markup('x-value', [], count($logs)        )]),
    ]);
    foreach ($total_by_actions as $c_object_name => $c_object_total) {
      foreach ($c_object_total as $c_action_name => $c_total) {
        $markup_total->child_insert(
          new markup('x-param', ['data-id' => $c_object_name.'-'.$c_action_name], [
            new markup('x-title', [], new text_multiline(['— ', $c_object_name, ' | ', $c_action_name], [], '')),
            new markup('x-value', [], $c_total)
          ])
        );
      }
    }
    return new block('Execution plan', ['data-id' => 'block__logs', 'data-style' => 'title-is-simple'], [
      $decorator, $markup_total
    ]);
  }

  static function render_info_opener() {
    return (new markup_simple('input', [
      'type'             => 'checkbox',
      'role'             => 'button',
      'data-opener-type' => 'info',
      'title'            => new text('press to show more information')
    ]))->render();
  }

  static function render_info_markup($data) {
    $info = new markup('x-info');
    foreach ($data as $c_title => $c_value)
      $info->child_insert(
        new markup('x-param', [], [
        new markup('x-title', [], $c_title),
        new markup('x-value', [], $c_value)]), $c_title);
    return static::render_info_opener().$info->render();
  }

  static function render_stack($data) {
    $result = [];
    foreach ($data as $c_info)
      $result[] = ($c_info['class'   ] ?? '').
                  ($c_info['type'    ] ?? '').
                   $c_info['function'];
    return implode(' → ', array_reverse($result));
  }

  # ─────────────────────────────────────────────────────────────────────
  # console output as text (for *.jsd | *.cssd)
  # ─────────────────────────────────────────────────────────────────────

  static function text_get() {
    return static::block_text__information ().
           static::block_text__diagram_load().
           static::block_text__logs        ();
  }

  static function block_text__information() {
    $information = [];
    $information['Total generation time'] = core::format_msecond(timer::period_get('total', 0, 1));
    $information['Memory for PHP'] = core::format_bytes(memory_get_usage(true));
    $result = '  CURRENT PAGE INFORMATION'.nl.nl;
    foreach ($information as $c_key => $c_value) {
      $result.= '  '.str_pad($c_key, 38, ' ', STR_PAD_LEFT).' : ';
      $result.=      $c_value.nl; }
    return nl.$result.nl;
  }

  static function block_text__diagram_load() {
    $statistics = [];
    $total = 0;
    foreach (static::$data as $c_log) {
      if (floatval($c_log->time)) {
        if (!isset($statistics[$c_log->object]))
                   $statistics[$c_log->object] = 0;
        $statistics[$c_log->object] += floatval($c_log->time);
        $total += floatval($c_log->time); }}
    $result = '  CPU LOAD TIME'.nl.nl;
    foreach ($statistics as $c_key => $c_value) {
      $c_percent = $c_value / $total * 100;
      $result.= '  '.str_pad($c_key, 15, ' ', STR_PAD_LEFT)                           .  ' | ';
      $result.=      str_pad(str_repeat('#', (int)($c_percent / 10)), 10, '-')        .  ' | ';
      $result.=      str_pad(core::format_number($c_percent, 2), 5, ' ', STR_PAD_LEFT).' % | ';
      $result.=      core::format_msecond($c_value).' sec.'.nl; }
    return nl.$result.nl;
  }

  static function block_text__logs() {
    $total_sequence_hash = '';
    $total_data_hash     = '';
    $total_by_actions    = [];
    $logs = static::logs_select();
    $result = '  EXECUTION PLAN'.nl.nl;
    $result.= '  ------------------------------------------------------------'.nl;
    $result.= '  Time     | Object     | Action     | Value | Description    '.nl;
    $result.= '  ------------------------------------------------------------'.nl;
    foreach (static::logs_select() as $c_log) {
      $c_sequence_hash      = core::hash_get(['time' => 0, 'args' => []] + (array)$c_log);
      $c_data_hash          = core::hash_get(['time' => 0              ] + (array)$c_log);
      $total_sequence_hash  = core::hash_get($total_sequence_hash.$c_sequence_hash);
      $total_data_hash      = core::hash_get($total_data_hash    .$c_data_hash    );
      if (isset($total_by_actions[$c_log->object][$c_log->action]))
                $total_by_actions[$c_log->object][$c_log->action]++;
      else      $total_by_actions[$c_log->object][$c_log->action] = 1;
      $result.= '  '.str_pad(core::format_msecond($c_log->time), 8).' | ';
      $result.=      str_pad($c_log->object, 10)                   .' | ';
      $result.=      str_pad($c_log->action, 10)                   .' | ';
      $result.=      str_pad($c_log->value,   5)                   .' | ';
      if (!empty($c_log->info))
           $result.= (new text($c_log->description.'   …   '.static::render_info_text($c_log->info), $c_log->args, false))->render().nl;
      else $result.= (new text($c_log->description,                                                  $c_log->args, false))->render().nl;
    }
    $result.= '  ------------------------------------------------------------'.nl;
    $result.= nl.'  '.str_pad('Sequence hash: ', 25).$total_sequence_hash;
    $result.= nl.'  '.str_pad('Data hash: ',     25).$total_data_hash;
    $result.= nl.'  '.str_pad('Total: ',         25).count($logs);
    foreach ($total_by_actions as $c_object_name => $c_objstatistic) {
      foreach ($c_objstatistic as $c_action_name => $c_total) {
        $result.= nl.'  '.str_pad('- '.$c_object_name.' | '.$c_action_name.': ', 25).$c_total;
      }
    }
    return nl.$result.nl;
  }

  static function render_info_text($data) {
    $result = new node;
    foreach ($data as $c_title => $c_value)
      $result->child_insert(new text($c_title.': '.$c_value.'; '), $c_title);
    return $result->render();
  }

}}