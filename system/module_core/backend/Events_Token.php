<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\core;

use effcore\Core;
use effcore\Locale;
use effcore\Request;
use effcore\Security;
use effcore\Token;

abstract class Events_Token {

    static function on_apply($name, $args) {

        if ($name === 'request') {
            $type = $args->get(0);

            if ($type === 'server_name') {
                $result = Request::server_name_get($args->get_named('decode') === 'yes');
                if ($args->get_named('with_specific_port') === 'yes' && Request::port_get() !== '80' && Request::port_get() !== '443') $result = $result.':'.Request::port_get();
                if ($args->get_named('preg_quote'        ) === 'yes'                                                                 ) $result = preg_quote($result);
                return $result;
            }

            if ($type === 'host'    ) {$result = Request::host_get    ($args->get_named('decode') === 'yes'); if ($args->get_named('preg_quote') === 'yes') $result = preg_quote($result); return $result;}
            if ($type === 'hostname') {$result = Request::hostname_get($args->get_named('decode') === 'yes'); if ($args->get_named('preg_quote') === 'yes') $result = preg_quote($result); return $result;}
            if ($type === 'scheme'  ) {$result = Request::scheme_get();                                       if ($args->get_named('preg_quote') === 'yes') $result = preg_quote($result); return $result;}
            if ($type === 'addr'    ) {$result = Request::addr_get();                                         if ($args->get_named('preg_quote') === 'yes') $result = preg_quote($result); return $result;}
            if ($type === 'uri'     ) {$result = Request::URI_get();                                          if ($args->get_named('preg_quote') === 'yes') $result = preg_quote($result); return $result;}
            if ($type === 'path'    ) {$result = Request::path_get();                                         if ($args->get_named('preg_quote') === 'yes') $result = preg_quote($result); return $result;}
            if ($type === 'query'   ) {$result = Request::query_get();                                        if ($args->get_named('preg_quote') === 'yes') $result = preg_quote($result); return $result;}
        }

        switch ($name) {
            case 'current_time_utc'                : return                              Core::    time_get();
            case 'current_date_utc'                : return                              Core::    date_get();
            case 'current_datetime_utc'            : return                              Core::datetime_get();
            case 'current_time_utc_formatted'      : return Locale:: format_utc_time    (Core::    time_get());
            case 'current_date_utc_formatted'      : return Locale:: format_utc_date    (Core::    date_get());
            case 'current_datetime_utc_formatted'  : return Locale:: format_utc_datetime(Core::datetime_get());
            case 'current_time_local'              : return Locale::     time_utc_to_loc(Core::    time_get());
            case 'current_date_local'              : return Locale::     date_utc_to_loc(Core::    date_get());
            case 'current_datetime_local'          : return Locale:: datetime_utc_to_loc(Core::datetime_get());
            case 'current_time_local_formatted'    : return Locale:: format_loc_time    (Core::    time_get());
            case 'current_date_local_formatted'    : return Locale:: format_loc_date    (Core::    date_get());
            case 'current_datetime_local_formatted': return Locale:: format_loc_datetime(Core::datetime_get());
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            case 'return_if_token':
                if ($args->get_count() > 2) {
                    if (!str_contains($args->get(0), '%%')) {
                        if (str_contains($args->get(0), 'return_if')) return '!!! "return_if…" inside "return_if_token" is meaningless !!!';
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
                    if ($args->get_named('filter_css_color') === 'yes') $value = Security::validate_css_color($value) ? $value : null; # examples: "…&value=%23ff0", "…&value=%23a1b2c3", "…&value=LightGoldenrodYellow"
                    if ($args->get_named('filter_css_float') === 'yes') $value = Security::validate_css_float($value) ? $value : null; # examples: "…&value=%2E567", "…&value=1234%2E567", "…&value=1234567"
                    if ($args->get_named('filter_css_units') === 'yes') $value = Security::validate_css_units($value) ? $value : null; # examples: "…&value=%2D1234%2E567px"
                    return $value ?: $default_value;
                }
                break;
        }
    }

}
