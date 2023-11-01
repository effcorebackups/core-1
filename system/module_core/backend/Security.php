<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

use DateTime;
use DateTimeZone;

abstract class Security {

    const WHITE_TAGS = 'h1|h2|h3|h4|h5|h6|hr|a|p|address|blockquote|div|code|pre|'.
                       'ul|ol|li|dl|dt|dd|table|caption|col|colgroup|thead|tbody|tfoot|tr|th|td|ruby|rp|rt|'.
                       'br|b|i|u|s|q|em|span|strong|small|sub|sup|del|ins|mark|abbr|cite|dfn|wbr|kbd|var|samp|'.
                       'img|map|area|svg|video|audio|source|track|bdi|bdo';

    # hash performance (5 million iterations):
    # ┌────────────────────────╥─────────────┬────────┬─────────────────────────┐
    # │ function               ║ time (sec.) │ is hex │ has 32-bit sign problem │
    # ╞════════════════════════╬═════════════╪════════╪═════════════════════════╡
    # │ crc32(…)               ║ 0.093       │ no     │ yes                     │
    # │ md5(…)                 ║ 0.320       │ yes    │ no                      │
    # │ sha1(…)                ║ 0.335       │ yes    │ no                      │
    # │ hash('md2', …)         ║ 4.773       │ yes    │ no                      │
    # │ hash('md4', …)         ║ 0.329       │ yes    │ no                      │
    # │ hash('md5', …)         ║ 0.374       │ yes    │ no                      │
    # │ hash('sha1', …)        ║ 0.390       │ yes    │ no                      │
    # │ hash('sha256', …)      ║ 0.671       │ yes    │ no                      │
    # │ hash('sha512/256', …)  ║ 0.852       │ yes    │ no                      │
    # │ hash('sha512', …)      ║ 0.879       │ yes    │ no                      │
    # │ hash('sha3-224', …)    ║ 4.512       │ yes    │ no                      │
    # │ hash('sha3-256', …)    ║ 4.680       │ yes    │ no                      │
    # │ hash('sha3-512', …)    ║ 5.100       │ yes    │ no                      │
    # │ hash('ripemd128', …)   ║ 0.572       │ yes    │ no                      │
    # │ hash('ripemd320', …)   ║ 0.728       │ yes    │ no                      │
    # │ hash('whirlpool', …)   ║ 1.278       │ yes    │ no                      │
    # │ hash('tiger128,3', …)  ║ 0.391       │ yes    │ no                      │
    # │ hash('tiger192,4', …)  ║ 0.443       │ yes    │ no                      │
    # │ hash('snefru', …)      ║ 2.707       │ yes    │ no                      │
    # │ hash('snefru256', …)   ║ 2.716       │ yes    │ no                      │
    # │ hash('gost', …)        ║ 1.970       │ yes    │ no                      │
    # │ hash('gost-crypto', …) ║ 2.153       │ yes    │ no                      │
    # │ hash('adler32', …)     ║ 0.204       │ yes    │ no                      │
    # │ hash('crc32', …)       ║ 0.198       │ yes    │ no                      │
    # │ hash('crc32b', …)      ║ 0.200       │ yes    │ no                      │
    # │ hash('fnv132', …)      ║ 0.195       │ yes    │ no                      │
    # │ hash('fnv1a32', …)     ║ 0.201       │ yes    │ no                      │
    # │ hash('fnv164', …)      ║ 0.203       │ yes    │ no                      │
    # │ hash('fnv1a64', …)     ║ 0.209       │ yes    │ no                      │
    # │ hash('joaat', …)       ║ 0.200       │ yes    │ no                      │
    # │ hash('haval128,3', …)  ║ 0.747       │ yes    │ no                      │
    # │ hash('haval256,5', …)  ║ 1.134       │ yes    │ no                      │
    # └────────────────────────╨─────────────┴────────┴─────────────────────────┘

    static function hash_get($data) {
        if (gettype($data) === 'string')
             return md5($data);
        else return md5(serialize($data));
    }

    static function hash_get_mini($data, $length = 8) {
        return substr(
            static::hash_get($data), 0, $length
        );
    }

