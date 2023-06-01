<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

##########################
### single entry point ###
##########################

define('effcore\\DIR_ROOT',    $web_root.'/');
define('effcore\\DIR_DYNAMIC', $web_root.'/dynamic/');
define('effcore\\DIR_SYSTEM',  $web_root.'/system/');
define('effcore\\DIR_MODULES', $web_root.'/modules/');
define('PHP_INT_32_MAX', 0x7fffffff);

const A0 = "\0";
const NL = "\n";
const CR = "\r";
const TB = "\t";
const BR = "<br>";
const HR = "<hr>";

ini_set('pcre.jit', false);
if (!ini_get('date.timezone')) {
    date_default_timezone_set('UTC');
}

# ─────────────────────────────────────────────────────────────────────
# make the required registrations
# ─────────────────────────────────────────────────────────────────────

require_once('module_core/backend/core.php');
require_once('module_storage/backend/markers.php');
spl_autoload_register('\\effcore\\core::structure_autoload');
if (in_array('container', stream_get_wrappers()) !== true) {
    stream_wrapper_register('container', '\\effcore\\file_container');
}

timer::tap('total');

# ─────────────────────────────────────────────────────────────────────
# prepare user input
# ─────────────────────────────────────────────────────────────────────

$_ORIGINAL_POST    = $_POST;
$_ORIGINAL_GET     = $_GET;
$_ORIGINAL_REQUEST = $_REQUEST;
$_ORIGINAL_FILES   = $_FILES;

$_POST    = request::sanitize('_POST');
$_GET     = request::sanitize('_GET');
$_REQUEST = request::sanitize('_REQUEST');
$_FILES   = request::sanitize('_FILES', true);

# ─────────────────────────────────────────────────────────────────────
# redirect on invalid requests (for example: send the value "http://домен/путь?запрос" over the socket instead of "http://xn--d1acufc/%D0%BF%D1%83%D1%82%D1%8C?%D0%B7%D0%B0%D0%BF%D1%80%D0%BE%D1%81" through the browser)
# ─────────────────────────────────────────────────────────────────────

$raw_url = request::scheme_get().'://'.
           request::host_get(false).
           request::uri_get();
if (core::sanitize_url($raw_url) !== $raw_url || core::validate_url($raw_url, FILTER_FLAG_PATH_REQUIRED) === false || url::get_current()->has_error === true) {
    response::send_header_and_exit('bad_request');
}

# ─────────────────────────────────────────────────────────────────────
# redirect on invalid arguments
# ─────────────────────────────────────────────────────────────────────

if (count($_ORIGINAL_GET)) {
    if (core::hash_get($_GET) !== core::hash_get($_ORIGINAL_GET)) {
        response::send_header_and_exit('redirect', null, null, count($_GET) ?
            request::scheme_get().'://'.request::host_get(false).request::path_get().'?'.http_build_query($_GET, '', '&', PHP_QUERY_RFC3986) :
            request::scheme_get().'://'.request::host_get(false).request::path_get()
        );
    }
}

# ─────────────────────────────────────────────────────────────────────
# redirect to url without leading slash
# ─────────────────────────────────────────────────────────────────────

if (request::uri_get()     !== '/' &&
    request::uri_get()[-1] === '/') {
    $new_url = rtrim(request::uri_get(), '/');
    response::send_header_and_exit('redirect', null, null,
        $new_url === '' ? '/' :
        $new_url
    );
}

# note:
# ════════════════╦════════════════════════════════════════════════════
# url /           ║ is page 'page-front'
# url /page       ║ is page 'page'
# url /file       ║ is page 'file'
# url /file.type  ║ is file 'file.type'
# ────────────────╨────────────────────────────────────────────────────

#######################
### return the FILE ###
#######################

