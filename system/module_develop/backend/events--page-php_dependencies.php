<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\develop;

use const effcore\DIR_MODULES;
use const effcore\DIR_SYSTEM;
use effcore\core;
use effcore\decorator;
use effcore\file;
use effcore\markup;
use effcore\module;
use effcore\node;
use effcore\text_multiline;
use effcore\text_simple;

abstract class events_page_php_dependencies {

    # legend: + the extension is always enabled
    #         ± the extension is enabled by default
    #         − the extension is not enabled by default

    const EXTENSIONS_DEFAULT_STATUS = [
        'bcmath'       => '±',
        'bz2'          => '−',
        'calendar'     => '±',
        'cgi-fcgi'     => '−',
        'Core'         => '+',
        'ctype'        => '±',
        'curl'         => '−',
        'date'         => '+',
        'dba'          => '−',
        'dom'          => '±',
        'exif'         => '−',
        'fileinfo'     => '±',
        'filter'       => '±',
        'ftp'          => '−',
        'gd'           => '−',
        'gettext'      => '−',
        'gmp'          => '−',
        'hash'         => '±',
        'iconv'        => '±',
        'imap'         => '−',
        'interbase'    => '−',
        'intl'         => '−',
        'json'         => '+',
        'ldap'         => '−',
        'libxml'       => '±',
        'mbstring'     => '−',
        'mysqli'       => '−',
        'mysqlnd'      => '−',
        'oci8_12c'     => '−',
        'odbc'         => '−',
        'openssl'      => '−',
        'pcre'         => '+',
        'pdo_firebird' => '−',
        'pdo_mysql'    => '−',
        'pdo_oci'      => '−',
        'pdo_odbc'     => '−',
        'pdo_pgsql'    => '−',
        'pdo_sqlite'   => '−',
        'PDO'          => '±',
        'pgsql'        => '−',
        'Phar'         => '±',
        'posix'        => '±',
        'readline'     => '−',
        'Reflection'   => '+',
        'session'      => '±',
        'shmop'        => '−',
        'SimpleXML'    => '±',
        'snmp'         => '−',
        'soap'         => '−',
        'sockets'      => '−',
        'SPL'          => '+',
        'sqlite3'      => '−',
        'standard'     => '+',
        'sysvmsg'      => '−',
        'sysvsem'      => '−',
        'sysvshm'      => '−',
        'tidy'         => '−',
        'tokenizer'    => '±',
        'wddx'         => '−',
        'xml'          => '±',
        'xmlreader'    => '±',
        'xmlrpc'       => '−',
        'xmlwriter'    => '±',
        'xsl'          => '−',
        'Zend OPcache' => '−',
        'zip'          => '−',
        'zlib'         => '−'
    ];

