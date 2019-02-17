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

  static function on_show_logo_system($page) {
    $logo           = new markup_simple('img', ['src' => '/'.module::get('page')->path.'frontend/images/logo-system.svg', 'alt' => 'effcore', 'height' => '100']);
    $info_copyright = new markup('x-info-copyright', [], translation::get('© 2017—2019 Maxim Rysevets. All rights reserved.'));
    $info_build     = new markup('x-info-build',     [], translation::get('Build number: %%_number', ['number' => storage::get('files')->select('bundle/system/build')]));
    return new block('', ['class' => ['logo-system' => 'logo-system']], [
      $logo,
      $info_copyright,
      $info_build
    ]);
  }

  static function on_show_block_system_info($page) {
    $storage_files = storage::get('files');
    $logo_system = new markup_simple('img', ['src' => '/'.module::get('page')->path.'frontend/images/logo-system.svg', 'alt' => 'effcore', 'height' => '100']);
    $cron_link = new markup('a', ['target' => 'cron', 'href' => '/cron/'.core::key_get('cron')], '/cron/'.core::key_get('cron'));
    $decorator = new decorator('dl');
    $decorator->data = [[
      'logo'          => ['title' => '',                     'value' => $logo_system                                      ],
      'copyright'     => ['title' => 'Copyright',            'value' => '© 2017—2019 Maxim Rysevets. All rights reserved.'],
      'build_number'  => ['title' => 'Build number',         'value' => $storage_files->select('bundle/system/build')     ],
      'prov_key'      => ['title' => 'Provisioning key',     'value' => 'not applicable'                                  ],
      'subscr_to_upd' => ['title' => 'Subscribe to updates', 'value' => 'not applicable'                                  ],
      'cron_url'      => ['title' => 'Cron URL',             'value' => $cron_link                                        ]
    ]];
    return new block('', ['class' => ['system-info' => 'system-info']], [
      $decorator->build()
    ]);
  }

  static function on_show_block_environment_info($page) {
    $storage_sql = storage::get('sql');
    $is_enabled_opcache = function_exists('opcache_get_status') && !empty(opcache_get_status(false)['opcache_enabled']);
    $is_enabled_opcache_value = new markup('x-value', ['data-state' => $is_enabled_opcache ? 'ok' : 'warning'], $is_enabled_opcache ? 'yes' : 'no');
    $decorator = new decorator('dl');
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