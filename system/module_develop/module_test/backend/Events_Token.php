<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use effcore\Captcha;
use effcore\Core;
use effcore\Module;
use effcore\Request;
use effcore\Test_step_Request;
use effcore\User;

abstract class Events_Token {

    protected static $cache = [];

    static function on_apply($name, $args) {
        if ($name === 'test_software_name') return Request::software_get_info()->name ?? '';
        if ($name === 'test_email_random'    && $args->get_count() === 0) {                                                                                                                                                      return 'test_'.Core::hash_get_mini(random_int(0, PHP_INT_32_MAX)).'@example.com';}
        if ($name === 'test_nickname_random' && $args->get_count() === 0) {                                                                                                                                                      return 'test_'.Core::hash_get_mini(random_int(0, PHP_INT_32_MAX));               }
        if ($name === 'test_password_random' && $args->get_count() === 0) {                                                                                                                                                      return         User::password_generate();                                        }
        if ($name === 'test_email_random'    && $args->get_count() === 1) {if (!isset( static::$cache['test_email_random'   ][Core::hash_get($args->get(0))] )) static::$cache['test_email_random'   ][Core::hash_get($args->get(0))] = 'test_'.Core::hash_get_mini(random_int(0, PHP_INT_32_MAX)).'@example.com'; return static::$cache['test_email_random'   ][Core::hash_get($args->get(0))];}
        if ($name === 'test_nickname_random' && $args->get_count() === 1) {if (!isset( static::$cache['test_nickname_random'][Core::hash_get($args->get(0))] )) static::$cache['test_nickname_random'][Core::hash_get($args->get(0))] = 'test_'.Core::hash_get_mini(random_int(0, PHP_INT_32_MAX));                return static::$cache['test_nickname_random'][Core::hash_get($args->get(0))];}
        if ($name === 'test_password_random' && $args->get_count() === 1) {if (!isset( static::$cache['test_password_random'][Core::hash_get($args->get(0))] )) static::$cache['test_password_random'][Core::hash_get($args->get(0))] =         User::password_generate();                                         return static::$cache['test_password_random'][Core::hash_get($args->get(0))];}
        if ($name === 'test_cookies') {
            $result = [];
            foreach (Test_step_Request::$history as $c_response) {
                if (isset($c_response['headers']['Set-Cookie'])) {
                    foreach ($c_response['headers']['Set-Cookie'] as $c_cookie) {
                        $c_key   = Core::array_key_first($c_cookie['parsed']);
                        $c_value =                 reset($c_cookie['parsed']);
                        $result[$c_key] = $c_value; }}}
            return Core::data_to_attributes($result, false, '; ', '', '');
        }
        if ($name === 'test_captcha') {
            if (Module::is_enabled('captcha')) {
                $last_response = end(Test_step_Request::$history);
                if ($last_response) {
                    $captcha = Captcha::select_by_id(Core::ip_to_hex($last_response['info']['primary_ip']));
                    return $captcha->characters ?? null;
                }
            }
        }
        if ($name === 'test_form_validation_id' && $args->get_count() === 1) {
            $last_response = end(Test_step_Request::$history);
            if ($last_response) {
                return $last_response['headers']['X-Form-Validation-Id--'.$args->get(0)] ?? '';
            }
        }
        if (strpos($name, 'test_response_') === 0) {
            $type = substr($name, strlen('test_response_'));
            $last_response = end(Test_step_Request::$history);
            if ($last_response) {
                if ($type === 'content'        && isset($last_response['data'])                                                                                ) return (string)$last_response['data'];
                if ($type === 'http_code'      && isset($last_response['info'])    && array_key_exists('http_code',                  $last_response['info']   )) return    (int)$last_response['info']['http_code'];
                if ($type === 'location'       && isset($last_response['headers']) && array_key_exists('Location',                   $last_response['headers'])) return (string)$last_response['headers']['Location'];
                if ($type === 'content_length' && isset($last_response['headers']) && array_key_exists('Content-Length',             $last_response['headers'])) return    (int)$last_response['headers']['Content-Length'];
                if ($type === 'content_range'  && isset($last_response['headers']) && array_key_exists('Content-Range',              $last_response['headers'])) return (string)$last_response['headers']['Content-Range'];
                if ($type === 'submit_error'   && isset($last_response['headers']) && array_key_exists('X-Form-Submit-Errors-Count', $last_response['headers'])) return    (int)$last_response['headers']['X-Form-Submit-Errors-Count'];
                if ($type === 'time_total'     && isset($last_response['headers']) && array_key_exists('X-Time-total',               $last_response['headers'])) return  (float)$last_response['headers']['X-Time-total'];
                if ($type === 'return_level'   && isset($last_response['headers']) && array_key_exists('X-Return-level',             $last_response['headers'])) return (string)$last_response['headers']['X-Return-level'];
            }
        }
    }

}
