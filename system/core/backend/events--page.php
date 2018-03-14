<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use const \effcore\br;
          use \effcore\node;
          use \effcore\table;
          use \effcore\markup;
          use \effcore\module;
          use \effcore\locale;
          use \effcore\storage;
          use \effcore\translation;
          use \effcore\markup_simple;
          use \effcore\table_body_row_cell;
          use \effcore\control_actions_list;
          abstract class events_page extends \effcore\events_page {

  static function on_show_info($page) {
    $title = new markup('h2', [], 'Shared information'); # @todo: move title to block settings
    $info = new markup('dl', ['class' => ['info' => 'info']]);
    $logo_effcore = new markup_simple('img', ['src' => '/system/page/frontend/logo-effcore._vector', 'alt' => 'effcore']);
    $info->child_insert(new markup('dt', [], 'System'));
    $info->child_insert(new markup('dd', [], $logo_effcore));
    $info->child_insert(new markup('dt', [], 'Bundle build number'));
    $info->child_insert(new markup('dd', [], storage::get('files')->select('bundle/global/build')));
    $info->child_insert(new markup('dt', [], 'Author'));
    $info->child_insert(new markup('dd', [], 'Maxim Rysevets'));
    $info->child_insert(new markup('dt', [], 'Build years'));
    $info->child_insert(new markup('dd', [], '2017—2018'));
    $info->child_insert(new markup('dt', [], 'All rights reserved'));
    $info->child_insert(new markup('dd', [], 'yes'));
    $info->child_insert(new markup('dt', [], 'Valid HTML5'));
    $info->child_insert(new markup('dd', [], 'yes'));
    $info->child_insert(new markup('dt', [], 'Valid CSS'));
    $info->child_insert(new markup('dd', [], 'yes'));
    $info->child_insert(new markup('dt', [], 'Architecture'));
    $info->child_insert(new markup('dd', [], php_uname('m')));
    $info->child_insert(new markup('dt', [], 'Server software'));
    $info->child_insert(new markup('dd', [], $_SERVER['SERVER_SOFTWARE']));
    $info->child_insert(new markup('dt', [], 'Operating System'));
    $info->child_insert(new markup('dd', [], php_uname('s')));
    $info->child_insert(new markup('dt', [], 'Hostname'));
    $info->child_insert(new markup('dd', [], php_uname('n')));
    $info->child_insert(new markup('dt', [], 'PHP Version'));
    $info->child_insert(new markup('dd', [], php_uname('v')));
    $info->child_insert(new markup('dt', [], 'PHP Release'));
    $info->child_insert(new markup('dd', [], php_uname('r')));
    $info->child_insert(new markup('dt', [], 'Provisioning key'));
    $info->child_insert(new markup('dd', [], 'no'));
    $info->child_insert(new markup('dt', [], 'Subscribe for updates'));
    $info->child_insert(new markup('dd', [], 'no'));
    return new node([], [$title, $info]);
  }

  static function on_show_modules($page) {
    $thead = [['Module information', 'State', '']];
    $tbody = [];
    foreach (module::get_all() as $c_module) {
      $c_action_list = new control_actions_list([], [], null);
      $c_action_list->action_add('/admin/modules/'.$c_module->id.'/enable', 'enable',       $c_module->state != 'always_on');
      $c_action_list->action_add('/admin/modules/'.$c_module->id.'/disable', 'disable',     $c_module->state != 'always_on');
      $c_action_list->action_add('/admin/modules/'.$c_module->id.'/uninstall', 'uninstall', $c_module->state != 'always_on');
      $tbody[] = [
        new table_body_row_cell(['class' => ['info' => 'info']],
          translation::get('ID')         .': '.$c_module->id.br.
          translation::get('Version')    .': '.locale::format_version($c_module->version).br.
          translation::get('Title')      .': '.translation::get($c_module->title).br.
          translation::get('Description').': '.translation::get($c_module->description).br.
          translation::get('Path')       .': '.$c_module->path),
        new table_body_row_cell(['class' => ['state' => 'state']], $c_module->state),
        new table_body_row_cell(['class' => ['actions' => 'actions']], $c_action_list)
      ];
    }
    return new markup('x-block', ['id' => 'modules_admin'], [
      new table([], $tbody, $thead)
    ]);
  }

}}