    ######################################
    ### functionality for sanitization ###
    ######################################

    static function sanitize_date      ($value) {$result = DateTime::createFromFormat('Y-m-d',         $value, new DateTimeZone('UTC')); return $result instanceof DateTime ? $result->format('Y-m-d'        ) : null;}
    static function sanitize_time      ($value) {$result = DateTime::createFromFormat(      'H:i:s',   $value, new DateTimeZone('UTC')); return $result instanceof DateTime ? $result->format(      'H:i:s'  ) : null;}
    static function sanitize_datetime  ($value) {$result = DateTime::createFromFormat('Y-m-d H:i:s',   $value, new DateTimeZone('UTC')); return $result instanceof DateTime ? $result->format('Y-m-d H:i:s'  ) : null;}
    static function sanitize_T_datetime($value) {$result = DateTime::createFromFormat('Y-m-d\\TH:i:s', $value, new DateTimeZone('UTC')); return $result instanceof DateTime ? $result->format('Y-m-d\\TH:i:s') : null;}

    static function sanitize_id($value, $corrector = '-') {
        return preg_replace('%[^a-z0-9_\\-]%S', $corrector, mb_strtolower($value));
    }

    static function sanitize_url($value) {
        # remove all characters except: ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_,;:.!?+-*/='"`^~(){}[]<>|\$#@%&
        return filter_var($value, FILTER_SANITIZE_URL);
    }

    static function sanitize_file_part($value, $characters_allowed = 'a-zA-Z0-9_\\-\\.', $max_length = 220, $prefix = '') {
        $value = trim($value, '.');
        $value = preg_replace_callback('%(?<char>[^'.$characters_allowed.'])%uS', function ($c_match) use ($prefix) {
            if (       $c_match['char']  === ' ') return '-';
            if (strlen($c_match['char']) ===  1 ) return $prefix.dechex(ord($c_match['char'][0]));
            if (strlen($c_match['char']) ===  2 ) return $prefix.dechex(ord($c_match['char'][0])).$prefix.dechex(ord($c_match['char'][1]));
            if (strlen($c_match['char']) ===  3 ) return $prefix.dechex(ord($c_match['char'][0])).$prefix.dechex(ord($c_match['char'][1])).$prefix.dechex(ord($c_match['char'][2]));
            if (strlen($c_match['char']) ===  4 ) return $prefix.dechex(ord($c_match['char'][0])).$prefix.dechex(ord($c_match['char'][1])).$prefix.dechex(ord($c_match['char'][2])).$prefix.dechex(ord($c_match['char'][3]));
        }, $value);
        return substr($value, 0, $max_length);
    }

    static function sanitize_tags($value, $allowed_tags = self::WHITE_TAGS) {
        $allowed_tags_parsed = Core::array_keys_map(explode('|', $allowed_tags));
        return preg_replace_callback('%'.'[<]'.'(?<closer>[/]|)'.'(?<tag_name>[a-z]{1,1024})'.'(?<attributes>[^>]{0,})'.'(?<end>[>]|)'.'%iS', function ($c_match) use ($allowed_tags, $allowed_tags_parsed) {
            if (strpos($c_match['attributes'], '<') !== false)
                $c_match['attributes'] = static::sanitize_tags($c_match['attributes'], $allowed_tags);
            if (isset($allowed_tags_parsed[$c_match['tag_name']]))
                 return    '<'.$c_match['closer'].$c_match['tag_name'].$c_match['attributes'].($c_match['end'] ? '>'    : '');
            else return '&lt;'.$c_match['closer'].$c_match['tag_name'].$c_match['attributes'].($c_match['end'] ? '&gt;' : '');
        }, $value);
    }

    static function sanitize_js_events($value) {
        return preg_replace_callback('%\b(?<event_name>on[a-z]{1,})(\s{0,})[=]%iS', function ($c_match) {
            return Core::html_entity_encode_total($c_match['event_name']).'=';
        }, $value);
    }