    static function block_markup__php_dependencies_list($page, $args = []) {
        $modules_path = module::get_all('path');
        arsort($modules_path);
        $statistic_by_mod = [];
        $statistic_by_fnc = [];
        $statistic_by_ext = [];
        $functions_by_ext = [];
        foreach (get_loaded_extensions() as $c_extension) {
            foreach (get_extension_funcs($c_extension) ?: [] as $c_function) {
                $functions_by_ext[$c_function] = $c_extension;
            }
        }
        # scan each php file on used functions
        foreach (file::select_recursive(DIR_SYSTEM,  '%^.*\\.php$%') +
                 file::select_recursive(DIR_MODULES, '%^.*\\.php$%') as $c_path => $c_file) {
            $c_matches = [];
            $c_path_relative = $c_file->path_get_relative();
            $c_module_id = key(core::array_search__any_array_item_in_value($c_path_relative, $modules_path));
            # load file and search functions in it
            preg_match_all('%(?<![a-zA-Z0-9_])(?<name>[a-zA-Z0-9_]+)\\(%sS', $c_file->load(), $c_matches, PREG_OFFSET_CAPTURE);
            if ($c_matches) {
                foreach ($c_matches['name'] as $c_match) {
                    if (isset($functions_by_ext[$c_match[0]])) {
                        $c_extension = $functions_by_ext[$c_match[0]];
                        $c_function = $c_match[0];
                        $c_position = $c_match[1];
                        $statistic_by_fnc[$c_function][] = $c_position;
                        $statistic_by_mod[$c_module_id][$c_extension][] = $c_position;
                        $statistic_by_ext[$c_extension][$c_function][] = (object)[
                            'file'     => $c_path_relative,
                            'position' => $c_position,
                            'module'   => $c_module_id
                        ];
                    }
                }
            }
        }
        ksort($statistic_by_mod);
        ksort($statistic_by_fnc);
        ksort($statistic_by_ext);

        # ─────────────────────────────────────────────────────────────────────
        # prepare report by modules
        # ─────────────────────────────────────────────────────────────────────

        $mod_title = new markup('h2', [], 'Module dependencies from PHP extensions');
        $mod_legend = new markup('p', [], new text_multiline([
            '+ the extension is always enabled',
            '± the extension is enabled by default',
            '− the extension is not enabled by default']));
        $mod_decorator = new decorator('table-adaptive');
        $mod_decorator->id = 'modules_dependency';
        foreach ($statistic_by_mod as $c_module_id => $c_extensions) {
            if ($c_module_id) {
                ksort($c_extensions);
                $c_extensions_list = new text_multiline([], [], ', ', false, false);
                foreach ($c_extensions as $c_name => $c_usage)
                    $c_extensions_list->text_append(
                              !isset(static::EXTENSIONS_DEFAULT_STATUS[$c_name]) ? $c_name :
                        $c_name.' ('.static::EXTENSIONS_DEFAULT_STATUS[$c_name].')');
                $mod_decorator->data[$c_module_id] = [
                    'module'    => ['value' => new text_simple($c_module_id), 'title' => 'Module'       ],
                    'extension' => ['value' => $c_extensions_list,            'title' => 'PHP extension']
                ];
            }
        }

        # ─────────────────────────────────────────────────────────────────────
        # prepare report by functions
        # ─────────────────────────────────────────────────────────────────────

        $fnc_title = new markup('h2', [], 'PHP functions usage');
        $fnc_decorator = new decorator('table-adaptive');
        $fnc_decorator->id = 'functions_usage';
        $fnc_decorator->result_attributes = ['data-style' => 'compact'];
        foreach ($statistic_by_fnc as $c_function => $c_positions) {
            $fnc_decorator->data[$c_function] = [
                'function' => ['value' => new text_simple($c_function),         'title' => 'Function'       ],
                'usage'    => ['value' => new text_simple(count($c_positions)), 'title' => 'Usage frequency']
            ];
        }

        # ─────────────────────────────────────────────────────────────────────
        # prepare full report
        # ─────────────────────────────────────────────────────────────────────

        $ext_title = new markup('h2', [], 'Full report');
        $ext_decorator = new decorator('table-adaptive');
        $ext_decorator->id = 'extensions_dependency';
        $ext_decorator->result_attributes = ['data-style' => 'compact'];
        foreach ($statistic_by_ext as $c_extension => $c_functions) {
            foreach ($c_functions as $c_function => $c_positions) {
                foreach ($c_positions as $c_position_info) {
                    $ext_decorator->data[] = [
                        'extension' => ['value' => new text_simple($c_extension                   ), 'title' => 'PHP ext.'],
                        'module'    => ['value' => new text_simple($c_position_info->module ?: '—'), 'title' => 'Module'  ],
                        'function'  => ['value' => new text_simple($c_function                    ), 'title' => 'Function'],
                        'file'      => ['value' => new text_simple($c_position_info->file         ), 'title' => 'File'    ],
                        'position'  => ['value' => new text_simple($c_position_info->position     ), 'title' => 'Pos.'    ]
                    ];
                }
            }
        }
        # return result
        return new node([], [
            new markup('p',  [], new text_multiline(['The report was generated in real time.', 'The system can search for the used functions only for enabled PHP modules!'])),
            $mod_title,
            $mod_decorator,
            $mod_legend,
            $fnc_title,
            $fnc_decorator,
            $ext_title,
            $ext_decorator
        ]);
    }

}
