<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\core;

use effcore\Core;
use effcore\Request;
use effcore\Token;

abstract class Events_Token {

    static function on_apply($name, $args) {
        switch ($name) {
            case 'request_scheme':
                $result = Request::scheme_get();
                if ($args->get_named('preg_quote') === 'yes') $result = preg_quote($result);
                return $result;
            case 'request_server_name':
                $result = Request::name_get($args->get_named('decode') === 'yes');
                if ($args->get_named('with_specific_port') === 'yes' && Request::port_get() !== '80' && Request::port_get() !== '443') $result = $result.':'.Request::port_get();
                if ($args->get_named('preg_quote') === 'yes') $result = preg_quote($result);
                return $result;
            case 'request_host':
                $result = Request::host_get($args->get_named('decode') === 'yes');
                if ($args->get_named('no_port') === 'yes' && strpos($result, ':')) $result = explode(':', $result)[0];
                if ($args->get_named('preg_quote') === 'yes') $result = preg_quote($result);
                return $result;
            case 'request_addr':
                $result = Request::addr_get();
                if ($args->get_named('preg_quote') === 'yes') $result = preg_quote($result);
                return $result;
            case 'request_uri':
                $result = Request::uri_get();
                if ($args->get_named('preg_quote') === 'yes') $result = preg_quote($result);
                return $result;
            case 'request_path':
                $result = Request::path_get();
                if ($args->get_named('preg_quote') === 'yes') $result = preg_quote($result);
                return $result;
            case 'request_query':
                $result = Request::query_get();
                if ($args->get_named('preg_quote') === 'yes') $result = preg_quote($result);
                return $result;
            case 'return_if_token':
                if ($args->get_count() > 2) {
                    if (strpos($args->get(0), '%%') === false) {
                        if (strpos($args->get(0), 'return_if') !== false) return '!!! "return_if…" inside "return_if_token" is meaningless !!!';
                        if (strlen($args->get(0)) && $args->get_count() === 3) return Token::apply('%%_'.$args->get(0)) === $args->get(1) ? $args->get(2) : null;
                        if (strlen($args->get(0)) && $args->get_count() === 4) return Token::apply('%%_'.$args->get(0)) === $args->get(1) ? $args->get(2) : $args->get(3);
                    }
                }
                break;
            case 'return_if_url_arg':
                if ($args->get_count() === 3) return Request::value_get($args->get(0), 0, '_GET') === $args->get(1) ? $args->get(2) : null;
                if ($args->get_count() === 4) return Request::value_get($args->get(0), 0, '_GET') === $args->get(1) ? $args->get(2) : $args->get(3);
                break;
            case 'return_url_arg':
                if ($args->get_count() > 1) {
                    $arg_name      = $args->get(0);
                    $default_value = $args->get(1);
                    $value         = Request::value_get($arg_name, 0, '_GET');
                    if ($args->get_named('filter_css_color') === 'yes') $value = Core::validate_css_color($value) ? $value : null; # examples: "…&value=%23ff0", "…&value=%23a1b2c3", "…&value=LightGoldenrodYellow"
                    if ($args->get_named('filter_css_float') === 'yes') $value = Core::validate_css_float($value) ? $value : null; # examples: "…&value=%2E567", "…&value=1234%2E567", "…&value=1234567"
                    if ($args->get_named('filter_css_units') === 'yes') $value = Core::validate_css_units($value) ? $value : null; # examples: "…&value=%2D1234%2E567px"
                    return $value ?: $default_value;
                }
                break;
        }
    }

}
