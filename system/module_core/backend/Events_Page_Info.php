<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\core;

use effcore\Core;
use effcore\Decorator;
use effcore\Locale;
use effcore\Markup_simple;
use effcore\Markup;
use effcore\Module;
use effcore\Node;
use effcore\Request;
use effcore\Storage;
use effcore\Text;
use effcore\Token;
use effcore\Update;
use effcore\Url;
use effcore\User;

abstract class Events_Page_Info {

    static function block_markup__system_info($page, $args = []) {
        $logo      = new Markup('x-logo',      [], new Markup_simple('img', ['src' => '/'.Module::get('page')->path.Token::apply('frontend/pictures/logo.svgd?color=%%_return_token_color_encode(color__text)&color_fire=%%_return_token_color_encode(color__text)'), 'alt' => new Text('system logotype'), 'width' => '300']));
        $copyright = new Markup('x-copyright', [], 'Copyright © 2017—2023 Maxim Rysevets. All rights reserved.');
        $build     = new Markup('x-build',     [], [
            new Markup('x-title', [], 'Build number'),
            new Markup('x-value', [], Storage::get('data')->select('bundle/system/build'))]);
        return new Node([], [
            $logo,
            $copyright,
            $build
        ]);
    }

    static function block_markup__service_info($page, $args = []) {
        $settings           = Module::settings_get('core');
        $is_required_update = Update::is_required();
        $is_cron_run = Core::is_cron_run(Core::DATE_PERIOD_D);
        $cron_auto_run_frequency = $settings->cron_auto_run_frequency ?
            Locale::format_seconds($settings->cron_auto_run_frequency): 'no';
        $cron_url = Request::scheme_get().'://'.
                    Request::host_get(false).'/manage/cron/'.
                    User::key_get('cron');
        $fix_link_for_cron   = new Markup('a', ['href' => $cron_url,                     'target' => 'cron'  ], 'fix');
        $fix_link_for_update = new Markup('a', ['href' => '/manage/modules/update/data', 'target' => 'update'], 'fix');
        $sticker_for_cron_last_run      = new Markup('x-sticker', ['data-style' => $is_cron_run        ? 'ok' : 'warning'], $is_cron_run ? $settings->cron_last_run_date : [$settings->cron_last_run_date ? $settings->cron_last_run_date : 'no', ' → ', $fix_link_for_cron]);
        $sticker_for_is_required_update = new Markup('x-sticker', ['data-style' => $is_required_update ? 'warning' : 'ok'], $is_required_update ? ['yes', ' → ', $fix_link_for_update] : 'no');
        $decorator = new Decorator('table-dl');
        $decorator->id = 'service_info';
        $decorator->data = [[
            'cron_url'      => ['title' => 'Cron URL',                'value' => Url::url_to_markup($cron_url)   ],
            'cron_auto_run' => ['title' => 'Cron autorun frequency',  'value' => $cron_auto_run_frequency        ],
            'cron_last_run' => ['title' => 'Cron last run',           'value' => $sticker_for_cron_last_run      ],
            'update_is_req' => ['title' => 'Data update is required', 'value' => $sticker_for_is_required_update ] ]];
        return new Node([], [
            $decorator
        ]);
    }

