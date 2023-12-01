<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use effcore\Captcha;
use effcore\Core;
use effcore\Module;
use effcore\Request;
use effcore\Security;
use effcore\Test_step_Request;
use effcore\User;

abstract class Events_Token {

    protected static $cache = [];

    static function on_apply($name, $args) {

        if ($name === 'test_email_random'    && $args->get_count() === 0) {                                                                                                                                                              return 'test_'.Security::hash_get_mini(random_int(0, PHP_INT_32_MAX)).'@example.com';}
        if ($name === 'test_nickname_random' && $args->get_count() === 0) {                                                                                                                                                              return 'test_'.Security::hash_get_mini(random_int(0, PHP_INT_32_MAX));               }
        if ($name === 'test_password_random' && $args->get_count() === 0) {                                                                                                                                                              return         User::password_generate();                                            }
        if ($name === 'test_email_random'    && $args->get_count() === 1) {if (!isset( static::$cache['test_email_random'   ][Security::hash_get($args->get(0))] )) static::$cache['test_email_random'   ][Security::hash_get($args->get(0))] = 'test_'.Security::hash_get_mini(random_int(0, PHP_INT_32_MAX)).'@example.com'; return static::$cache['test_email_random'   ][Security::hash_get($args->get(0))];}
        if ($name === 'test_nickname_random' && $args->get_count() === 1) {if (!isset( static::$cache['test_nickname_random'][Security::hash_get($args->get(0))] )) static::$cache['test_nickname_random'][Security::hash_get($args->get(0))] = 'test_'.Security::hash_get_mini(random_int(0, PHP_INT_32_MAX));                return static::$cache['test_nickname_random'][Security::hash_get($args->get(0))];}
        if ($name === 'test_password_random' && $args->get_count() === 1) {if (!isset( static::$cache['test_password_random'][Security::hash_get($args->get(0))] )) static::$cache['test_password_random'][Security::hash_get($args->get(0))] =         User::password_generate();                                             return static::$cache['test_password_random'][Security::hash_get($args->get(0))];}

        #################################
        ### response-dependent tokens ###
        #################################

        if ($name === 'test_response_captcha') {
            if (Module::is_enabled('captcha')) {
                $last_response = end(Test_step_Request::$history);
                if ($last_response) {
                    $captcha = Captcha::select_by_id(Core::ip_to_hex($last_response['info']['primary_ip']));
                    return $captcha->characters ?? null;
                }
            }
        }

        if ($name === 'test_response_cookies') {
            $result = [];
            foreach (Test_step_Request::$history as $c_response)
                if (isset($c_response['headers']['set-cookie']))
                    foreach ($c_response['headers']['set-cookie'] as $c_cookie)
                        $result[array_key_first($c_cookie['parsed'])] = reset($c_cookie['parsed']);
            return Core::data_to_attributes($result, false, '; ', '', '');
        }

        if (str_starts_with($name, 'test_response_')) {
            $type = substr($name, strlen('test_response_'));
            $last_response = end(Test_step_Request::$history);
            if ($last_response) {
                if ($type === 'content'         && isset($last_response['data'])                                                                                ) return (string)$last_response['data'];
                if ($type === 'http_code'       && isset($last_response['info'])    && array_key_exists('http_code'                 , $last_response['info']   )) return    (int)$last_response['info']['http_code'];
                if ($type === 'location'        && isset($last_response['headers']) && array_key_exists('location'                  , $last_response['headers'])) return (string)$last_response['headers']['location'];
                if ($type === 'content_length'  && isset($last_response['headers']) && array_key_exists('content-length'            , $last_response['headers'])) return    (int)$last_response['headers']['content-length'];
                if ($type === 'content_range'   && isset($last_response['headers']) && array_key_exists('content-range'             , $last_response['headers'])) return (string)$last_response['headers']['content-range'];
                if ($type === 'submit_error'    && isset($last_response['headers']) && array_key_exists('x-form-submit-errors-count', $last_response['headers'])) return    (int)$last_response['headers']['x-form-submit-errors-count'];
                if ($type === 'time_total'      && isset($last_response['headers']) && array_key_exists('x-time-total'              , $last_response['headers'])) return  (float)$last_response['headers']['x-time-total'];
                if ($type === 'return_level'    && isset($last_response['headers']) && array_key_exists('x-return-level'            , $last_response['headers'])) return (string)$last_response['headers']['x-return-level'];
                if ($type === 'web_server_name') {
                    return $last_response['headers']['x-web-server-name'] ?? Request::web_server_get_info()->name;
                }
                if ($type === 'form_validation_id' && $args->get_count() === 1) {
                    return $last_response['headers']['x-form-validation-id--'.$args->get(0)] ?? '';
                }
            }
        }
    }

}
