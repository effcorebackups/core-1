<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
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

  static function block_markup__system_info($page, $args = []) {
    $logo      = new markup('x-logo',      [], new markup_simple('img', ['src' => '/'.module::get('page')->path.'frontend/pictures/logo-simplified.svgd', 'alt' => new text('system logotype'), 'width' => '300']));
    $copyright = new markup('x-copyright', [], 'Copyright © 2017—2021 Maxim Rysevets. All rights reserved.');
    $build     = new markup('x-build',     [], [
      new markup('x-title', [], 'Build number'),
      new markup('x-value', [], storage::get('files')->select('bundle/system/build'))]);
    return new node([], [
      $logo,
      $copyright,
      $build
    ]);
  }

  static function block_markup__service_info($page, $args = []) {
    $settings           = module::settings_get('core');
    $is_required_update = module::is_required_update_data();
    $is_required_update_fixlink = new markup('a', ['href' => '/manage/modules/update/data'], 'fix');
    $is_required_update_sticker = new markup('x-sticker', ['data-state' => !$is_required_update ? 'ok' : 'warning'], $is_required_update ? 'yes' : 'no');
    $cron_last_run_sticker      = new markup('x-sticker', ['data-state' => !empty($settings->cron_last_run_date) && $settings->cron_last_run_date > core::datetime_get('-'.core::date_period_d.' second') ? 'ok' : 'warning'], locale::format_datetime($settings->cron_last_run_date) ?? 'no');
    $cron_link = new markup('a', ['target' => 'cron', 'href' => '/manage/cron/'.core::key_get('cron')], '/manage/cron/'.core::key_get('cron'));
    $decorator = new decorator('table-dl');
    $decorator->id = 'service_info';
    $decorator->data = [[
      'cron_url'      => ['title' => 'Cron URL',                'value' => $cron_link                                                                                                                                     ],
      'cron_last_run' => ['title' => 'Cron last run',           'value' => $cron_last_run_sticker                                                                                                                         ],
      'upd_is_req'    => ['title' => 'Data update is required', 'value' => new node([], $is_required_update ? [$is_required_update_sticker, new text(' → '), $is_required_update_fixlink] : [$is_required_update_sticker])] ]];
    return new node([], [
      $decorator
    ]);
  }

  static function block_markup__environment_info($page, $args = []) {
    $storage_sql = storage::get('sql');
    $php_version_curl = curl_version()['version'].' | ssl: '.curl_version()['ssl_version'].' | libz: '.curl_version()['libz_version'];
    $is_enabled_opcache = function_exists('opcache_get_status') && !empty(@opcache_get_status(false)['opcache_enabled']);
    $is_enabled_opcache_sticker = new markup('x-sticker', ['data-state' => $is_enabled_opcache ? 'ok' : 'warning'], $is_enabled_opcache ? 'yes' : 'no');
    $decorator = new decorator('table-dl');
    $decorator->id = 'environment_info';
    $decorator->data = [[
      'web_server'                  => ['title' => 'Web server',              'value' => core::server_get_software()                                ],
      'php_version'                 => ['title' => 'PHP version',             'value' => phpversion()                                               ],
      'php_version_curl'            => ['title' => 'PHP CURL version',        'value' => $php_version_curl                                          ],
      'php_version_pcre'            => ['title' => 'PHP PCRE version',        'value' => PCRE_VERSION                                               ],
      'php_state_opcache'           => ['title' => 'PHP OPCache is enabled',  'value' => $is_enabled_opcache_sticker                                ],
      'php_ini_max_file_uploads'    => ['title' => 'PHP max_file_uploads',    'value' => core::max_file_uploads_get()                               ],
      'php_ini_upload_max_filesize' => ['title' => 'PHP upload_max_filesize', 'value' => locale::format_bytes(core::upload_max_filesize_bytes_get())],
      'php_ini_post_max_size'       => ['title' => 'PHP post_max_size',       'value' => locale::format_bytes(core::post_max_size_bytes_get())      ],
      'php_ini_max_input_time'      => ['title' => 'PHP max_input_time',      'value' => locale::format_seconds(core::max_input_time_get())         ],
      'php_ini_max_execution_time'  => ['title' => 'PHP max_execution_time',  'value' => locale::format_seconds(core::max_execution_time_get())     ],
      'storage_sql'                 => ['title' => 'SQL storage',             'value' => $storage_sql->title_get().' '.$storage_sql->version_get()  ],
      'operating_system'            => ['title' => 'Operating System',        'value' => php_uname('s').' | '.php_uname('r').' | '.php_uname('v')   ],
      'architecture'                => ['title' => 'Architecture',            'value' => php_uname('m')                                             ],
      'hostname'                    => ['title' => 'Hostname',                'value' => php_uname('n')                                             ],
      'timezone'                    => ['title' => 'Time zone',               'value' => date_default_timezone_get()                                ],
      'datetime'                    => ['title' => 'UTC date/time',           'value' => core::datetime_get()                                       ] ]];
    return new node([], [
      $decorator
    ]);
  }

}}