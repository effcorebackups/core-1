<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use const \effcore\br;
          use \effcore\block;
          use \effcore\control_actions_list;
          use \effcore\event;
          use \effcore\locale;
          use \effcore\module;
          use \effcore\table_body_row_cell;
          use \effcore\table;
          use \effcore\translation;
          abstract class events_page_modules {

  static function on_show_block_modules($page) {
    $thead = [['Module information', 'State', '']];
    $tbody = [];
    foreach (module::all_get() as $c_module) {
      if ($c_module->state != 'always_on') {
        $c_action_list = new control_actions_list();
        $c_action_list->action_add('enable/'.   $c_module->id, 'enable');
        $c_action_list->action_add('disable/'.  $c_module->id, 'disable');
        $c_action_list->action_add('uninstall/'.$c_module->id, 'uninstall');
      }
      $tbody[] = [
        new table_body_row_cell(['class' => ['info' => 'info']],
          translation::get('ID')         .': '.$c_module->id.br.
          translation::get('Version')    .': '.locale::format_version($c_module->version).br.
          translation::get('Title')      .': '.translation::get($c_module->title).br.
          translation::get('Description').': '.translation::get($c_module->description).br.
          translation::get('Path')       .': '.$c_module->path),
        new table_body_row_cell(['class' => ['state' => 'state']], $c_module->state),
        new table_body_row_cell(['class' => ['actions' => 'actions']], $c_action_list ?? null)
      ];
    }
    return new block('', ['class' => ['modules' => 'modules']], [
      new table([], $tbody, $thead)
    ]);
  }

}}