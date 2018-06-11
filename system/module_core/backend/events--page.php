<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use const \effcore\br;
          use \effcore\control_actions_list;
          use \effcore\core;
          use \effcore\event;
          use \effcore\locale;
          use \effcore\markup_simple;
          use \effcore\markup;
          use \effcore\module;
          use \effcore\node;
          use \effcore\storage;
          use \effcore\table_body_row_cell;
          use \effcore\table;
          use \effcore\timer;
          use \effcore\translation;
          abstract class events_page extends \effcore\events_page {

  static function on_cron_run($page) {
    if ($page->args_get('key') === core::key_get('cron')) {
      timer::tap('cron');
      event::start('on_cron');
      timer::tap('cron');
      print 'cron execution time: '.timer::get_period('cron', -1, -2).' sec.';
      exit();
    } else {
      core::send_header_and_exit('page_not_found');
    }
  }

  static function on_show_info($page) {
    $title = new markup('h2', [], 'Shared information'); # @todo: move title to block settings
    $info = new markup('dl', ['class' => ['info' => 'info']]);
    $logo_effcore = new markup_simple('img', ['src' => '/'.module::get('page')->get_path().'frontend/images/logo-effcore._vector', 'alt' => 'effcore']);
    $cron_link = new markup('a', ['target' => 'cron', 'href' => '/cron/'.core::key_get('cron')], '/cron/'.core::key_get('cron'));
    $info->child_insert(new markup('dt', [], 'System'));
    $info->child_insert(new markup('dd', [], $logo_effcore));
    $info->child_insert(new markup('dt', [], 'Bundle build number'));
    $info->child_insert(new markup('dd', [], storage::get('files')->select('bundle/system/build')));
    $info->child_insert(new markup('dt', [], 'Author'));
    $info->child_insert(new markup('dd', [], 'Maxim Rysevets'));
    $info->child_insert(new markup('dt', [], 'Build years'));
    $info->child_insert(new markup('dd', [], '2017—2018'));
    $info->child_insert(new markup('dt', [], 'All rights reserved'));
    $info->child_insert(new markup('dd', [], 'yes'));
    $info->child_insert(new markup('dt', [], 'Server software'));
    $info->child_insert(new markup('dd', [], core::server_full_name_get()));
    $info->child_insert(new markup('dt', [], 'PHP Version'));
    $info->child_insert(new markup('dd', [], phpversion()));
    $info->child_insert(new markup('dt', [], 'Architecture'));
    $info->child_insert(new markup('dd', [], php_uname('m')));
    $info->child_insert(new markup('dt', [], 'Operating System'));
    $info->child_insert(new markup('dd', [], php_uname('s')));
    $info->child_insert(new markup('dt', [], 'OS Version'));
    $info->child_insert(new markup('dd', [], php_uname('v')));
    $info->child_insert(new markup('dt', [], 'Hostname'));
    $info->child_insert(new markup('dd', [], php_uname('n')));
    $info->child_insert(new markup('dt', [], 'Cron URL'));
    $info->child_insert(new markup('dd', [], $cron_link));
    $info->child_insert(new markup('dt', [], 'Provisioning key'));
    $info->child_insert(new markup('dd', [], 'no'));
    $info->child_insert(new markup('dt', [], 'Subscribe for updates'));
    $info->child_insert(new markup('dd', [], 'no'));
    return new node([], [$title, $info]);
  }

  static function on_show_modules($page) {
    $thead = [['Module information', 'State', '']];
    $tbody = [];
    foreach (module::all_get() as $c_module) {
      $c_action_list = new control_actions_list();
      $c_action_list->action_add('/manage/modules/'.$c_module->id.'/enable', 'enable',       $c_module->state != 'always_on');
      $c_action_list->action_add('/manage/modules/'.$c_module->id.'/disable', 'disable',     $c_module->state != 'always_on');
      $c_action_list->action_add('/manage/modules/'.$c_module->id.'/uninstall', 'uninstall', $c_module->state != 'always_on');
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
    return new markup('x-block', ['class' => ['modules']], [
      new table([], $tbody, $thead)
    ]);
  }

}}