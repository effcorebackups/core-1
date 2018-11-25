<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\block;
          use \effcore\core;
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

  static function on_show_block_info($page) {
    $storage_files = storage::get('files');
    $storage_sql   = storage::get('sql');
    $info = new markup('dl', ['class' => ['info' => 'info']]);
    $logo_system = new markup_simple('img', ['src' => '/'.module::get('page')->path.'frontend/images/logo-system.svg', 'alt' => 'effcore', 'height' => '30']);
    $cron_link = new markup('a', ['target' => 'cron', 'href' => '/cron/'.core::key_get('cron')], '/cron/'.core::key_get('cron'));
    $info->child_insert(new markup('dt', [], 'System'));
    $info->child_insert(new markup('dd', [], $logo_system));
    $info->child_insert(new markup('dt', [], 'Copyright'));
    $info->child_insert(new markup('dd', [], '© 2017—2019 Maxim Rysevets. All rights reserved.'));
    $info->child_insert(new markup('dt', [], 'Build number'));
    $info->child_insert(new markup('dd', [], $storage_files->select('bundle/system/build')));
    $info->child_insert(new markup('dt', [], 'Web server'));
    $info->child_insert(new markup('dd', [], core::server_software_get()));
    $info->child_insert(new markup('dt', [], 'PHP Version'));
    $info->child_insert(new markup('dd', [], phpversion().' ('.php_uname('m').')'));
    $info->child_insert(new markup('dt', [], translation::get('Storage %%_name', ['name' => $storage_sql->name])));
    $info->child_insert(new markup('dd', [], $storage_sql->title_get().' '.$storage_sql->version_get()));
    $info->child_insert(new markup('dt', [], 'Operating System'));
    $info->child_insert(new markup('dd', [], php_uname('s')));
    $info->child_insert(new markup('dt', [], 'OS Version'));
    $info->child_insert(new markup('dd', [], php_uname('v')));
    $info->child_insert(new markup('dt', [], 'Hostname'));
    $info->child_insert(new markup('dd', [], php_uname('n')));
    $info->child_insert(new markup('dt', [], 'Server timezone'));
    $info->child_insert(new markup('dd', [], date_default_timezone_get()));
    $info->child_insert(new markup('dt', [], 'Server date / time'));
    $info->child_insert(new markup('dd', [], locale::datetime_global_to_native(core::datetime_get())));
    $info->child_insert(new markup('dt', [], 'Session expiration date'));
    $info->child_insert(new markup('dd', [], locale::timestmp_global_to_native(session::id_expired_extract(session::id_get()))));
    $info->child_insert(new markup('dt', [], 'Cron URL'));
    $info->child_insert(new markup('dd', [], $cron_link));
    $info->child_insert(new markup('dt', [], 'Provisioning key'));
    $info->child_insert(new markup('dd', [], 'not applicable'));
    $info->child_insert(new markup('dt', [], 'Subscribe to updates'));
    $info->child_insert(new markup('dd', [], 'not applicable'));
    return new block('Shared information', ['class' => ['info' => 'info']], [
      $info
    ]);
  }

}}