    static function block_markup__environment_info($page, $args = []) {
        $web_server_info         = Request::software_get_info();
        $storage_sql             = Storage::get('sql');
        $php_version_curl        = curl_version()['version'].' | ssl: '.curl_version()['ssl_version'].' | libz: '.curl_version()['libz_version'];
        $is_enabled_opcache      = function_exists('opcache_get_status') && !empty(opcache_get_status(false)['opcache_enabled']);
        $is_enabled_opcache_jit  = function_exists('opcache_get_status') && !empty(opcache_get_status(false)['jit']['enabled']);
        $php_memory_limit        = Core::php_memory_limit_bytes_get();
        $php_max_file_uploads    = Core::php_max_file_uploads_get();
        $php_upload_max_filesize = Core::php_upload_max_filesize_bytes_get();
        $php_post_max_size       = Core::php_post_max_size_bytes_get();
        $php_max_input_time      = Core::php_max_input_time_get();
        $php_max_execution_time  = Core::php_max_execution_time_get();
        $sticker_for_is_enabled_opcache      =                                               new Markup('x-sticker', ['data-style' => $is_enabled_opcache     ? 'ok' : 'warning'], $is_enabled_opcache     ? 'yes' : 'no');
        $sticker_for_is_enabled_opcache_jit  = version_compare(phpversion(), '8.0.0', '>') ? new Markup('x-sticker', ['data-style' => $is_enabled_opcache_jit ? 'ok' : 'warning'], $is_enabled_opcache_jit ? 'yes' : 'no') : '-';
        $sticker_for_php_memory_limit        = new Markup('x-sticker', ['data-style' => $php_memory_limit        >= 0x10000000 ? 'ok' : 'warning', 'title' => (new Text('Recommended minimum value: %%_value', ['value' => Locale::format_bytes  (0x10000000)]))->render()], Locale::format_bytes  ($php_memory_limit)       );
        $sticker_for_php_max_file_uploads    = new Markup('x-sticker', ['data-style' => $php_max_file_uploads    >= 20         ? 'ok' : 'warning', 'title' => (new Text('Recommended minimum value: %%_value', ['value' => Locale::format_pieces (20)]))        ->render()], Locale::format_pieces ($php_max_file_uploads)   );
        $sticker_for_php_upload_max_filesize = new Markup('x-sticker', ['data-style' => $php_upload_max_filesize >= 0x40000000 ? 'ok' : 'warning', 'title' => (new Text('Recommended minimum value: %%_value', ['value' => Locale::format_bytes  (0x40000000)]))->render()], Locale::format_bytes  ($php_upload_max_filesize));
        $sticker_for_php_post_max_size       = new Markup('x-sticker', ['data-style' => $php_post_max_size       >= 0x40000000 ? 'ok' : 'warning', 'title' => (new Text('Recommended minimum value: %%_value', ['value' => Locale::format_bytes  (0x40000000)]))->render()], Locale::format_bytes  ($php_post_max_size)      );
        $sticker_for_php_max_input_time      = new Markup('x-sticker', ['data-style' => $php_max_input_time      >= 60         ? 'ok' : 'warning', 'title' => (new Text('Recommended minimum value: %%_value', ['value' => Locale::format_seconds(60)]))        ->render()], Locale::format_seconds($php_max_input_time)     );
        $sticker_for_php_max_execution_time  = new Markup('x-sticker', ['data-style' => $php_max_execution_time  >= 60         ? 'ok' : 'warning', 'title' => (new Text('Recommended minimum value: %%_value', ['value' => Locale::format_seconds(60)]))        ->render()], Locale::format_seconds($php_max_execution_time) );
        $decorator = new Decorator('table-dl');
        $decorator->id = 'environment_info';
        $decorator->data = [[
            'web_server'              => ['title' => 'Web server',                 'value' => ucfirst($web_server_info->name).' '.$web_server_info->version],
            'php_version'             => ['title' => 'PHP version',                'value' => phpversion()                                                 ],
            'php_version_curl'        => ['title' => 'PHP CURL version',           'value' => $php_version_curl                                            ],
            'php_version_pcre'        => ['title' => 'PHP PCRE version',           'value' => PCRE_VERSION                                                 ],
            'php_state_opcache'       => ['title' => 'PHP OPCache is enabled',     'value' => $sticker_for_is_enabled_opcache                              ],
            'php_state_opcache_jit'   => ['title' => 'PHP OPCache JIT is enabled', 'value' => $sticker_for_is_enabled_opcache_jit                          ],
            'php_memory_limit'        => ['title' => 'PHP memory_limit',           'value' => $sticker_for_php_memory_limit                                ],
            'php_max_file_uploads'    => ['title' => 'PHP max_file_uploads',       'value' => $sticker_for_php_max_file_uploads                            ],
            'php_upload_max_filesize' => ['title' => 'PHP upload_max_filesize',    'value' => $sticker_for_php_upload_max_filesize                         ],
            'php_post_max_size'       => ['title' => 'PHP post_max_size',          'value' => $sticker_for_php_post_max_size                               ],
            'php_max_input_time'      => ['title' => 'PHP max_input_time',         'value' => $sticker_for_php_max_input_time                              ],
            'php_max_execution_time'  => ['title' => 'PHP max_execution_time',     'value' => $sticker_for_php_max_execution_time                          ],
            'storage_sql'             => ['title' => 'SQL storage',                'value' => $storage_sql->title_get().' '.$storage_sql->version_get()    ],
            'operating_system'        => ['title' => 'Operating System',           'value' => php_uname('s').' | '.php_uname('r').' | '.php_uname('v')     ],
            'architecture'            => ['title' => 'Architecture',               'value' => php_uname('m')                                               ],
            'hostname'                => ['title' => 'Hostname',                   'value' => php_uname('n')                                               ],
            'datetime'                => ['title' => 'Date/Time',                  'value' => Core::datetime_get().' (UTC)'                                ] ]];
        return new Node([], [
            $decorator
        ]);
    }

}