$file = url::get_current()->file_info_get();
if ($file instanceof file && $file->type) {

    $file_types = file::types_get();

    # ─────────────────────────────────────────────────────────────────────
    # case for any system file ('.type', '.name.type'…) - show 'forbidden' even if it does not exist!
    # ─────────────────────────────────────────────────────────────────────

    if ( ($file->name !== '' && $file->name[0] === '.') ||
         ($file->type !== '' && $file->name === '') ) {
        response::send_header_and_exit('access_forbidden', null, new text_multiline([
            'file of this type is protected',
            'go to <a href="/">front page</a>'
        ], [], BR.BR));
    }

    # ─────────────────────────────────────────────────────────────────────
    # case for protected file - show 'forbidden' even if it does not exist!
    # ─────────────────────────────────────────────────────────────────────

    if (isset($file_types[$file->type]->kind) &&
              $file_types[$file->type]->kind === 'protected') {
        response::send_header_and_exit('access_forbidden', null, new text_multiline([
            'file of this type is protected',
            'go to <a href="/">front page</a>'
        ], [], BR.BR));
    }

    # ─────────────────────────────────────────────────────────────────────
    # case for virtual file
    # ─────────────────────────────────────────────────────────────────────

    if (isset($file_types[$file->type]->kind) &&
              $file_types[$file->type]->kind === 'virtual') {
        $type = $file_types[$file->type];
        event::start('on_file_load', 'virtual', ['type_info' => &$type, 'file' => &$file]);
        exit();
    }

    # ─────────────────────────────────────────────────────────────────────
    # protecting files from attacks
    # ─────────────────────────────────────────────────────────────────────

    $type = $file_types[$file->type] ?? (object)['type' => $file->type, 'module_id' => null];
    $real_path = core::validate_realpath($file->path_get());
    if ($real_path === false)               {event::start('on_file_load', 'not_found', ['type_info' => &$type, 'file' => &$file, 'real_path' => $real_path, 'phase' => 1]); exit();} # object does not really exist or object is inaccessible to the web server by rights
    if ($real_path !== $file->path_get())   {event::start('on_file_load', 'not_found', ['type_info' => &$type, 'file' => &$file, 'real_path' => $real_path, 'phase' => 2]); exit();} # resolved path is not the same as the original
    if (strpos($real_path, DIR_ROOT) !== 0) {event::start('on_file_load', 'not_found', ['type_info' => &$type, 'file' => &$file, 'real_path' => $real_path, 'phase' => 3]); exit();} # object is outside the web root
    if (!is_file    ($file->path_get()))    {event::start('on_file_load', 'not_found', ['type_info' => &$type, 'file' => &$file, 'real_path' => $real_path, 'phase' => 4]); exit();} # object exists, but it is not a file
    if (!is_readable($file->path_get())) response::send_header_and_exit('access_forbidden'); # object is inaccessible to the web server by rights

    # ─────────────────────────────────────────────────────────────────────
    # case for dynamic file
    # ─────────────────────────────────────────────────────────────────────

    if (isset($file_types[$file->type]->kind) &&
              $file_types[$file->type]->kind === 'dynamic') {
        $type = $file_types[$file->type];
        event::start('on_file_load', 'dynamic', ['type_info' => &$type, 'file' => &$file]);
        exit();

    # ─────────────────────────────────────────────────────────────────────
    # case for static file
    # ─────────────────────────────────────────────────────────────────────

    } else {
        $type = $file_types[$file->type] ?? (object)['type' => $file->type, 'module_id' => null];
        event::start('on_file_load', 'static', ['type_info' => &$type, 'file' => &$file]);
        exit();
    }

}

#######################
### return the PAGE ###
#######################

if (!storage::get('sql')->is_installed()) {
    if (!preg_match('%^/install(/[a-z]{2}|)$%', url::get_current()->path)) {
        url::go('/install/en');
    }
}

# ─────────────────────────────────────────────────────────────────────
# cron autorun
# ─────────────────────────────────────────────────────────────────────

if (storage::get('sql')->is_installed()) {
    $settings = module::settings_get('core');
    if ($settings->cron_auto_run_frequency) {
        if (!core::is_cron_run($settings->cron_auto_run_frequency)) {
            if (!preg_match('%^/manage/cron/.*$%', url::get_current()->path)) {
                if (core::cron_run_register()) {
                    event::start('on_cron_run');
                }
            }
        }
    }
}

# ─────────────────────────────────────────────────────────────────────
# page search and display
# ─────────────────────────────────────────────────────────────────────

ob_start();
$result = '';
foreach (event::start('on_module_start') as $c_results) {
    foreach ($c_results as $c_result) {
        if ($c_result) {
            $result.= str_replace(NL.NL, '', $c_result);
        }
    }
}
timer::tap('total');
if (console::visible_mode_get()) {
    $result = str_replace('</body>', console::markup_get()->render().'</body>', $result);
}
if (module::is_enabled('test')) {
    header('X-PHP-Memory-usage: '.memory_get_usage(true));
    header('X-Time-total: '.timer::period_get('total', 0, 1));
    header('X-Return-level: system-page');
}
header('Cache-Control: private, no-cache');
header('Content-Length: '.strlen($result));
print $result;
exit();
