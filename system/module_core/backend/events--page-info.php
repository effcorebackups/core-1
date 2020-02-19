<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\block;
          use \effcore\core;
          use \effcore\decorator;
          use \effcore\language;
          use \effcore\locale;
          use \effcore\markup_simple;
          use \effcore\markup;
          use \effcore\module;
          use \effcore\node;
          use \effcore\storage;
          use \effcore\text;
          abstract class events_page_info {

  static function block_system_info($page) {
    $logo      = new markup('x-logo',      [], new markup_simple('img', ['src' => '/'.module::get('page')->path.'frontend/images/logo-simplified.svgd', 'alt' => new text('system logotype'), 'width' => '300']));
    $copyright = new markup('x-copyright', [], 'Copyright © 2017—2020 Maxim Rysevets. All rights reserved.');
    $build     = new markup('x-build',     [], [
      new markup('x-title', [], 'Build number'),
      new markup('x-value', [], storage::get('files')->select('bundle/system/build'))]);
    return new block('System information', ['data-id' => 'info_system', 'data-title-is-hidden' => true], [
      $logo,
      $copyright,
      $build
    ]);
  }

  static function block_service_info($page) {
    $settings            = module::settings_get('core');
    $is_required_updates = module::is_required_updates();
    $is_required_updates_fixlink = new markup('a', ['href' => '/manage/modules/update'], 'fix');
    $is_required_updates_sticker = new markup('x-sticker', ['data-state' => !$is_required_updates ? 'ok' : 'warning'], $is_required_updates ? 'yes' : 'no');
    $cron_last_run_sticker       = new markup('x-sticker', ['data-state' => !empty($settings->cron_last_run_date) && $settings->cron_last_run_date > core::datetime_get('-'.core::date_period_d.' second') ? 'ok' : 'warning'], locale::format_datetime($settings->cron_last_run_date) ?? 'no');
    $cron_link = new markup('a', ['target' => 'cron', 'href' => '/manage/cron/'.core::key_get('cron')], '/manage/cron/'.core::key_get('cron'));
    $decorator = new decorator('table-dl');
    $decorator->id = 'service_info';
    $decorator->data = [[
      'prov_key'      => ['title' => 'Provisioning key',        'value' => 'not applicable'                                                                                                                                   ],
      'subscr_to_upd' => ['title' => 'Subscription to updates', 'value' => 'not applicable'                                                                                                                                   ],
      'upd_is_req'    => ['title' => 'Data update is required', 'value' => new node([], $is_required_updates ? [$is_required_updates_sticker, new text(' → '), $is_required_updates_fixlink] : [$is_required_updates_sticker])],
      'cron_url'      => ['title' => 'Cron URL',                'value' => $cron_link                                                                                                                                         ],
      'cron_last_run' => ['title' => 'Cron last run',           'value' => $cron_last_run_sticker                                                                                                                             ]]];
    return new block('Service', ['data-id' => 'info_service', 'data-title-is-styled' => 'false'], [
      $decorator
    ]);
  }

  static function block_environment_info($page) {
    $storage_sql = storage::get('sql');
    $is_enabled_opcache = function_exists('opcache_get_status') && !empty(@opcache_get_status(false)['opcache_enabled']);
    $is_enabled_opcache_sticker = new markup('x-sticker', ['data-state' => $is_enabled_opcache ? 'ok' : 'warning'], $is_enabled_opcache ? 'yes' : 'no');
    $decorator = new decorator('table-dl');
    $decorator->id = 'environment_info';
    $decorator->data = [[
      'web_server'       => ['title' => 'Web server',             'value' => core::server_get_software()                              ],
      'php_version'      => ['title' => 'PHP version',            'value' => phpversion().' ('.php_uname('m').')'                     ],
      'opcache_state'    => ['title' => 'PHP OPCache is enabled', 'value' => $is_enabled_opcache_sticker                              ],
      'storage_sql'      => ['title' => 'SQL storage',            'value' => $storage_sql->title_get().' '.$storage_sql->version_get()],
      'operating_system' => ['title' => 'Operating System',       'value' => php_uname('s').' | '.php_uname('v')                      ],
      'hostname'         => ['title' => 'Hostname',               'value' => php_uname('n')                                           ],
      'timezone'         => ['title' => 'Time zone',              'value' => date_default_timezone_get()                              ],
      'datetime'         => ['title' => 'UTC date/time',          'value' => core::datetime_get()                                     ]]];
    return new block('Server', ['data-id' => 'info_server', 'data-title-is-styled' => 'false'], [
      $decorator
    ]);
  }

}}