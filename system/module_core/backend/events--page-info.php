<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\block;
          use \effcore\core;
          use \effcore\decorator;
          use \effcore\locale;
          use \effcore\markup_simple;
          use \effcore\markup;
          use \effcore\module;
          use \effcore\session;
          use \effcore\storage;
          use \effcore\translation;
          abstract class events_page_info {

  static function on_show_block_system_info($page) {
    $logo      = new markup('x-logo',      [], new markup_simple('img', ['src' => '/'.module::get('page')->path.'frontend/images/logo-system.svg', 'alt' => 'effcore', 'width' => '300']));
    $copyright = new markup('x-copyright', [], '© 2017—2019 Maxim Rysevets. All rights reserved.');
    $build     = new markup('x-build',     [], [
      new markup('x-title', [], 'Build number'),
      new markup('x-value', [], storage::get('files')->select('bundle/system/build'))
    ]);
    return new block('', ['class' => ['system-info' => 'system-info']], [
      $logo,
      $copyright,
      $build
    ]);
  }

  static function on_show_block_service_info($page) {
    $storage_files = storage::get('files');
    $cron_link = new markup('a', ['target' => 'cron', 'href' => '/cron/'.core::key_get('cron')], '/cron/'.core::key_get('cron'));
    $decorator = new decorator('dl');
    $decorator->id = 'service_info';
    $decorator->data = [[
      'prov_key'      => ['title' => 'Provisioning key',     'value' => 'not applicable'],
      'subscr_to_upd' => ['title' => 'Subscribe to updates', 'value' => 'not applicable'],
      'cron_url'      => ['title' => 'Cron URL',             'value' => $cron_link      ]
    ]];
    return new block('Service', ['class' => ['service-info' => 'service-info']], [
      $decorator->build()
    ]);
  }

  static function on_show_block_environment_info($page) {
    $storage_sql = storage::get('sql');
    $is_enabled_opcache = function_exists('opcache_get_status') && !empty(opcache_get_status(false)['opcache_enabled']);
    $is_enabled_opcache_value = new markup('x-value', ['data-state' => $is_enabled_opcache ? 'ok' : 'warning'], $is_enabled_opcache ? 'yes' : 'no');
    $decorator = new decorator('dl');
    $decorator->id = 'environment_info';
    $decorator->data = [[
      'web_server'    => ['title' => 'Web server',             'value' => core::server_software_get()                              ],
      'php_version'   => ['title' => 'PHP Version',            'value' => phpversion().' ('.php_uname('m').')'                     ],
      'opcache_state' => ['title' => 'PHP OPcache is anebled', 'value' => $is_enabled_opcache_value                                ],
      'storage_sql'   => ['title' => 'Storage SQL',            'value' => $storage_sql->title_get().' '.$storage_sql->version_get()],
      'os_name'       => ['title' => 'Operating System',       'value' => php_uname('s')                                           ],
      'os_version'    => ['title' => 'OS Version',             'value' => php_uname('v')                                           ],
      'hostname'      => ['title' => 'Hostname',               'value' => php_uname('n')                                           ],
      'timezone'      => ['title' => 'Server timezone',        'value' => date_default_timezone_get()                              ],
      'datetime'      => ['title' => 'Server UTC date / time', 'value' => core::datetime_get()                                     ],
    ]];
    return new block('Environment', ['class' => ['environment-info' => 'environment-info']], [
      $decorator->build()
    ]);
  }

}}