    static function sanitize_js_code($value) {
        $space = '[^a-z]{0,}';
        return preg_replace_callback(
                '%'.'[j]'.$space.'[a]'.$space.'[v]'.$space.'[a]'.$space.'[s]'.$space.
                    '[c]'.$space.'[r]'.$space.'[i]'.$space.'[p]'.$space.'[t]'.$space.'[:]'.'%iS', function ($c_match) {
            return 'noscript:';
        }, $value);
    }

    static function base64_detect_and_decode($value) {
        return preg_replace_callback('%base64(\s*),(\s*)'.
            '(?<code>'.'([a-z0-9+/]{4}){0,}'.
                       '([a-z0-9+/]{3}[=]|'.
                        '[a-z0-9+/]{2}[=][=]){0,1}'.')'.'%iS', function ($c_match) {
            return base64_decode($c_match['code']);
        }, $value);
    }

    static function sanitize_from_XSS($value) {
        $last_result = $value;
        for ($i = 0; $i < 10; $i++) {
            $c_result = rawurldecode($last_result);
            $c_result = Core::html_entity_decode_total($c_result);
            $c_result = static::base64_detect_and_decode($c_result);
            $c_result = static::sanitize_tags($c_result);
            $c_result = static::sanitize_js_events($c_result);
            $c_result = static::sanitize_js_code($c_result);
            if ($c_result === $last_result) return $c_result;
            if ($i >= 5) return 'TOO MUCH ENCODINGS';
            $last_result = $c_result;
        }
    }

    ####################################
    ### functionality for validation ###
    ####################################

    static function validate_date      ($value) {if ($value === null) return false; $result = DateTime::createFromFormat('Y-m-d',         $value, new DateTimeZone('UTC')); return $result instanceof DateTime && strlen($result->format('Y-m-d'        )) === strlen(Field_Date::INPUT_MAX_DATE);}
    static function validate_time      ($value) {if ($value === null) return false; $result = DateTime::createFromFormat(      'H:i:s',   $value, new DateTimeZone('UTC')); return $result instanceof DateTime && strlen($result->format(      'H:i:s'  )) === strlen(Field_Time::INPUT_MAX_TIME);}
    static function validate_datetime  ($value) {if ($value === null) return false; $result = DateTime::createFromFormat('Y-m-d H:i:s',   $value, new DateTimeZone('UTC')); return $result instanceof DateTime && strlen($result->format('Y-m-d H:i:s'  )) === strlen(Field_DateTime::INPUT_MAX_DATETIME);}
    static function validate_T_datetime($value) {if ($value === null) return false; $result = DateTime::createFromFormat('Y-m-d\\TH:i:s', $value, new DateTimeZone('UTC')); return $result instanceof DateTime && strlen($result->format('Y-m-d\\TH:i:s')) === strlen(Field_DateTime::INPUT_MAX_DATETIME);}

    static function validate_id($value) {
        return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '%^['.Field_ID_text::CHARACTERS_ALLOWED.']+$%']]);
    }

    static function validate_property_name($value) {
        return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '%^[a-zA-Z_][a-zA-Z0-9_]*$%']]);
    }

    static function validate_hash($value, $length = 32) {
        return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '%^[a-f0-9]{'.$length.'}$%']]); # 32 - md5 | 40 - sha1 | …
    }

    static function validate_number($value, $with_minus = true) {
        return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' =>
            $with_minus ? '%^(?<int___1>'.             '[0]'                        .')$|'.
                           '^(?<int___2>'.      '[1-9][0-9]*'                       .')$|'.
                           '^(?<int___3>'.'[-]'.'[1-9][0-9]*'                       .')$|'.
                           '^(?<float_1>'.           '[0-9]' .'[.]'.    '[0-9]+'    .')$|'.
                           '^(?<float_2>'.'[-]'.     '[0-9]' .'[.]'.'[0]*[1-9]+[0]*'.')$|'.
                           '^(?<float_3>'.'[-]'.'[1-9]'      .'[.]'.    '[0-9]+'    .')$|'.
                           '^(?<float_4>'.      '[1-9][0-9]+'.'[.]'.    '[0-9]+'    .')$|'.
                           '^(?<float_5>'.'[-]'.'[1-9][0-9]+'.'[.]'.    '[0-9]+'    .')$%S' :
                    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
                          '%^(?<int___1>'.             '[0]'                        .')$|'.
                           '^(?<int___2>'.      '[1-9][0-9]*'                       .')$|'.
                           '^(?<float_1>'.           '[0-9]' .'[.]'.    '[0-9]+'    .')$|'.
                           '^(?<float_4>'.      '[1-9][0-9]+'.'[.]'.    '[0-9]+'    .')$%S'
        ]]);
    }

    static function validate_int($value, $with_minus = true) {
        return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' =>
            $with_minus ? '%^(?<int___1>'.             '[0]' .')$|'.
                           '^(?<int___2>'.      '[1-9][0-9]*'.')$|'.
                           '^(?<int___3>'.'[-]'.'[1-9][0-9]*'.')$%S' :
                    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
                          '%^(?<int___1>'.             '[0]' .')$|'.
                           '^(?<int___2>'.      '[1-9][0-9]*'.')$%S'
        ]]);
    }

    static function validate_range($min, $max, $step, $value) {
        if (bccomp(           $value, $min, 20) /* $value  <  $min */ ===  -1) return false;
        if (bccomp(           $value, $max, 20) /* $value  >  $max */ ===  +1) return false;
        if (bccomp(           $value, $min, 20) /* $value === $min */ ===   0) return true;
        if (bccomp(           $value, $max, 20) /* $value === $max */ ===   0) return true;
        if (rtrim(bcdiv(bcsub($value, $min, 20), $step, 20), '0')[-1] === '.') return true;
        return false;
    }

    static function validate_hex_color($value) {
        return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' =>
            '%^#(?<R>[a-f0-9]{2})'.
               '(?<G>[a-f0-9]{2})'.
               '(?<B>[a-f0-9]{2})$%'
        ]]);
    }

    static function validate_ip_v4($value) {
        return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    static function validate_ip_v6($value) {
        return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    static function validate_mime_type($value) {
        return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '%^[a-z]{1,20}/[a-zA-Z0-9\\+\\-\\.]{1,100}$%']]);
    }

    static function validate_email($value) {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    static function validate_nickname($value) {
        return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '%^['.Field_Nickname::CHARACTERS_ALLOWED.']{4,32}$%']]);
    }

    static function validate_tel($value) {
        return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '%^[+][0-9]{1,14}$%']]);
    }

    static function validate_url($value, $flags = 0) {
        return filter_var($value, FILTER_VALIDATE_URL, $flags);
    }

    static function validate_realpath($value) {
        $value = realpath($value);
        if ($value !== false && Core::is_Win())
            $value = str_replace('\\', '/', $value);
        return $value;
    }

    static function validate_css_color($value) {
        return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '%^\\#[a-fA-F0-9]{3}$|^\\#[a-fA-F0-9]{6}$|^[a-zA-Z]{3,20}$%S']]); # examples: "#ff0", "#a1b2c3", "Red", "LightGoldenrodYellow"
    }

    static function validate_css_float($value) {
        return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '%^[0-9]{0,4}[\\.]{0,1}[0-9]{1,3}$%S']]); # examples: ".567", "1234.567", "1234567" | fake values: ".", "123.", "1.2.3", "12..3"
    }

    # valid values:
    # ┌────────────┬─────────────┐
    # │ 1234.567   │ -1234.567   │
    # │ 1234 567   │ -1234 567   │
    # │     .567   │ -    .567   │
    # │      567   │ -     567   │
    # ├────────────┼─────────────┤
    # │ 1234.567px │ -1234.567px │
    # │ 1234 567px │ -1234 567px │
    # │     .567px │ -    .567px │
    # │      567px │ -     567px │
    # └────────────┴─────────────┘

    static function validate_css_units($value) {
        return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' =>
            '%^(?<sign>[\\-]{0,1})'. # examples: "-1px", "1234.567em", ".5%", "-.567px" | fake values: "-", ".", "px", "123.", "123-", "12-3", "12--3", "12..3", "1-2-3", "1.2.3"
              '(?<value>[0-9]{0,4}[\\.]{0,1}[0-9]{1,3})'.
              '(?<dimension>px|em|\\%|)$%S']]);
    }

}
