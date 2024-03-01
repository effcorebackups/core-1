<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use const effcore\DIR_ROOT;
use effcore\Core;
use effcore\Request;
use effcore\Test;
use effcore\Text;
use effcore\URL;

abstract class Events_Test__Class_URL {

    static function test_step_code__construct(&$test, $dpath) {
        $protocol = Request::scheme_get();
        $domain = Request::host_get();
        $data = [
                                   '/'                                         => ['protocol' => $protocol, 'domain' => $domain              , 'path' => '/'                   , 'query' => ''             , 'anchor' => ''      ],
                                   '/?key=value'                               => ['protocol' => $protocol, 'domain' => $domain              , 'path' => '/'                   , 'query' => 'key=value'    , 'anchor' => ''      ],
                                   '/#anchor'                                  => ['protocol' => $protocol, 'domain' => $domain              , 'path' => '/'                   , 'query' => ''             , 'anchor' => 'anchor'],
                                   '/?key=value#anchor'                        => ['protocol' => $protocol, 'domain' => $domain              , 'path' => '/'                   , 'query' => 'key=value'    , 'anchor' => 'anchor'],
                                   '/dir/subdir/page'                          => ['protocol' => $protocol, 'domain' => $domain              , 'path' => '/dir/subdir/page'    , 'query' => ''             , 'anchor' => ''      ],
                                   '/dir/subdir/page?key=value'                => ['protocol' => $protocol, 'domain' => $domain              , 'path' => '/dir/subdir/page'    , 'query' => 'key=value'    , 'anchor' => ''      ],
                                   '/dir/subdir/page#anchor'                   => ['protocol' => $protocol, 'domain' => $domain              , 'path' => '/dir/subdir/page'    , 'query' => ''             , 'anchor' => 'anchor'],
                                   '/dir/subdir/page?key=value#anchor'         => ['protocol' => $protocol, 'domain' => $domain              , 'path' => '/dir/subdir/page'    , 'query' => 'key=value'    , 'anchor' => 'anchor'],
                   'subdomain.domain'                                          => ['protocol' => 'http'   , 'domain' => 'subdomain.domain'   , 'path' => '/'                   , 'query' => ''             , 'anchor' => ''      ],
                   'subdomain.domain/'                                         => ['protocol' => 'http'   , 'domain' => 'subdomain.domain'   , 'path' => '/'                   , 'query' => ''             , 'anchor' => ''      ],
                   'subdomain.domain/?key=value'                               => ['protocol' => 'http'   , 'domain' => 'subdomain.domain'   , 'path' => '/'                   , 'query' => 'key=value'    , 'anchor' => ''      ],
                   'subdomain.domain/#anchor'                                  => ['protocol' => 'http'   , 'domain' => 'subdomain.domain'   , 'path' => '/'                   , 'query' => ''             , 'anchor' => 'anchor'],
                   'subdomain.domain/?key=value#anchor'                        => ['protocol' => 'http'   , 'domain' => 'subdomain.domain'   , 'path' => '/'                   , 'query' => 'key=value'    , 'anchor' => 'anchor'],
                   'subdomain.domain/dir/subdir/page'                          => ['protocol' => 'http'   , 'domain' => 'subdomain.domain'   , 'path' => '/dir/subdir/page'    , 'query' => ''             , 'anchor' => ''      ],
                   'subdomain.domain/dir/subdir/page?key=value'                => ['protocol' => 'http'   , 'domain' => 'subdomain.domain'   , 'path' => '/dir/subdir/page'    , 'query' => 'key=value'    , 'anchor' => ''      ],
                   'subdomain.domain/dir/subdir/page#anchor'                   => ['protocol' => 'http'   , 'domain' => 'subdomain.domain'   , 'path' => '/dir/subdir/page'    , 'query' => ''             , 'anchor' => 'anchor'],
                   'subdomain.domain/dir/subdir/page?key=value#anchor'         => ['protocol' => 'http'   , 'domain' => 'subdomain.domain'   , 'path' => '/dir/subdir/page'    , 'query' => 'key=value'    , 'anchor' => 'anchor'],
                   'subdomain.domain:80'                                       => ['protocol' => 'http'   , 'domain' => 'subdomain.domain:80', 'path' => '/'                   , 'query' => ''             , 'anchor' => ''      ],
                   'subdomain.domain:80/'                                      => ['protocol' => 'http'   , 'domain' => 'subdomain.domain:80', 'path' => '/'                   , 'query' => ''             , 'anchor' => ''      ],
                   'subdomain.domain:80/?key=value'                            => ['protocol' => 'http'   , 'domain' => 'subdomain.domain:80', 'path' => '/'                   , 'query' => 'key=value'    , 'anchor' => ''      ],
                   'subdomain.domain:80/#anchor'                               => ['protocol' => 'http'   , 'domain' => 'subdomain.domain:80', 'path' => '/'                   , 'query' => ''             , 'anchor' => 'anchor'],
                   'subdomain.domain:80/?key=value#anchor'                     => ['protocol' => 'http'   , 'domain' => 'subdomain.domain:80', 'path' => '/'                   , 'query' => 'key=value'    , 'anchor' => 'anchor'],
                   'subdomain.domain:80/dir/subdir/page'                       => ['protocol' => 'http'   , 'domain' => 'subdomain.domain:80', 'path' => '/dir/subdir/page'    , 'query' => ''             , 'anchor' => ''      ],
                   'subdomain.domain:80/dir/subdir/page?key=value'             => ['protocol' => 'http'   , 'domain' => 'subdomain.domain:80', 'path' => '/dir/subdir/page'    , 'query' => 'key=value'    , 'anchor' => ''      ],
                   'subdomain.domain:80/dir/subdir/page#anchor'                => ['protocol' => 'http'   , 'domain' => 'subdomain.domain:80', 'path' => '/dir/subdir/page'    , 'query' => ''             , 'anchor' => 'anchor'],
                   'subdomain.domain:80/dir/subdir/page?key=value#anchor'      => ['protocol' => 'http'   , 'domain' => 'subdomain.domain:80', 'path' => '/dir/subdir/page'    , 'query' => 'key=value'    , 'anchor' => 'anchor'],
            'http://subdomain.domain'                                          => ['protocol' => 'http'   , 'domain' => 'subdomain.domain'   , 'path' => '/'                   , 'query' => ''             , 'anchor' => ''      ],
            'http://subdomain.domain/'                                         => ['protocol' => 'http'   , 'domain' => 'subdomain.domain'   , 'path' => '/'                   , 'query' => ''             , 'anchor' => ''      ],
            'http://subdomain.domain/?key=value'                               => ['protocol' => 'http'   , 'domain' => 'subdomain.domain'   , 'path' => '/'                   , 'query' => 'key=value'    , 'anchor' => ''      ],
            'http://subdomain.domain/#anchor'                                  => ['protocol' => 'http'   , 'domain' => 'subdomain.domain'   , 'path' => '/'                   , 'query' => ''             , 'anchor' => 'anchor'],
            'http://subdomain.domain/?key=value#anchor'                        => ['protocol' => 'http'   , 'domain' => 'subdomain.domain'   , 'path' => '/'                   , 'query' => 'key=value'    , 'anchor' => 'anchor'],
            'http://subdomain.domain/dir/subdir/page'                          => ['protocol' => 'http'   , 'domain' => 'subdomain.domain'   , 'path' => '/dir/subdir/page'    , 'query' => ''             , 'anchor' => ''      ],
            'http://subdomain.domain/dir/subdir/page?key=value'                => ['protocol' => 'http'   , 'domain' => 'subdomain.domain'   , 'path' => '/dir/subdir/page'    , 'query' => 'key=value'    , 'anchor' => ''      ],
            'http://subdomain.domain/dir/subdir/page#anchor'                   => ['protocol' => 'http'   , 'domain' => 'subdomain.domain'   , 'path' => '/dir/subdir/page'    , 'query' => ''             , 'anchor' => 'anchor'],
            'http://subdomain.domain/dir/subdir/page?key=value#anchor'         => ['protocol' => 'http'   , 'domain' => 'subdomain.domain'   , 'path' => '/dir/subdir/page'    , 'query' => 'key=value'    , 'anchor' => 'anchor'],
            'http://subdomain.domain:80'                                       => ['protocol' => 'http'   , 'domain' => 'subdomain.domain:80', 'path' => '/'                   , 'query' => ''             , 'anchor' => ''      ],
            'http://subdomain.domain:80/'                                      => ['protocol' => 'http'   , 'domain' => 'subdomain.domain:80', 'path' => '/'                   , 'query' => ''             , 'anchor' => ''      ],
            'http://subdomain.domain:80/?key=value'                            => ['protocol' => 'http'   , 'domain' => 'subdomain.domain:80', 'path' => '/'                   , 'query' => 'key=value'    , 'anchor' => ''      ],
            'http://subdomain.domain:80/#anchor'                               => ['protocol' => 'http'   , 'domain' => 'subdomain.domain:80', 'path' => '/'                   , 'query' => ''             , 'anchor' => 'anchor'],
            'http://subdomain.domain:80/?key=value#anchor'                     => ['protocol' => 'http'   , 'domain' => 'subdomain.domain:80', 'path' => '/'                   , 'query' => 'key=value'    , 'anchor' => 'anchor'],
            'http://subdomain.domain:80/dir/subdir/page'                       => ['protocol' => 'http'   , 'domain' => 'subdomain.domain:80', 'path' => '/dir/subdir/page'    , 'query' => ''             , 'anchor' => ''      ],
            'http://subdomain.domain:80/dir/subdir/page?key=value'             => ['protocol' => 'http'   , 'domain' => 'subdomain.domain:80', 'path' => '/dir/subdir/page'    , 'query' => 'key=value'    , 'anchor' => ''      ],
            'http://subdomain.domain:80/dir/subdir/page#anchor'                => ['protocol' => 'http'   , 'domain' => 'subdomain.domain:80', 'path' => '/dir/subdir/page'    , 'query' => ''             , 'anchor' => 'anchor'],
            'http://subdomain.domain:80/dir/subdir/page?key=value#anchor'      => ['protocol' => 'http'   , 'domain' => 'subdomain.domain:80', 'path' => '/dir/subdir/page'    , 'query' => 'key=value'    , 'anchor' => 'anchor'],
                                 '/?ключ=значение'                             => ['protocol' => $protocol, 'domain' => $domain              , 'path' => '/'                   , 'query' => 'ключ=значение', 'anchor' => ''      ],
                                 '/#якорь'                                     => ['protocol' => $protocol, 'domain' => $domain              , 'path' => '/'                   , 'query' => ''             , 'anchor' => 'якорь' ],
                                 '/?ключ=значение#якорь'                       => ['protocol' => $protocol, 'domain' => $domain              , 'path' => '/'                   , 'query' => 'ключ=значение', 'anchor' => 'якорь' ],
                                 '/дир/субдир/страница'                        => ['protocol' => $protocol, 'domain' => $domain              , 'path' => '/дир/субдир/страница', 'query' => ''             , 'anchor' => ''      ],
                                 '/дир/субдир/страница?ключ=значение'          => ['protocol' => $protocol, 'domain' => $domain              , 'path' => '/дир/субдир/страница', 'query' => 'ключ=значение', 'anchor' => ''      ],
                                 '/дир/субдир/страница#якорь'                  => ['protocol' => $protocol, 'domain' => $domain              , 'path' => '/дир/субдир/страница', 'query' => ''             , 'anchor' => 'якорь' ],
                                 '/дир/субдир/страница?ключ=значение#якорь'    => ['protocol' => $protocol, 'domain' => $domain              , 'path' => '/дир/субдир/страница', 'query' => 'ключ=значение', 'anchor' => 'якорь' ],
                   'субдомен.домен'                                            => ['protocol' => 'http'   , 'domain' => 'субдомен.домен'     , 'path' => '/'                   , 'query' => ''             , 'anchor' => ''      ],
                   'субдомен.домен/'                                           => ['protocol' => 'http'   , 'domain' => 'субдомен.домен'     , 'path' => '/'                   , 'query' => ''             , 'anchor' => ''      ],
                   'субдомен.домен/?ключ=значение'                             => ['protocol' => 'http'   , 'domain' => 'субдомен.домен'     , 'path' => '/'                   , 'query' => 'ключ=значение', 'anchor' => ''      ],
                   'субдомен.домен/#якорь'                                     => ['protocol' => 'http'   , 'domain' => 'субдомен.домен'     , 'path' => '/'                   , 'query' => ''             , 'anchor' => 'якорь' ],
                   'субдомен.домен/?ключ=значение#якорь'                       => ['protocol' => 'http'   , 'domain' => 'субдомен.домен'     , 'path' => '/'                   , 'query' => 'ключ=значение', 'anchor' => 'якорь' ],
                   'субдомен.домен/дир/субдир/страница'                        => ['protocol' => 'http'   , 'domain' => 'субдомен.домен'     , 'path' => '/дир/субдир/страница', 'query' => ''             , 'anchor' => ''      ],
                   'субдомен.домен/дир/субдир/страница?ключ=значение'          => ['protocol' => 'http'   , 'domain' => 'субдомен.домен'     , 'path' => '/дир/субдир/страница', 'query' => 'ключ=значение', 'anchor' => ''      ],
                   'субдомен.домен/дир/субдир/страница#якорь'                  => ['protocol' => 'http'   , 'domain' => 'субдомен.домен'     , 'path' => '/дир/субдир/страница', 'query' => ''             , 'anchor' => 'якорь' ],
                   'субдомен.домен/дир/субдир/страница?ключ=значение#якорь'    => ['protocol' => 'http'   , 'domain' => 'субдомен.домен'     , 'path' => '/дир/субдир/страница', 'query' => 'ключ=значение', 'anchor' => 'якорь' ],
                   'субдомен.домен:80'                                         => ['protocol' => 'http'   , 'domain' => 'субдомен.домен:80'  , 'path' => '/'                   , 'query' => ''             , 'anchor' => ''      ],
                   'субдомен.домен:80/'                                        => ['protocol' => 'http'   , 'domain' => 'субдомен.домен:80'  , 'path' => '/'                   , 'query' => ''             , 'anchor' => ''      ],
                   'субдомен.домен:80/?ключ=значение'                          => ['protocol' => 'http'   , 'domain' => 'субдомен.домен:80'  , 'path' => '/'                   , 'query' => 'ключ=значение', 'anchor' => ''      ],
                   'субдомен.домен:80/#якорь'                                  => ['protocol' => 'http'   , 'domain' => 'субдомен.домен:80'  , 'path' => '/'                   , 'query' => ''             , 'anchor' => 'якорь' ],
                   'субдомен.домен:80/?ключ=значение#якорь'                    => ['protocol' => 'http'   , 'domain' => 'субдомен.домен:80'  , 'path' => '/'                   , 'query' => 'ключ=значение', 'anchor' => 'якорь' ],
                   'субдомен.домен:80/дир/субдир/страница'                     => ['protocol' => 'http'   , 'domain' => 'субдомен.домен:80'  , 'path' => '/дир/субдир/страница', 'query' => ''             , 'anchor' => ''      ],
                   'субдомен.домен:80/дир/субдир/страница?ключ=значение'       => ['protocol' => 'http'   , 'domain' => 'субдомен.домен:80'  , 'path' => '/дир/субдир/страница', 'query' => 'ключ=значение', 'anchor' => ''      ],
                   'субдомен.домен:80/дир/субдир/страница#якорь'               => ['protocol' => 'http'   , 'domain' => 'субдомен.домен:80'  , 'path' => '/дир/субдир/страница', 'query' => ''             , 'anchor' => 'якорь' ],
                   'субдомен.домен:80/дир/субдир/страница?ключ=значение#якорь' => ['protocol' => 'http'   , 'domain' => 'субдомен.домен:80'  , 'path' => '/дир/субдир/страница', 'query' => 'ключ=значение', 'anchor' => 'якорь' ],
            'http://субдомен.домен'                                            => ['protocol' => 'http'   , 'domain' => 'субдомен.домен'     , 'path' => '/'                   , 'query' => ''             , 'anchor' => ''      ],
            'http://субдомен.домен/'                                           => ['protocol' => 'http'   , 'domain' => 'субдомен.домен'     , 'path' => '/'                   , 'query' => ''             , 'anchor' => ''      ],
            'http://субдомен.домен/?ключ=значение'                             => ['protocol' => 'http'   , 'domain' => 'субдомен.домен'     , 'path' => '/'                   , 'query' => 'ключ=значение', 'anchor' => ''      ],
            'http://субдомен.домен/#якорь'                                     => ['protocol' => 'http'   , 'domain' => 'субдомен.домен'     , 'path' => '/'                   , 'query' => ''             , 'anchor' => 'якорь' ],
            'http://субдомен.домен/?ключ=значение#якорь'                       => ['protocol' => 'http'   , 'domain' => 'субдомен.домен'     , 'path' => '/'                   , 'query' => 'ключ=значение', 'anchor' => 'якорь' ],
            'http://субдомен.домен/дир/субдир/страница'                        => ['protocol' => 'http'   , 'domain' => 'субдомен.домен'     , 'path' => '/дир/субдир/страница', 'query' => ''             , 'anchor' => ''      ],
            'http://субдомен.домен/дир/субдир/страница?ключ=значение'          => ['protocol' => 'http'   , 'domain' => 'субдомен.домен'     , 'path' => '/дир/субдир/страница', 'query' => 'ключ=значение', 'anchor' => ''      ],
            'http://субдомен.домен/дир/субдир/страница#якорь'                  => ['protocol' => 'http'   , 'domain' => 'субдомен.домен'     , 'path' => '/дир/субдир/страница', 'query' => ''             , 'anchor' => 'якорь' ],
            'http://субдомен.домен/дир/субдир/страница?ключ=значение#якорь'    => ['protocol' => 'http'   , 'domain' => 'субдомен.домен'     , 'path' => '/дир/субдир/страница', 'query' => 'ключ=значение', 'anchor' => 'якорь' ],
            'http://субдомен.домен:80'                                         => ['protocol' => 'http'   , 'domain' => 'субдомен.домен:80'  , 'path' => '/'                   , 'query' => ''             , 'anchor' => ''      ],
            'http://субдомен.домен:80/'                                        => ['protocol' => 'http'   , 'domain' => 'субдомен.домен:80'  , 'path' => '/'                   , 'query' => ''             , 'anchor' => ''      ],
            'http://субдомен.домен:80/?ключ=значение'                          => ['protocol' => 'http'   , 'domain' => 'субдомен.домен:80'  , 'path' => '/'                   , 'query' => 'ключ=значение', 'anchor' => ''      ],
            'http://субдомен.домен:80/#якорь'                                  => ['protocol' => 'http'   , 'domain' => 'субдомен.домен:80'  , 'path' => '/'                   , 'query' => ''             , 'anchor' => 'якорь' ],
            'http://субдомен.домен:80/?ключ=значение#якорь'                    => ['protocol' => 'http'   , 'domain' => 'субдомен.домен:80'  , 'path' => '/'                   , 'query' => 'ключ=значение', 'anchor' => 'якорь' ],
            'http://субдомен.домен:80/дир/субдир/страница'                     => ['protocol' => 'http'   , 'domain' => 'субдомен.домен:80'  , 'path' => '/дир/субдир/страница', 'query' => ''             , 'anchor' => ''      ],
            'http://субдомен.домен:80/дир/субдир/страница?ключ=значение'       => ['protocol' => 'http'   , 'domain' => 'субдомен.домен:80'  , 'path' => '/дир/субдир/страница', 'query' => 'ключ=значение', 'anchor' => ''      ],
            'http://субдомен.домен:80/дир/субдир/страница#якорь'               => ['protocol' => 'http'   , 'domain' => 'субдомен.домен:80'  , 'path' => '/дир/субдир/страница', 'query' => ''             , 'anchor' => 'якорь' ],
            'http://субдомен.домен:80/дир/субдир/страница?ключ=значение#якорь' => ['protocol' => 'http'   , 'domain' => 'субдомен.домен:80'  , 'path' => '/дир/субдир/страница', 'query' => 'ключ=значение', 'anchor' => 'якорь' ],
        ];

        foreach ($data as $c_value => $c_expected) {
            $c_url = new URL($c_value);
            $c_result = $c_url->protocol === $c_expected['protocol'] &&
                        $c_url->domain   === $c_expected['domain']   &&
                        $c_url->path     === $c_expected['path']     &&
                        $c_url->query    === $c_expected['query']    &&
                        $c_url->anchor   === $c_expected['anchor'];
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($c_url)]);
                yield Test::FAILED;
            }
        }
    }

    static function test_step_code__has_error(&$test, $dpath) {
        $data = [
            ':',
            ':/',
            'http:',
            'http:/',
            'http:///',
            'http:///path/',
            'http:/domain/path?key=value',
            'javascript://%0Aalert(document.cookie)'
        ];

        foreach ($data as $c_value) {
            $c_url = new URL($c_value);
            $c_expected = true;
            $с_received = $c_url->has_error;
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($c_url->has_error)]);
                yield Test::FAILED;
            }
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $expected = [
            '://:80?key=value' => true,
            '://:80?key=value#anchor' => true,
            '://:80' => true,
            '://:80/dir/subdir/page?key=value' => true,
            '://:80/dir/subdir/page?key=value#anchor' => true,
            '://:80/dir/subdir/page' => true,
            '://:80/dir/subdir/page#anchor' => true,
            '://:80#anchor' => true,
            '://?key=value' => true,
            '://?key=value#anchor' => true,
            '://' => true,
            ':///dir/subdir/page?key=value' => true,
            ':///dir/subdir/page?key=value#anchor' => true,
            ':///dir/subdir/page' => true,
            ':///dir/subdir/page#anchor' => true,
            '://#anchor' => true,
            '://subdomain.domain:80?key=value' => true,
            '://subdomain.domain:80?key=value#anchor' => true,
            '://subdomain.domain:80' => true,
            '://subdomain.domain:80/dir/subdir/page?key=value' => true,
            '://subdomain.domain:80/dir/subdir/page?key=value#anchor' => true,
            '://subdomain.domain:80/dir/subdir/page' => true,
            '://subdomain.domain:80/dir/subdir/page#anchor' => true,
            '://subdomain.domain:80#anchor' => true,
            '://subdomain.domain?key=value' => true,
            '://subdomain.domain?key=value#anchor' => true,
            '://subdomain.domain' => true,
            '://subdomain.domain/dir/subdir/page?key=value' => true,
            '://subdomain.domain/dir/subdir/page?key=value#anchor' => true,
            '://subdomain.domain/dir/subdir/page' => true,
            '://subdomain.domain/dir/subdir/page#anchor' => true,
            '://subdomain.domain#anchor' => true,
            ':80?key=value' => true,
            ':80?key=value#anchor' => true,
            ':80' => true,
            ':80/dir/subdir/page?key=value' => true,
            ':80/dir/subdir/page?key=value#anchor' => true,
            ':80/dir/subdir/page' => true,
            ':80/dir/subdir/page#anchor' => true,
            ':80#anchor' => true,
            '?key=value' => true,
            '?key=value#anchor' => true,
            '' => true,
            '#anchor' => true,
            'http://:80?key=value' => true,
            'http://:80?key=value#anchor' => true,
            'http://:80' => true,
            'http://:80/dir/subdir/page?key=value' => true,
            'http://:80/dir/subdir/page?key=value#anchor' => true,
            'http://:80/dir/subdir/page' => true,
            'http://:80/dir/subdir/page#anchor' => true,
            'http://:80#anchor' => true,
            'http://?key=value' => true,
            'http://?key=value#anchor' => true,
            'http://' => true,
            'http:///dir/subdir/page?key=value' => true,
            'http:///dir/subdir/page?key=value#anchor' => true,
            'http:///dir/subdir/page' => true,
            'http:///dir/subdir/page#anchor' => true,
            'http://#anchor' => true,
            'http://subdomain.domain:80?key=value' => true,
            'http://subdomain.domain:80?key=value#anchor' => true,
            'http://subdomain.domain:80#anchor' => true,
            'http://subdomain.domain?key=value' => true,
            'http://subdomain.domain?key=value#anchor' => true,
            'http://subdomain.domain#anchor' => true,
            'http:80?key=value' => true,
            'http:80?key=value#anchor' => true,
            'http:80#anchor' => true,
            'http?key=value' => true,
            'http?key=value#anchor' => true,
            'http#anchor' => true,
            'httpsubdomain.domain:80?key=value' => true,
            'httpsubdomain.domain:80?key=value#anchor' => true,
            'httpsubdomain.domain:80#anchor' => true,
            'httpsubdomain.domain?key=value' => true,
            'httpsubdomain.domain?key=value#anchor' => true,
            'httpsubdomain.domain#anchor' => true,
            'subdomain.domain:80?key=value' => true,
            'subdomain.domain:80?key=value#anchor' => true,
            'subdomain.domain:80#anchor' => true,
            'subdomain.domain?key=value' => true,
            'subdomain.domain?key=value#anchor' => true,
            'subdomain.domain#anchor' => true,
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            '/dir/subdir/page?key=value' => false,
            '/dir/subdir/page?key=value#anchor' => false,
            '/dir/subdir/page' => false,
            '/dir/subdir/page#anchor' => false,
            'http://subdomain.domain:80' => false,
            'http://subdomain.domain:80/dir/subdir/page?key=value' => false,
            'http://subdomain.domain:80/dir/subdir/page?key=value#anchor' => false,
            'http://subdomain.domain:80/dir/subdir/page' => false,
            'http://subdomain.domain:80/dir/subdir/page#anchor' => false,
            'http://subdomain.domain' => false,
            'http://subdomain.domain/dir/subdir/page?key=value' => false,
            'http://subdomain.domain/dir/subdir/page?key=value#anchor' => false,
            'http://subdomain.domain/dir/subdir/page' => false,
            'http://subdomain.domain/dir/subdir/page#anchor' => false,
            'http:80' => false,
            'http:80/dir/subdir/page?key=value' => false,
            'http:80/dir/subdir/page?key=value#anchor' => false,
            'http:80/dir/subdir/page' => false,
            'http:80/dir/subdir/page#anchor' => false,
            'http' => false,
            'http/dir/subdir/page?key=value' => false,
            'http/dir/subdir/page?key=value#anchor' => false,
            'http/dir/subdir/page' => false,
            'http/dir/subdir/page#anchor' => false,
            'httpsubdomain.domain:80' => false,
            'httpsubdomain.domain:80/dir/subdir/page?key=value' => false,
            'httpsubdomain.domain:80/dir/subdir/page?key=value#anchor' => false,
            'httpsubdomain.domain:80/dir/subdir/page' => false,
            'httpsubdomain.domain:80/dir/subdir/page#anchor' => false,
            'httpsubdomain.domain' => false,
            'httpsubdomain.domain/dir/subdir/page?key=value' => false,
            'httpsubdomain.domain/dir/subdir/page?key=value#anchor' => false,
            'httpsubdomain.domain/dir/subdir/page' => false,
            'httpsubdomain.domain/dir/subdir/page#anchor' => false,
            'subdomain.domain:80' => false,
            'subdomain.domain:80/dir/subdir/page?key=value' => false,
            'subdomain.domain:80/dir/subdir/page?key=value#anchor' => false,
            'subdomain.domain:80/dir/subdir/page' => false,
            'subdomain.domain:80/dir/subdir/page#anchor' => false,
            'subdomain.domain' => false,
            'subdomain.domain/dir/subdir/page?key=value' => false,
            'subdomain.domain/dir/subdir/page?key=value#anchor' => false,
            'subdomain.domain/dir/subdir/page' => false,
            'subdomain.domain/dir/subdir/page#anchor' => false
        ];

        $parts = [
            1 => 'http',
            2 => '://',
            3 => 'subdomain.domain',
            4 => ':80',
            5 => '/dir/subdir/page',
            6 => '?key=value',
            7 => '#anchor'
        ];

        for ($i = 0b0000000; $i <= 0b1111111; $i++) {
            $c_value = '';
            if ($i & 0b0000001) $c_value.= $parts[1];
            if ($i & 0b0000010) $c_value.= $parts[2];
            if ($i & 0b0000100) $c_value.= $parts[3];
            if ($i & 0b0001000) $c_value.= $parts[4];
            if ($i & 0b0010000) $c_value.= $parts[5];
            if ($i & 0b0100000) $c_value.= $parts[6];
            if ($i & 0b1000000) $c_value.= $parts[7];
            $c_url = new URL($c_value);
            $c_expected = $expected[$c_value];
            $с_received = $c_url->has_error;
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $expected = [
            '://:80?key=value' => true,
            '://:80?key=value#anchor' => true,
            '://:80' => true,
            '://:80/?key=value' => true,
            '://:80/?key=value#anchor' => true,
            '://:80/' => true,
            '://:80/#anchor' => true,
            '://:80#anchor' => true,
            '://?key=value' => true,
            '://?key=value#anchor' => true,
            '://' => true,
            ':///?key=value' => true,
            ':///?key=value#anchor' => true,
            ':///' => true,
            ':///#anchor' => true,
            '://#anchor' => true,
            '://subdomain.domain:80?key=value' => true,
            '://subdomain.domain:80?key=value#anchor' => true,
            '://subdomain.domain:80' => true,
            '://subdomain.domain:80/?key=value' => true,
            '://subdomain.domain:80/?key=value#anchor' => true,
            '://subdomain.domain:80/' => true,
            '://subdomain.domain:80/#anchor' => true,
            '://subdomain.domain:80#anchor' => true,
            '://subdomain.domain?key=value' => true,
            '://subdomain.domain?key=value#anchor' => true,
            '://subdomain.domain' => true,
            '://subdomain.domain/?key=value' => true,
            '://subdomain.domain/?key=value#anchor' => true,
            '://subdomain.domain/' => true,
            '://subdomain.domain/#anchor' => true,
            '://subdomain.domain#anchor' => true,
            ':80?key=value' => true,
            ':80?key=value#anchor' => true,
            ':80' => true,
            ':80/?key=value' => true,
            ':80/?key=value#anchor' => true,
            ':80/' => true,
            ':80/#anchor' => true,
            ':80#anchor' => true,
            '?key=value' => true,
            '?key=value#anchor' => true,
            '' => true,
            '#anchor' => true,
            'http://:80?key=value' => true,
            'http://:80?key=value#anchor' => true,
            'http://:80' => true,
            'http://:80/?key=value' => true,
            'http://:80/?key=value#anchor' => true,
            'http://:80/' => true,
            'http://:80/#anchor' => true,
            'http://:80#anchor' => true,
            'http://?key=value' => true,
            'http://?key=value#anchor' => true,
            'http://' => true,
            'http:///?key=value' => true,
            'http:///?key=value#anchor' => true,
            'http:///' => true,
            'http:///#anchor' => true,
            'http://#anchor' => true,
            'http://subdomain.domain:80?key=value' => true,
            'http://subdomain.domain:80?key=value#anchor' => true,
            'http://subdomain.domain:80#anchor' => true,
            'http://subdomain.domain?key=value' => true,
            'http://subdomain.domain?key=value#anchor' => true,
            'http://subdomain.domain#anchor' => true,
            'http:80?key=value' => true,
            'http:80?key=value#anchor' => true,
            'http:80#anchor' => true,
            'http?key=value' => true,
            'http?key=value#anchor' => true,
            'http#anchor' => true,
            'httpsubdomain.domain:80?key=value' => true,
            'httpsubdomain.domain:80?key=value#anchor' => true,
            'httpsubdomain.domain:80#anchor' => true,
            'httpsubdomain.domain?key=value' => true,
            'httpsubdomain.domain?key=value#anchor' => true,
            'httpsubdomain.domain#anchor' => true,
            'subdomain.domain:80?key=value' => true,
            'subdomain.domain:80?key=value#anchor' => true,
            'subdomain.domain:80#anchor' => true,
            'subdomain.domain?key=value' => true,
            'subdomain.domain?key=value#anchor' => true,
            'subdomain.domain#anchor' => true,
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            '/?key=value' => false,
            '/?key=value#anchor' => false,
            '/' => false,
            '/#anchor' => false,
            'http://subdomain.domain:80' => false,
            'http://subdomain.domain:80/?key=value' => false,
            'http://subdomain.domain:80/?key=value#anchor' => false,
            'http://subdomain.domain:80/' => false,
            'http://subdomain.domain:80/#anchor' => false,
            'http://subdomain.domain' => false,
            'http://subdomain.domain/?key=value' => false,
            'http://subdomain.domain/?key=value#anchor' => false,
            'http://subdomain.domain/' => false,
            'http://subdomain.domain/#anchor' => false,
            'http:80' => false,
            'http:80/?key=value' => false,
            'http:80/?key=value#anchor' => false,
            'http:80/' => false,
            'http:80/#anchor' => false,
            'http' => false,
            'http/?key=value' => false,
            'http/?key=value#anchor' => false,
            'http/' => false,
            'http/#anchor' => false,
            'httpsubdomain.domain:80' => false,
            'httpsubdomain.domain:80/?key=value' => false,
            'httpsubdomain.domain:80/?key=value#anchor' => false,
            'httpsubdomain.domain:80/' => false,
            'httpsubdomain.domain:80/#anchor' => false,
            'httpsubdomain.domain' => false,
            'httpsubdomain.domain/?key=value' => false,
            'httpsubdomain.domain/?key=value#anchor' => false,
            'httpsubdomain.domain/' => false,
            'httpsubdomain.domain/#anchor' => false,
            'subdomain.domain:80' => false,
            'subdomain.domain:80/?key=value' => false,
            'subdomain.domain:80/?key=value#anchor' => false,
            'subdomain.domain:80/' => false,
            'subdomain.domain:80/#anchor' => false,
            'subdomain.domain' => false,
            'subdomain.domain/?key=value' => false,
            'subdomain.domain/?key=value#anchor' => false,
            'subdomain.domain/' => false,
            'subdomain.domain/#anchor' => false
        ];

        $parts = [
            1 => 'http',
            2 => '://',
            3 => 'subdomain.domain',
            4 => ':80',
            5 => '/',
            6 => '?key=value',
            7 => '#anchor'
        ];

        for ($i = 0b0000000; $i <= 0b1111111; $i++) {
            $c_value = '';
            if ($i & 0b0000001) $c_value.= $parts[1];
            if ($i & 0b0000010) $c_value.= $parts[2];
            if ($i & 0b0000100) $c_value.= $parts[3];
            if ($i & 0b0001000) $c_value.= $parts[4];
            if ($i & 0b0010000) $c_value.= $parts[5];
            if ($i & 0b0100000) $c_value.= $parts[6];
            if ($i & 0b1000000) $c_value.= $parts[7];
            $c_url = new URL($c_value);
            $c_expected = $expected[$c_value];
            $с_received = $c_url->has_error;
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }
    }

    static function test_step_code__absolute_get(&$test, $dpath) {
        $protocol = Request::scheme_get();
        $domain = Request::host_get();
        $data = [
                                    '/'                                 => $protocol.'://'.$domain,
                                    '/?key=value'                       => $protocol.'://'.$domain.'/?key=value',
                                    '/#anchor'                          => $protocol.'://'.$domain.'/#anchor',
                                    '/?key=value#anchor'                => $protocol.'://'.$domain.'/?key=value#anchor',
                                    '/dir/subdir/page'                  => $protocol.'://'.$domain.'/dir/subdir/page',
                                    '/dir/subdir/page?key=value'        => $protocol.'://'.$domain.'/dir/subdir/page?key=value',
                                    '/dir/subdir/page#anchor'           => $protocol.'://'.$domain.'/dir/subdir/page#anchor',
                                    '/dir/subdir/page?key=value#anchor' => $protocol.'://'.$domain.'/dir/subdir/page?key=value#anchor',
                            $domain                                     => $protocol.'://'.$domain,
                            $domain.'/'                                 => $protocol.'://'.$domain,
                            $domain.'/?key=value'                       => $protocol.'://'.$domain.'/?key=value',
                            $domain.'/#anchor'                          => $protocol.'://'.$domain.'/#anchor',
                            $domain.'/?key=value#anchor'                => $protocol.'://'.$domain.'/?key=value#anchor',
                            $domain.'/dir/subdir/page'                  => $protocol.'://'.$domain.'/dir/subdir/page',
                            $domain.'/dir/subdir/page?key=value'        => $protocol.'://'.$domain.'/dir/subdir/page?key=value',
                            $domain.'/dir/subdir/page#anchor'           => $protocol.'://'.$domain.'/dir/subdir/page#anchor',
                            $domain.'/dir/subdir/page?key=value#anchor' => $protocol.'://'.$domain.'/dir/subdir/page?key=value#anchor',
            $protocol.'://'.$domain                                     => $protocol.'://'.$domain,
            $protocol.'://'.$domain.'/'                                 => $protocol.'://'.$domain,
            $protocol.'://'.$domain.'/?key=value'                       => $protocol.'://'.$domain.'/?key=value',
            $protocol.'://'.$domain.'/#anchor'                          => $protocol.'://'.$domain.'/#anchor',
            $protocol.'://'.$domain.'/?key=value#anchor'                => $protocol.'://'.$domain.'/?key=value#anchor',
            $protocol.'://'.$domain.'/dir/subdir/page'                  => $protocol.'://'.$domain.'/dir/subdir/page',
            $protocol.'://'.$domain.'/dir/subdir/page?key=value'        => $protocol.'://'.$domain.'/dir/subdir/page?key=value',
            $protocol.'://'.$domain.'/dir/subdir/page#anchor'           => $protocol.'://'.$domain.'/dir/subdir/page#anchor',
            $protocol.'://'.$domain.'/dir/subdir/page?key=value#anchor' => $protocol.'://'.$domain.'/dir/subdir/page?key=value#anchor'
        ];

        foreach ($data as $c_value => $c_expected) {
            $c_url = new URL($c_value);
            $с_received = $c_url->absolute_get();
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }
    }

    static function test_step_code__relative_get(&$test, $dpath) {
        $protocol = Request::scheme_get();
        $domain = Request::host_get();
        $data = [
                                    '/'                                 => '/',
                                    '/?key=value'                       => '/?key=value',
                                    '/#anchor'                          => '/#anchor',
                                    '/?key=value#anchor'                => '/?key=value#anchor',
                                    '/dir/subdir/page'                  => '/dir/subdir/page',
                                    '/dir/subdir/page?key=value'        => '/dir/subdir/page?key=value',
                                    '/dir/subdir/page#anchor'           => '/dir/subdir/page#anchor',
                                    '/dir/subdir/page?key=value#anchor' => '/dir/subdir/page?key=value#anchor',
                            $domain                                     => '/',
                            $domain.'/'                                 => '/',
                            $domain.'/?key=value'                       => '/?key=value',
                            $domain.'/#anchor'                          => '/#anchor',
                            $domain.'/?key=value#anchor'                => '/?key=value#anchor',
                            $domain.'/dir/subdir/page'                  => '/dir/subdir/page',
                            $domain.'/dir/subdir/page?key=value'        => '/dir/subdir/page?key=value',
                            $domain.'/dir/subdir/page#anchor'           => '/dir/subdir/page#anchor',
                            $domain.'/dir/subdir/page?key=value#anchor' => '/dir/subdir/page?key=value#anchor',
            $protocol.'://'.$domain                                     => '/',
            $protocol.'://'.$domain.'/'                                 => '/',
            $protocol.'://'.$domain.'/?key=value'                       => '/?key=value',
            $protocol.'://'.$domain.'/#anchor'                          => '/#anchor',
            $protocol.'://'.$domain.'/?key=value#anchor'                => '/?key=value#anchor',
            $protocol.'://'.$domain.'/dir/subdir/page'                  => '/dir/subdir/page',
            $protocol.'://'.$domain.'/dir/subdir/page?key=value'        => '/dir/subdir/page?key=value',
            $protocol.'://'.$domain.'/dir/subdir/page#anchor'           => '/dir/subdir/page#anchor',
            $protocol.'://'.$domain.'/dir/subdir/page?key=value#anchor' => '/dir/subdir/page?key=value#anchor',
        ];

        foreach ($data as $c_value => $c_expected) {
            $c_url = new URL($c_value);
            $с_received = $c_url->relative_get();
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }
    }

    static function test_step_code__file_type_get(&$test, $dpath) {
        $file_1 = new URL('http://example.com/path/Name.ext');
        $file_2 = new URL('http://example.com/path/Name.Расширение');
        $file_3 = new URL('http://example.com/path/Имя.ext');
        $file_4 = new URL('http://example.com/path/Имя.Расширение');

        $received = $file_1->file_type_get() === 'ext' &&
                    $file_2->file_type_get() === 'расширение' &&
                    $file_3->file_type_get() === 'ext' &&
                    $file_4->file_type_get() === 'расширение';

        $expected = true;
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'file_type_get', 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'file_type_get', 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }
    }

    static function test_step_code__file_info_get(&$test, $dpath) {
        $data = [

            # ─────────────────────────────────────────────────────────────────────
            # possible transpositions of '"|.|..|...|0'
            # ─────────────────────────────────────────────────────────────────────

            /*  │"  │   │   │ */                 'http://example.com/'.''              => ['dirs' => null           , 'name' => null       , 'type' => null], # no file
            /*  │.  │   │   │ */                 'http://example.com/'.'.'             => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │.. │   │   │ */                 'http://example.com/'.'..'            => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │...│   │   │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT       , 'name' => '...'      , 'type' => ''  ],
            /*  │0  │   │   │ */                 'http://example.com/'.'0'             => ['dirs' => DIR_ROOT       , 'name' => '0'        , 'type' => ''  ],
            /*  │"  │"  │   │ */                 'http://example.com/'.''              => ['dirs' => null           , 'name' => null       , 'type' => null], # no file
            /*  │"  │.  │   │ */                 'http://example.com/'.'.'             => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │"  │.. │   │ */                 'http://example.com/'.'..'            => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │"  │...│   │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT       , 'name' => '...'      , 'type' => ''  ],
            /*  │"  │0  │   │ */                 'http://example.com/'.'0'             => ['dirs' => DIR_ROOT       , 'name' => '0'        , 'type' => ''  ],
            /*  │.  │"  │   │ */                 'http://example.com/'.'.'             => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │.  │.  │   │ */                 'http://example.com/'.'..'            => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │.  │.. │   │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT       , 'name' => '...'      , 'type' => ''  ],
            /*  │.  │...│   │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT       , 'name' => '....'     , 'type' => ''  ],
            /*  │.  │0  │   │ */                 'http://example.com/'.'.0'            => ['dirs' => DIR_ROOT       , 'name' => ''         , 'type' => '0' ],
            /*  │.. │"  │   │ */                 'http://example.com/'.'..'            => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │.. │.  │   │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT       , 'name' => '...'      , 'type' => ''  ],
            /*  │.. │.. │   │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT       , 'name' => '....'     , 'type' => ''  ],
            /*  │.. │...│   │ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT       , 'name' => '.....'    , 'type' => ''  ],
            /*  │.. │0  │   │ */                 'http://example.com/'.'..0'           => ['dirs' => DIR_ROOT       , 'name' => '.'        , 'type' => '0' ],
            /*  │...│"  │   │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT       , 'name' => '...'      , 'type' => ''  ],
            /*  │...│.  │   │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT       , 'name' => '....'     , 'type' => ''  ],
            /*  │...│.. │   │ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT       , 'name' => '.....'    , 'type' => ''  ],
            /*  │...│...│   │ */                 'http://example.com/'.'......'        => ['dirs' => DIR_ROOT       , 'name' => '......'   , 'type' => ''  ],
            /*  │...│0  │   │ */                 'http://example.com/'.'...0'          => ['dirs' => DIR_ROOT       , 'name' => '..'       , 'type' => '0' ],
            /*  │0  │"  │   │ */                 'http://example.com/'.'0'             => ['dirs' => DIR_ROOT       , 'name' => '0'        , 'type' => ''  ],
            /*  │0  │.  │   │ */                 'http://example.com/'.'0.'            => ['dirs' => DIR_ROOT       , 'name' => '0.'       , 'type' => ''  ],
            /*  │0  │.. │   │ */                 'http://example.com/'.'0..'           => ['dirs' => DIR_ROOT       , 'name' => '0..'      , 'type' => ''  ],
            /*  │0  │...│   │ */                 'http://example.com/'.'0...'          => ['dirs' => DIR_ROOT       , 'name' => '0...'     , 'type' => ''  ],
            /*  │0  │0  │   │ */                 'http://example.com/'.'00'            => ['dirs' => DIR_ROOT       , 'name' => '00'       , 'type' => ''  ],
            /*  │"  │"  │"  │ */                 'http://example.com/'.''              => ['dirs' => null           , 'name' => null       , 'type' => null], # no file
            /*  │"  │"  │.  │ */                 'http://example.com/'.'.'             => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │"  │"  │.. │ */                 'http://example.com/'.'..'            => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │"  │"  │...│ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT       , 'name' => '...'      , 'type' => ''  ],
            /*  │"  │"  │0  │ */                 'http://example.com/'.'0'             => ['dirs' => DIR_ROOT       , 'name' => '0'        , 'type' => ''  ],
            /*  │"  │.  │"  │ */                 'http://example.com/'.'.'             => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │"  │.  │.  │ */                 'http://example.com/'.'..'            => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │"  │.  │.. │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT       , 'name' => '...'      , 'type' => ''  ],
            /*  │"  │.  │...│ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT       , 'name' => '....'     , 'type' => ''  ],
            /*  │"  │.  │0  │ */                 'http://example.com/'.'.0'            => ['dirs' => DIR_ROOT       , 'name' => ''         , 'type' => '0' ],
            /*  │"  │.. │"  │ */                 'http://example.com/'.'..'            => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │"  │.. │.  │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT       , 'name' => '...'      , 'type' => ''  ],
            /*  │"  │.. │.. │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT       , 'name' => '....'     , 'type' => ''  ],
            /*  │"  │.. │...│ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT       , 'name' => '.....'    , 'type' => ''  ],
            /*  │"  │.. │0  │ */                 'http://example.com/'.'..0'           => ['dirs' => DIR_ROOT       , 'name' => '.'        , 'type' => '0' ],
            /*  │"  │...│"  │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT       , 'name' => '...'      , 'type' => ''  ],
            /*  │"  │...│.  │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT       , 'name' => '....'     , 'type' => ''  ],
            /*  │"  │...│.. │ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT       , 'name' => '.....'    , 'type' => ''  ],
            /*  │"  │...│...│ */                 'http://example.com/'.'......'        => ['dirs' => DIR_ROOT       , 'name' => '......'   , 'type' => ''  ],
            /*  │"  │...│0  │ */                 'http://example.com/'.'...0'          => ['dirs' => DIR_ROOT       , 'name' => '..'       , 'type' => '0' ],
            /*  │"  │0  │"  │ */                 'http://example.com/'.'0'             => ['dirs' => DIR_ROOT       , 'name' => '0'        , 'type' => ''  ],
            /*  │"  │0  │.  │ */                 'http://example.com/'.'0.'            => ['dirs' => DIR_ROOT       , 'name' => '0.'       , 'type' => ''  ],
            /*  │"  │0  │.. │ */                 'http://example.com/'.'0..'           => ['dirs' => DIR_ROOT       , 'name' => '0..'      , 'type' => ''  ],
            /*  │"  │0  │...│ */                 'http://example.com/'.'0...'          => ['dirs' => DIR_ROOT       , 'name' => '0...'     , 'type' => ''  ],
            /*  │"  │0  │0  │ */                 'http://example.com/'.'00'            => ['dirs' => DIR_ROOT       , 'name' => '00'       , 'type' => ''  ],
            /*  │.  │"  │"  │ */                 'http://example.com/'.'.'             => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │.  │"  │.  │ */                 'http://example.com/'.'..'            => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │.  │"  │.. │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT       , 'name' => '...'      , 'type' => ''  ],
            /*  │.  │"  │...│ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT       , 'name' => '....'     , 'type' => ''  ],
            /*  │.  │"  │0  │ */                 'http://example.com/'.'.0'            => ['dirs' => DIR_ROOT       , 'name' => ''         , 'type' => '0' ],
            /*  │.  │.  │"  │ */                 'http://example.com/'.'..'            => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │.  │.  │.  │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT       , 'name' => '...'      , 'type' => ''  ],
            /*  │.  │.  │.. │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT       , 'name' => '....'     , 'type' => ''  ],
            /*  │.  │.  │...│ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT       , 'name' => '.....'    , 'type' => ''  ],
            /*  │.  │.  │0  │ */                 'http://example.com/'.'..0'           => ['dirs' => DIR_ROOT       , 'name' => '.'        , 'type' => '0' ],
            /*  │.  │.. │"  │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT       , 'name' => '...'      , 'type' => ''  ],
            /*  │.  │.. │.  │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT       , 'name' => '....'     , 'type' => ''  ],
            /*  │.  │.. │.. │ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT       , 'name' => '.....'    , 'type' => ''  ],
            /*  │.  │.. │...│ */                 'http://example.com/'.'......'        => ['dirs' => DIR_ROOT       , 'name' => '......'   , 'type' => ''  ],
            /*  │.  │.. │0  │ */                 'http://example.com/'.'...0'          => ['dirs' => DIR_ROOT       , 'name' => '..'       , 'type' => '0' ],
            /*  │.  │...│"  │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT       , 'name' => '....'     , 'type' => ''  ],
            /*  │.  │...│.  │ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT       , 'name' => '.....'    , 'type' => ''  ],
            /*  │.  │...│.. │ */                 'http://example.com/'.'......'        => ['dirs' => DIR_ROOT       , 'name' => '......'   , 'type' => ''  ],
            /*  │.  │...│...│ */                 'http://example.com/'.'.......'       => ['dirs' => DIR_ROOT       , 'name' => '.......'  , 'type' => ''  ],
            /*  │.  │...│0  │ */                 'http://example.com/'.'....0'         => ['dirs' => DIR_ROOT       , 'name' => '...'      , 'type' => '0' ],
            /*  │.  │0  │"  │ */                 'http://example.com/'.'.0'            => ['dirs' => DIR_ROOT       , 'name' => ''         , 'type' => '0' ],
            /*  │.  │0  │.  │ */                 'http://example.com/'.'.0.'           => ['dirs' => DIR_ROOT       , 'name' => '.0.'      , 'type' => ''  ],
            /*  │.  │0  │.. │ */                 'http://example.com/'.'.0..'          => ['dirs' => DIR_ROOT       , 'name' => '.0..'     , 'type' => ''  ],
            /*  │.  │0  │...│ */                 'http://example.com/'.'.0...'         => ['dirs' => DIR_ROOT       , 'name' => '.0...'    , 'type' => ''  ],
            /*  │.  │0  │0  │ */                 'http://example.com/'.'.00'           => ['dirs' => DIR_ROOT       , 'name' => ''         , 'type' => '00'],
            /*  │.. │"  │"  │ */                 'http://example.com/'.'..'            => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │.. │"  │.  │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT       , 'name' => '...'      , 'type' => ''  ],
            /*  │.. │"  │.. │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT       , 'name' => '....'     , 'type' => ''  ],
            /*  │.. │"  │...│ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT       , 'name' => '.....'    , 'type' => ''  ],
            /*  │.. │"  │0  │ */                 'http://example.com/'.'..0'           => ['dirs' => DIR_ROOT       , 'name' => '.'        , 'type' => '0' ],
            /*  │.. │.  │"  │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT       , 'name' => '...'      , 'type' => ''  ],
            /*  │.. │.  │.  │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT       , 'name' => '....'     , 'type' => ''  ],
            /*  │.. │.  │.. │ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT       , 'name' => '.....'    , 'type' => ''  ],
            /*  │.. │.  │...│ */                 'http://example.com/'.'......'        => ['dirs' => DIR_ROOT       , 'name' => '......'   , 'type' => ''  ],
            /*  │.. │.  │0  │ */                 'http://example.com/'.'...0'          => ['dirs' => DIR_ROOT       , 'name' => '..'       , 'type' => '0' ],
            /*  │.. │.. │"  │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT       , 'name' => '....'     , 'type' => ''  ],
            /*  │.. │.. │.  │ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT       , 'name' => '.....'    , 'type' => ''  ],
            /*  │.. │.. │.. │ */                 'http://example.com/'.'......'        => ['dirs' => DIR_ROOT       , 'name' => '......'   , 'type' => ''  ],
            /*  │.. │.. │...│ */                 'http://example.com/'.'.......'       => ['dirs' => DIR_ROOT       , 'name' => '.......'  , 'type' => ''  ],
            /*  │.. │.. │0  │ */                 'http://example.com/'.'....0'         => ['dirs' => DIR_ROOT       , 'name' => '...'      , 'type' => '0' ],
            /*  │.. │...│"  │ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT       , 'name' => '.....'    , 'type' => ''  ],
            /*  │.. │...│.  │ */                 'http://example.com/'.'......'        => ['dirs' => DIR_ROOT       , 'name' => '......'   , 'type' => ''  ],
            /*  │.. │...│.. │ */                 'http://example.com/'.'.......'       => ['dirs' => DIR_ROOT       , 'name' => '.......'  , 'type' => ''  ],
            /*  │.. │...│...│ */                 'http://example.com/'.'........'      => ['dirs' => DIR_ROOT       , 'name' => '........' , 'type' => ''  ],
            /*  │.. │...│0  │ */                 'http://example.com/'.'.....0'        => ['dirs' => DIR_ROOT       , 'name' => '....'     , 'type' => '0' ],
            /*  │.. │0  │"  │ */                 'http://example.com/'.'..0'           => ['dirs' => DIR_ROOT       , 'name' => '.'        , 'type' => '0' ],
            /*  │.. │0  │.  │ */                 'http://example.com/'.'..0.'          => ['dirs' => DIR_ROOT       , 'name' => '..0.'     , 'type' => ''  ],
            /*  │.. │0  │.. │ */                 'http://example.com/'.'..0..'         => ['dirs' => DIR_ROOT       , 'name' => '..0..'    , 'type' => ''  ],
            /*  │.. │0  │...│ */                 'http://example.com/'.'..0...'        => ['dirs' => DIR_ROOT       , 'name' => '..0...'   , 'type' => ''  ],
            /*  │.. │0  │0  │ */                 'http://example.com/'.'..00'          => ['dirs' => DIR_ROOT       , 'name' => '.'        , 'type' => '00'],
            /*  │...│"  │"  │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT       , 'name' => '...'      , 'type' => ''  ],
            /*  │...│"  │.  │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT       , 'name' => '....'     , 'type' => ''  ],
            /*  │...│"  │.. │ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT       , 'name' => '.....'    , 'type' => ''  ],
            /*  │...│"  │...│ */                 'http://example.com/'.'......'        => ['dirs' => DIR_ROOT       , 'name' => '......'   , 'type' => ''  ],
            /*  │...│"  │0  │ */                 'http://example.com/'.'...0'          => ['dirs' => DIR_ROOT       , 'name' => '..'       , 'type' => '0' ],
            /*  │...│.  │"  │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT       , 'name' => '....'     , 'type' => ''  ],
            /*  │...│.  │.  │ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT       , 'name' => '.....'    , 'type' => ''  ],
            /*  │...│.  │.. │ */                 'http://example.com/'.'......'        => ['dirs' => DIR_ROOT       , 'name' => '......'   , 'type' => ''  ],
            /*  │...│.  │...│ */                 'http://example.com/'.'.......'       => ['dirs' => DIR_ROOT       , 'name' => '.......'  , 'type' => ''  ],
            /*  │...│.  │0  │ */                 'http://example.com/'.'....0'         => ['dirs' => DIR_ROOT       , 'name' => '...'      , 'type' => '0' ],
            /*  │...│.. │"  │ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT       , 'name' => '.....'    , 'type' => ''  ],
            /*  │...│.. │.  │ */                 'http://example.com/'.'......'        => ['dirs' => DIR_ROOT       , 'name' => '......'   , 'type' => ''  ],
            /*  │...│.. │.. │ */                 'http://example.com/'.'.......'       => ['dirs' => DIR_ROOT       , 'name' => '.......'  , 'type' => ''  ],
            /*  │...│.. │...│ */                 'http://example.com/'.'........'      => ['dirs' => DIR_ROOT       , 'name' => '........' , 'type' => ''  ],
            /*  │...│.. │0  │ */                 'http://example.com/'.'.....0'        => ['dirs' => DIR_ROOT       , 'name' => '....'     , 'type' => '0' ],
            /*  │...│...│"  │ */                 'http://example.com/'.'......'        => ['dirs' => DIR_ROOT       , 'name' => '......'   , 'type' => ''  ],
            /*  │...│...│.  │ */                 'http://example.com/'.'.......'       => ['dirs' => DIR_ROOT       , 'name' => '.......'  , 'type' => ''  ],
            /*  │...│...│.. │ */                 'http://example.com/'.'........'      => ['dirs' => DIR_ROOT       , 'name' => '........' , 'type' => ''  ],
            /*  │...│...│...│ */                 'http://example.com/'.'.........'     => ['dirs' => DIR_ROOT       , 'name' => '.........', 'type' => ''  ],
            /*  │...│...│0  │ */                 'http://example.com/'.'......0'       => ['dirs' => DIR_ROOT       , 'name' => '.....'    , 'type' => '0' ],
            /*  │...│0  │"  │ */                 'http://example.com/'.'...0'          => ['dirs' => DIR_ROOT       , 'name' => '..'       , 'type' => '0' ],
            /*  │...│0  │.  │ */                 'http://example.com/'.'...0.'         => ['dirs' => DIR_ROOT       , 'name' => '...0.'    , 'type' => ''  ],
            /*  │...│0  │.. │ */                 'http://example.com/'.'...0..'        => ['dirs' => DIR_ROOT       , 'name' => '...0..'   , 'type' => ''  ],
            /*  │...│0  │...│ */                 'http://example.com/'.'...0...'       => ['dirs' => DIR_ROOT       , 'name' => '...0...'  , 'type' => ''  ],
            /*  │...│0  │0  │ */                 'http://example.com/'.'...00'         => ['dirs' => DIR_ROOT       , 'name' => '..'       , 'type' => '00'],
            /*  │0  │"  │"  │ */                 'http://example.com/'.'0'             => ['dirs' => DIR_ROOT       , 'name' => '0'        , 'type' => ''  ],
            /*  │0  │"  │.  │ */                 'http://example.com/'.'0.'            => ['dirs' => DIR_ROOT       , 'name' => '0.'       , 'type' => ''  ],
            /*  │0  │"  │.. │ */                 'http://example.com/'.'0..'           => ['dirs' => DIR_ROOT       , 'name' => '0..'      , 'type' => ''  ],
            /*  │0  │"  │...│ */                 'http://example.com/'.'0...'          => ['dirs' => DIR_ROOT       , 'name' => '0...'     , 'type' => ''  ],
            /*  │0  │"  │0  │ */                 'http://example.com/'.'00'            => ['dirs' => DIR_ROOT       , 'name' => '00'       , 'type' => ''  ],
            /*  │0  │.  │"  │ */                 'http://example.com/'.'0.'            => ['dirs' => DIR_ROOT       , 'name' => '0.'       , 'type' => ''  ],
            /*  │0  │.  │.  │ */                 'http://example.com/'.'0..'           => ['dirs' => DIR_ROOT       , 'name' => '0..'      , 'type' => ''  ],
            /*  │0  │.  │.. │ */                 'http://example.com/'.'0...'          => ['dirs' => DIR_ROOT       , 'name' => '0...'     , 'type' => ''  ],
            /*  │0  │.  │...│ */                 'http://example.com/'.'0....'         => ['dirs' => DIR_ROOT       , 'name' => '0....'    , 'type' => ''  ],
            /*  │0  │.  │0  │ */                 'http://example.com/'.'0.0'           => ['dirs' => DIR_ROOT       , 'name' => '0'        , 'type' => '0' ],
            /*  │0  │.. │"  │ */                 'http://example.com/'.'0..'           => ['dirs' => DIR_ROOT       , 'name' => '0..'      , 'type' => ''  ],
            /*  │0  │.. │.  │ */                 'http://example.com/'.'0...'          => ['dirs' => DIR_ROOT       , 'name' => '0...'     , 'type' => ''  ],
            /*  │0  │.. │.. │ */                 'http://example.com/'.'0....'         => ['dirs' => DIR_ROOT       , 'name' => '0....'    , 'type' => ''  ],
            /*  │0  │.. │...│ */                 'http://example.com/'.'0.....'        => ['dirs' => DIR_ROOT       , 'name' => '0.....'   , 'type' => ''  ],
            /*  │0  │.. │0  │ */                 'http://example.com/'.'0..0'          => ['dirs' => DIR_ROOT       , 'name' => '0.'       , 'type' => '0' ],
            /*  │0  │...│"  │ */                 'http://example.com/'.'0...'          => ['dirs' => DIR_ROOT       , 'name' => '0...'     , 'type' => ''  ],
            /*  │0  │...│.  │ */                 'http://example.com/'.'0....'         => ['dirs' => DIR_ROOT       , 'name' => '0....'    , 'type' => ''  ],
            /*  │0  │...│.. │ */                 'http://example.com/'.'0.....'        => ['dirs' => DIR_ROOT       , 'name' => '0.....'   , 'type' => ''  ],
            /*  │0  │...│...│ */                 'http://example.com/'.'0......'       => ['dirs' => DIR_ROOT       , 'name' => '0......'  , 'type' => ''  ],
            /*  │0  │...│0  │ */                 'http://example.com/'.'0...0'         => ['dirs' => DIR_ROOT       , 'name' => '0..'      , 'type' => '0' ],
            /*  │0  │0  │"  │ */                 'http://example.com/'.'00'            => ['dirs' => DIR_ROOT       , 'name' => '00'       , 'type' => ''  ],
            /*  │0  │0  │.  │ */                 'http://example.com/'.'00.'           => ['dirs' => DIR_ROOT       , 'name' => '00.'      , 'type' => ''  ],
            /*  │0  │0  │.. │ */                 'http://example.com/'.'00..'          => ['dirs' => DIR_ROOT       , 'name' => '00..'     , 'type' => ''  ],
            /*  │0  │0  │...│ */                 'http://example.com/'.'00...'         => ['dirs' => DIR_ROOT       , 'name' => '00...'    , 'type' => ''  ],
            /*  │0  │0  │0  │ */                 'http://example.com/'.'000'           => ['dirs' => DIR_ROOT       , 'name' => '000'      , 'type' => ''  ],
            /*  │.  │0  │.  │0  │.  │ */         'http://example.com/'.'.0.0.'         => ['dirs' => DIR_ROOT       , 'name' => '.0.0.'    , 'type' => ''  ],
            /*  │.. │0  │.  │0  │.. │ */         'http://example.com/'.'..0.0..'       => ['dirs' => DIR_ROOT       , 'name' => '..0.0..'  , 'type' => ''  ],
            /*  │.. │0  │.. │0  │.. │ */         'http://example.com/'.'..0..0..'      => ['dirs' => DIR_ROOT       , 'name' => '..0..0..' , 'type' => ''  ],

            # ─────────────────────────────────────────────────────────────────────
            # possible transpositions of '"|.|..|...|0' + DIR
            # ─────────────────────────────────────────────────────────────────────

            /*  │dir  │"  │   │   │ */           'http://example.com/'.'dir/'          => ['dirs' => DIR_ROOT       , 'name' => 'dir'      , 'type' => ''  ], # redirect to 'http://example.com/dir'
            /*  │dir  │.  │   │   │ */           'http://example.com/'.'dir/.'         => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │dir  │.. │   │   │ */           'http://example.com/'.'dir/..'        => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │dir  │...│   │   │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...'      , 'type' => ''  ],
            /*  │dir  │0  │   │   │ */           'http://example.com/'.'dir/0'         => ['dirs' => DIR_ROOT.'dir/', 'name' => '0'        , 'type' => ''  ],
            /*  │dir  │"  │"  │   │ */           'http://example.com/'.'dir/'          => ['dirs' => DIR_ROOT       , 'name' => 'dir'      , 'type' => ''  ], # redirect to 'http://example.com/dir'
            /*  │dir  │"  │.  │   │ */           'http://example.com/'.'dir/.'         => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │dir  │"  │.. │   │ */           'http://example.com/'.'dir/..'        => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │dir  │"  │...│   │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...'      , 'type' => ''  ],
            /*  │dir  │"  │0  │   │ */           'http://example.com/'.'dir/0'         => ['dirs' => DIR_ROOT.'dir/', 'name' => '0'        , 'type' => ''  ],
            /*  │dir  │.  │"  │   │ */           'http://example.com/'.'dir/.'         => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │dir  │.  │.  │   │ */           'http://example.com/'.'dir/..'        => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │dir  │.  │.. │   │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...'      , 'type' => ''  ],
            /*  │dir  │.  │...│   │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....'     , 'type' => ''  ],
            /*  │dir  │.  │0  │   │ */           'http://example.com/'.'dir/.0'        => ['dirs' => DIR_ROOT.'dir/', 'name' => ''         , 'type' => '0' ],
            /*  │dir  │.. │"  │   │ */           'http://example.com/'.'dir/..'        => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │dir  │.. │.  │   │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...'      , 'type' => ''  ],
            /*  │dir  │.. │.. │   │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....'     , 'type' => ''  ],
            /*  │dir  │.. │...│   │ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....'    , 'type' => ''  ],
            /*  │dir  │.. │0  │   │ */           'http://example.com/'.'dir/..0'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '.'        , 'type' => '0' ],
            /*  │dir  │...│"  │   │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...'      , 'type' => ''  ],
            /*  │dir  │...│.  │   │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....'     , 'type' => ''  ],
            /*  │dir  │...│.. │   │ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....'    , 'type' => ''  ],
            /*  │dir  │...│...│   │ */           'http://example.com/'.'dir/......'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '......'   , 'type' => ''  ],
            /*  │dir  │...│0  │   │ */           'http://example.com/'.'dir/...0'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '..'       , 'type' => '0' ],
            /*  │dir  │0  │"  │   │ */           'http://example.com/'.'dir/0'         => ['dirs' => DIR_ROOT.'dir/', 'name' => '0'        , 'type' => ''  ],
            /*  │dir  │0  │.  │   │ */           'http://example.com/'.'dir/0.'        => ['dirs' => DIR_ROOT.'dir/', 'name' => '0.'       , 'type' => ''  ],
            /*  │dir  │0  │.. │   │ */           'http://example.com/'.'dir/0..'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '0..'      , 'type' => ''  ],
            /*  │dir  │0  │...│   │ */           'http://example.com/'.'dir/0...'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '0...'     , 'type' => ''  ],
            /*  │dir  │0  │0  │   │ */           'http://example.com/'.'dir/00'        => ['dirs' => DIR_ROOT.'dir/', 'name' => '00'       , 'type' => ''  ],
            /*  │dir  │"  │"  │"  │ */           'http://example.com/'.'dir/'          => ['dirs' => DIR_ROOT       , 'name' => 'dir'      , 'type' => ''  ], # redirect to 'http://example.com/dir'
            /*  │dir  │"  │"  │.  │ */           'http://example.com/'.'dir/.'         => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │dir  │"  │"  │.. │ */           'http://example.com/'.'dir/..'        => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │dir  │"  │"  │...│ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...'      , 'type' => ''  ],
            /*  │dir  │"  │"  │0  │ */           'http://example.com/'.'dir/0'         => ['dirs' => DIR_ROOT.'dir/', 'name' => '0'        , 'type' => ''  ],
            /*  │dir  │"  │.  │"  │ */           'http://example.com/'.'dir/.'         => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │dir  │"  │.  │.  │ */           'http://example.com/'.'dir/..'        => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │dir  │"  │.  │.. │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...'      , 'type' => ''  ],
            /*  │dir  │"  │.  │...│ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....'     , 'type' => ''  ],
            /*  │dir  │"  │.  │0  │ */           'http://example.com/'.'dir/.0'        => ['dirs' => DIR_ROOT.'dir/', 'name' => ''         , 'type' => '0' ],
            /*  │dir  │"  │.. │"  │ */           'http://example.com/'.'dir/..'        => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │dir  │"  │.. │.  │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...'      , 'type' => ''  ],
            /*  │dir  │"  │.. │.. │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....'     , 'type' => ''  ],
            /*  │dir  │"  │.. │...│ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....'    , 'type' => ''  ],
            /*  │dir  │"  │.. │0  │ */           'http://example.com/'.'dir/..0'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '.'        , 'type' => '0' ],
            /*  │dir  │"  │...│"  │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...'      , 'type' => ''  ],
            /*  │dir  │"  │...│.  │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....'     , 'type' => ''  ],
            /*  │dir  │"  │...│.. │ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....'    , 'type' => ''  ],
            /*  │dir  │"  │...│...│ */           'http://example.com/'.'dir/......'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '......'   , 'type' => ''  ],
            /*  │dir  │"  │...│0  │ */           'http://example.com/'.'dir/...0'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '..'       , 'type' => '0' ],
            /*  │dir  │"  │0  │"  │ */           'http://example.com/'.'dir/0'         => ['dirs' => DIR_ROOT.'dir/', 'name' => '0'        , 'type' => ''  ],
            /*  │dir  │"  │0  │.  │ */           'http://example.com/'.'dir/0.'        => ['dirs' => DIR_ROOT.'dir/', 'name' => '0.'       , 'type' => ''  ],
            /*  │dir  │"  │0  │.. │ */           'http://example.com/'.'dir/0..'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '0..'      , 'type' => ''  ],
            /*  │dir  │"  │0  │...│ */           'http://example.com/'.'dir/0...'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '0...'     , 'type' => ''  ],
            /*  │dir  │"  │0  │0  │ */           'http://example.com/'.'dir/00'        => ['dirs' => DIR_ROOT.'dir/', 'name' => '00'       , 'type' => ''  ],
            /*  │dir  │.  │"  │"  │ */           'http://example.com/'.'dir/.'         => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │dir  │.  │"  │.  │ */           'http://example.com/'.'dir/..'        => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │dir  │.  │"  │.. │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...'      , 'type' => ''  ],
            /*  │dir  │.  │"  │...│ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....'     , 'type' => ''  ],
            /*  │dir  │.  │"  │0  │ */           'http://example.com/'.'dir/.0'        => ['dirs' => DIR_ROOT.'dir/', 'name' => ''         , 'type' => '0' ],
            /*  │dir  │.  │.  │"  │ */           'http://example.com/'.'dir/..'        => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │dir  │.  │.  │.  │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...'      , 'type' => ''  ],
            /*  │dir  │.  │.  │.. │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....'     , 'type' => ''  ],
            /*  │dir  │.  │.  │...│ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....'    , 'type' => ''  ],
            /*  │dir  │.  │.  │0  │ */           'http://example.com/'.'dir/..0'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '.'        , 'type' => '0' ],
            /*  │dir  │.  │.. │"  │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...'      , 'type' => ''  ],
            /*  │dir  │.  │.. │.  │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....'     , 'type' => ''  ],
            /*  │dir  │.  │.. │.. │ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....'    , 'type' => ''  ],
            /*  │dir  │.  │.. │...│ */           'http://example.com/'.'dir/......'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '......'   , 'type' => ''  ],
            /*  │dir  │.  │.. │0  │ */           'http://example.com/'.'dir/...0'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '..'       , 'type' => '0' ],
            /*  │dir  │.  │...│"  │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....'     , 'type' => ''  ],
            /*  │dir  │.  │...│.  │ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....'    , 'type' => ''  ],
            /*  │dir  │.  │...│.. │ */           'http://example.com/'.'dir/......'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '......'   , 'type' => ''  ],
            /*  │dir  │.  │...│...│ */           'http://example.com/'.'dir/.......'   => ['dirs' => DIR_ROOT.'dir/', 'name' => '.......'  , 'type' => ''  ],
            /*  │dir  │.  │...│0  │ */           'http://example.com/'.'dir/....0'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '...'      , 'type' => '0' ],
            /*  │dir  │.  │0  │"  │ */           'http://example.com/'.'dir/.0'        => ['dirs' => DIR_ROOT.'dir/', 'name' => ''         , 'type' => '0' ],
            /*  │dir  │.  │0  │.  │ */           'http://example.com/'.'dir/.0.'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '.0.'      , 'type' => ''  ],
            /*  │dir  │.  │0  │.. │ */           'http://example.com/'.'dir/.0..'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '.0..'     , 'type' => ''  ],
            /*  │dir  │.  │0  │...│ */           'http://example.com/'.'dir/.0...'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.0...'    , 'type' => ''  ],
            /*  │dir  │.  │0  │0  │ */           'http://example.com/'.'dir/.00'       => ['dirs' => DIR_ROOT.'dir/', 'name' => ''         , 'type' => '00'],
            /*  │dir  │.. │"  │"  │ */           'http://example.com/'.'dir/..'        => ['dirs' => null           , 'name' => null       , 'type' => null], # reserved
            /*  │dir  │.. │"  │.  │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...'      , 'type' => ''  ],
            /*  │dir  │.. │"  │.. │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....'     , 'type' => ''  ],
            /*  │dir  │.. │"  │...│ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....'    , 'type' => ''  ],
            /*  │dir  │.. │"  │0  │ */           'http://example.com/'.'dir/..0'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '.'        , 'type' => '0' ],
            /*  │dir  │.. │.  │"  │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...'      , 'type' => ''  ],
            /*  │dir  │.. │.  │.  │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....'     , 'type' => ''  ],
            /*  │dir  │.. │.  │.. │ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....'    , 'type' => ''  ],
            /*  │dir  │.. │.  │...│ */           'http://example.com/'.'dir/......'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '......'   , 'type' => ''  ],
            /*  │dir  │.. │.  │0  │ */           'http://example.com/'.'dir/...0'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '..'       , 'type' => '0' ],
            /*  │dir  │.. │.. │"  │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....'     , 'type' => ''  ],
            /*  │dir  │.. │.. │.  │ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....'    , 'type' => ''  ],
            /*  │dir  │.. │.. │.. │ */           'http://example.com/'.'dir/......'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '......'   , 'type' => ''  ],
            /*  │dir  │.. │.. │...│ */           'http://example.com/'.'dir/.......'   => ['dirs' => DIR_ROOT.'dir/', 'name' => '.......'  , 'type' => ''  ],
            /*  │dir  │.. │.. │0  │ */           'http://example.com/'.'dir/....0'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '...'      , 'type' => '0' ],
            /*  │dir  │.. │...│"  │ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....'    , 'type' => ''  ],
            /*  │dir  │.. │...│.  │ */           'http://example.com/'.'dir/......'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '......'   , 'type' => ''  ],
            /*  │dir  │.. │...│.. │ */           'http://example.com/'.'dir/.......'   => ['dirs' => DIR_ROOT.'dir/', 'name' => '.......'  , 'type' => ''  ],
            /*  │dir  │.. │...│...│ */           'http://example.com/'.'dir/........'  => ['dirs' => DIR_ROOT.'dir/', 'name' => '........' , 'type' => ''  ],
            /*  │dir  │.. │...│0  │ */           'http://example.com/'.'dir/.....0'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '....'     , 'type' => '0' ],
            /*  │dir  │.. │0  │"  │ */           'http://example.com/'.'dir/..0'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '.'        , 'type' => '0' ],
            /*  │dir  │.. │0  │.  │ */           'http://example.com/'.'dir/..0.'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '..0.'     , 'type' => ''  ],
            /*  │dir  │.. │0  │.. │ */           'http://example.com/'.'dir/..0..'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '..0..'    , 'type' => ''  ],
            /*  │dir  │.. │0  │...│ */           'http://example.com/'.'dir/..0...'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '..0...'   , 'type' => ''  ],
            /*  │dir  │.. │0  │0  │ */           'http://example.com/'.'dir/..00'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '.'        , 'type' => '00'],
            /*  │dir  │...│"  │"  │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...'      , 'type' => ''  ],
            /*  │dir  │...│"  │.  │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....'     , 'type' => ''  ],
            /*  │dir  │...│"  │.. │ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....'    , 'type' => ''  ],
            /*  │dir  │...│"  │...│ */           'http://example.com/'.'dir/......'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '......'   , 'type' => ''  ],
            /*  │dir  │...│"  │0  │ */           'http://example.com/'.'dir/...0'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '..'       , 'type' => '0' ],
            /*  │dir  │...│.  │"  │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....'     , 'type' => ''  ],
            /*  │dir  │...│.  │.  │ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....'    , 'type' => ''  ],
            /*  │dir  │...│.  │.. │ */           'http://example.com/'.'dir/......'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '......'   , 'type' => ''  ],
            /*  │dir  │...│.  │...│ */           'http://example.com/'.'dir/.......'   => ['dirs' => DIR_ROOT.'dir/', 'name' => '.......'  , 'type' => ''  ],
            /*  │dir  │...│.  │0  │ */           'http://example.com/'.'dir/....0'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '...'      , 'type' => '0' ],
            /*  │dir  │...│.. │"  │ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....'    , 'type' => ''  ],
            /*  │dir  │...│.. │.  │ */           'http://example.com/'.'dir/......'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '......'   , 'type' => ''  ],
            /*  │dir  │...│.. │.. │ */           'http://example.com/'.'dir/.......'   => ['dirs' => DIR_ROOT.'dir/', 'name' => '.......'  , 'type' => ''  ],
            /*  │dir  │...│.. │...│ */           'http://example.com/'.'dir/........'  => ['dirs' => DIR_ROOT.'dir/', 'name' => '........' , 'type' => ''  ],
            /*  │dir  │...│.. │0  │ */           'http://example.com/'.'dir/.....0'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '....'     , 'type' => '0' ],
            /*  │dir  │...│...│"  │ */           'http://example.com/'.'dir/......'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '......'   , 'type' => ''  ],
            /*  │dir  │...│...│.  │ */           'http://example.com/'.'dir/.......'   => ['dirs' => DIR_ROOT.'dir/', 'name' => '.......'  , 'type' => ''  ],
            /*  │dir  │...│...│.. │ */           'http://example.com/'.'dir/........'  => ['dirs' => DIR_ROOT.'dir/', 'name' => '........' , 'type' => ''  ],
            /*  │dir  │...│...│...│ */           'http://example.com/'.'dir/.........' => ['dirs' => DIR_ROOT.'dir/', 'name' => '.........', 'type' => ''  ],
            /*  │dir  │...│...│0  │ */           'http://example.com/'.'dir/......0'   => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....'    , 'type' => '0' ],
            /*  │dir  │...│0  │"  │ */           'http://example.com/'.'dir/...0'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '..'       , 'type' => '0' ],
            /*  │dir  │...│0  │.  │ */           'http://example.com/'.'dir/...0.'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '...0.'    , 'type' => ''  ],
            /*  │dir  │...│0  │.. │ */           'http://example.com/'.'dir/...0..'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '...0..'   , 'type' => ''  ],
            /*  │dir  │...│0  │...│ */           'http://example.com/'.'dir/...0...'   => ['dirs' => DIR_ROOT.'dir/', 'name' => '...0...'  , 'type' => ''  ],
            /*  │dir  │...│0  │0  │ */           'http://example.com/'.'dir/...00'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '..'       , 'type' => '00'],
            /*  │dir  │0  │"  │"  │ */           'http://example.com/'.'dir/0'         => ['dirs' => DIR_ROOT.'dir/', 'name' => '0'        , 'type' => ''  ],
            /*  │dir  │0  │"  │.  │ */           'http://example.com/'.'dir/0.'        => ['dirs' => DIR_ROOT.'dir/', 'name' => '0.'       , 'type' => ''  ],
            /*  │dir  │0  │"  │.. │ */           'http://example.com/'.'dir/0..'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '0..'      , 'type' => ''  ],
            /*  │dir  │0  │"  │...│ */           'http://example.com/'.'dir/0...'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '0...'     , 'type' => ''  ],
            /*  │dir  │0  │"  │0  │ */           'http://example.com/'.'dir/00'        => ['dirs' => DIR_ROOT.'dir/', 'name' => '00'       , 'type' => ''  ],
            /*  │dir  │0  │.  │"  │ */           'http://example.com/'.'dir/0.'        => ['dirs' => DIR_ROOT.'dir/', 'name' => '0.'       , 'type' => ''  ],
            /*  │dir  │0  │.  │.  │ */           'http://example.com/'.'dir/0..'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '0..'      , 'type' => ''  ],
            /*  │dir  │0  │.  │.. │ */           'http://example.com/'.'dir/0...'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '0...'     , 'type' => ''  ],
            /*  │dir  │0  │.  │...│ */           'http://example.com/'.'dir/0....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '0....'    , 'type' => ''  ],
            /*  │dir  │0  │.  │0  │ */           'http://example.com/'.'dir/0.0'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '0'        , 'type' => '0' ],
            /*  │dir  │0  │.. │"  │ */           'http://example.com/'.'dir/0..'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '0..'      , 'type' => ''  ],
            /*  │dir  │0  │.. │.  │ */           'http://example.com/'.'dir/0...'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '0...'     , 'type' => ''  ],
            /*  │dir  │0  │.. │.. │ */           'http://example.com/'.'dir/0....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '0....'    , 'type' => ''  ],
            /*  │dir  │0  │.. │...│ */           'http://example.com/'.'dir/0.....'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '0.....'   , 'type' => ''  ],
            /*  │dir  │0  │.. │0  │ */           'http://example.com/'.'dir/0..0'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '0.'       , 'type' => '0' ],
            /*  │dir  │0  │...│"  │ */           'http://example.com/'.'dir/0...'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '0...'     , 'type' => ''  ],
            /*  │dir  │0  │...│.  │ */           'http://example.com/'.'dir/0....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '0....'    , 'type' => ''  ],
            /*  │dir  │0  │...│.. │ */           'http://example.com/'.'dir/0.....'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '0.....'   , 'type' => ''  ],
            /*  │dir  │0  │...│...│ */           'http://example.com/'.'dir/0......'   => ['dirs' => DIR_ROOT.'dir/', 'name' => '0......'  , 'type' => ''  ],
            /*  │dir  │0  │...│0  │ */           'http://example.com/'.'dir/0...0'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '0..'      , 'type' => '0' ],
            /*  │dir  │0  │0  │"  │ */           'http://example.com/'.'dir/00'        => ['dirs' => DIR_ROOT.'dir/', 'name' => '00'       , 'type' => ''  ],
            /*  │dir  │0  │0  │.  │ */           'http://example.com/'.'dir/00.'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '00.'      , 'type' => ''  ],
            /*  │dir  │0  │0  │.. │ */           'http://example.com/'.'dir/00..'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '00..'     , 'type' => ''  ],
            /*  │dir  │0  │0  │...│ */           'http://example.com/'.'dir/00...'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '00...'    , 'type' => ''  ],
            /*  │dir  │0  │0  │0  │ */           'http://example.com/'.'dir/000'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '000'      , 'type' => ''  ],
            /*  │dir  │.  │0  │.  │0  │.  │ */   'http://example.com/'.'dir/.0.0.'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.0.0.'    , 'type' => ''  ],
            /*  │dir  │.. │0  │.  │0  │.. │ */   'http://example.com/'.'dir/..0.0..'   => ['dirs' => DIR_ROOT.'dir/', 'name' => '..0.0..'  , 'type' => ''  ],
            /*  │dir  │.. │0  │.. │0  │.. │ */   'http://example.com/'.'dir/..0..0..'  => ['dirs' => DIR_ROOT.'dir/', 'name' => '..0..0..' , 'type' => ''  ],

        ];

        foreach ($data as $c_value => $c_expected) {
            $c_real_url = rtrim($c_value, '/'); # redirect emulation
            $с_received = (array)((new URL($c_real_url))->file_info_get());
            $c_result = $с_received                                 &&
                        $с_received['dirs'] === $c_expected['dirs'] &&
                        $с_received['name'] === $c_expected['name'] &&
                        $с_received['type'] === $c_expected['type'];
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }
    }

    static function test_step_code__query_args(&$test, $dpath) {

        ###################
        ### build_query ###
        ###################

        $data = [
            'main' => [
                'first name' => 'John Doe',
                'birthday' => '01/02/2003'
            ],
            'pastimes' => ['moto', 'bike'],
            'children' => [
                'Baby Doe Jr.' => ['age' => 10],
                'Baby Doe' => ['age' => 20]
            ],
            'programmer',
            'encode_[]=_key' => 'encode_[]=_value',
            'ключ 1' => 'значение 1',
            'no_value' => ''
        ];

        $expected = 'main[first%20name]=John%20Doe&'.
                    'main[birthday]=01%2F02%2F2003&'.
                    'pastimes[0]=moto&'.
                    'pastimes[1]=bike&'.
                    'children[Baby%20Doe%20Jr.][age]=10&'.
                    'children[Baby%20Doe][age]=20&'.
                    '0=programmer&'.
                    'encode_%5B%5D%3D_key=encode_%5B%5D%3D_value&'.
                    '%D0%BA%D0%BB%D1%8E%D1%87%201=%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D0%B5%201&'.
                    'no_value=';

        $received = URL::build_query($data);
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($expected, 0, 80, '…', 'UTF-8'), 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($expected, 0, 80, '…', 'UTF-8'), 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }

        #################
        ### parse_str ###
        #################

        $data = [
            ''                                                        => [],
            '&'                                                       => [],
            '='                                                       => [],
            '=value_1'                                                => [],
            'key_1'                                                   => ['key_1' => ''],
            'key_1='                                                  => ['key_1' => ''],
            'key_1=value_1'                                           => ['key_1' => 'value_1'],
            'key_1=value_1=still_value_1'                             => ['key_1' => 'value_1=still_value_1'],
            'key_1=value_1%3Dstill_value_1'                           => ['key_1' => 'value_1=still_value_1'],
            '&&&key_2'                                                => ['key_2' => ''],
            '&key_2'                                                  => ['key_2' => ''],
            'key_1&'                                                  => ['key_1' => ''],
            'key_1&key_2'                                             => ['key_1' => '', 'key_2' => ''],
            'key_1=value_1&key_arr[]=value_2&key_arr[]=value_3'       => ['key_1' => 'value_1', 'key_arr' => [   0  => 'value_2',    1  => 'value_3']],
            'key_1=value_1&key_arr[0]=value_2&key_arr[1]=value_3'     => ['key_1' => 'value_1', 'key_arr' => [   0  => 'value_2',    1  => 'value_3']],
            'key_1=value_1&key_arr[2]=value_2&key_arr[3]=value_3'     => ['key_1' => 'value_1', 'key_arr' => [   2  => 'value_2',    3  => 'value_3']],
            'key_1=value_1&key_arr[k-1]=value_2&key_arr[k-2]=value_3' => ['key_1' => 'value_1', 'key_arr' => ['k-1' => 'value_2', 'k-2' => 'value_3']],
            'key_1=value_1&key_arr[]=value_2&key_arr[k-2]=value_3'    => ['key_1' => 'value_1', 'key_arr' => [   0  => 'value_2', 'k-2' => 'value_3']],
            'key_1=value_1&key_arr[k-1]=value_2&key_arr[]=value_3'    => ['key_1' => 'value_1', 'key_arr' => ['k-1' => 'value_2',    0  => 'value_3']],
            'key_arr[][]=value_1'                                     => ['key_arr'  => [0 =>             [0 => 'value_1']]],
            'key_arr[][][]=value_1'                                   => ['key_arr'  => [0 => [0 =>       [0 => 'value_1']]]],
            'key_arr[][][][]=value_1'                                 => ['key_arr'  => [0 => [0 => [0 => [0 => 'value_1']]]]],
            'key_arr=value_1&key_arr[]=value_2'                       => ['key_arr'  => [0 =>                   'value_2']],
            'key_arr[]=value_1&key_arr=value_2'                       => ['key_arr'  =>                         'value_2'],
            'key_arr[=value_1'                                        => ['key_arr_' =>                         'value_1'],
            'key_arr]=value_1'                                        => ['key_arr]' =>                         'value_1'],
            '%D0%BA%D0%BB%D1%8E%D1%87=%D0%BC%D0%BE%D1%91%20%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D0%B5' => ['ключ' => 'моё значение'],
            '%D0%BA%D0%BB%D1%8E%D1%87=%D0%BC%D0%BE%D1%91%2B%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D0%B5' => ['ключ' => 'моё+значение']
        ];

        foreach ($data as $c_value => $c_expected) {
            $c_parse_result = null;
            $с_received = parse_str($c_value, $c_parse_result);
            $c_result = Core::data_serialize($c_parse_result, false, true) === Core::data_serialize($c_expected, false, true);
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($c_value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($c_value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }

        ########################
        ### query_arg_select ###
        ########################

        $value = 'http://example.com/?scalar=encode_%5B%5D%3D_value&array[0]=value%201&array[string]=value%202&%D0%BA%D0%BB%D1%8E%D1%87_1=%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D0%B5%201';
        $url = new URL($value);
        $expected = 'encode_[]=_value';
        $received = $url->query_arg_select('scalar');
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }

        $expected = [0 => 'value 1', 'string' => 'value 2'];
        $received = $url->query_arg_select('array');
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }

        $expected = 'значение 1';
        $received = $url->query_arg_select('ключ_1');
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }

        ########################
        ### query_arg_insert ###
        ########################

        $value = 'http://example.com/';
        $expected = 'http://example.com/?scalar=encode_%5B%5D%3D_value';
        $url = new URL($value);
        $url->query_arg_insert('scalar', 'encode_[]=_value');
        $received = $url->absolute_get();
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }

        $value = 'http://example.com/';
        $expected = 'http://example.com/?array[0]=value%201&array[string]=value%202';
        $url = new URL($value);
        $url->query_arg_insert('array', [0 => 'value 1', 'string' => 'value 2']);
        $received = $url->absolute_get();
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }

        $value = 'http://example.com/';
        $expected = 'http://example.com/?scalar=encode_%5B%5D%3D_value&array[0]=value%201&array[string]=value%202';
        $url = new URL($value);
        $url->query_arg_insert('scalar', 'encode_[]=_value');
        $url->query_arg_insert('array', [0 => 'value 1', 'string' => 'value 2']);
        $received = $url->absolute_get();
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }

        $value = 'http://example.com/';
        $expected = 'http://example.com/?%D0%BA%D0%BB%D1%8E%D1%87_1=%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D0%B5%201';
        $url = new URL($value);
        $url->query_arg_insert('ключ_1', 'значение 1');
        $received = $url->absolute_get();
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }

        ##################################
        ### query_arg_insert (replace) ###
        ##################################

        $value = 'http://example.com/?scalar=encode_%5B%5D%3D_value';
        $expected = 'http://example.com/?scalar=encode_%5B%5D%3D_new_value';
        $url = new URL($value);
        $url->query_arg_insert('scalar', 'encode_[]=_new_value');
        $received = $url->absolute_get();
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }

        $value = 'http://example.com/?array[0]=value%201&array[string]=value%202';
        $expected = 'http://example.com/?array[0]=new%20value%201&array[string]=new%20value%202';
        $url = new URL($value);
        $url->query_arg_insert('array', [0 => 'new value 1', 'string' => 'new value 2']);
        $received = $url->absolute_get();
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }

        $value = 'http://example.com/?scalar=encode_%5B%5D%3D_value&array[0]=value%201&array[string]=value%202';
        $expected = 'http://example.com/?scalar=encode_%5B%5D%3D_new_value&array[0]=new%20value%201&array[string]=new%20value%202';
        $url = new URL($value);
        $url->query_arg_insert('scalar', 'encode_[]=_new_value');
        $url->query_arg_insert('array', [0 => 'new value 1', 'string' => 'new value 2']);
        $received = $url->absolute_get();
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }

        $value = 'http://example.com/?%D0%BA%D0%BB%D1%8E%D1%87_1=%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D0%B5%201';
        $expected = 'http://example.com/?%D0%BA%D0%BB%D1%8E%D1%87_1=%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D0%B5%202';
        $url = new URL($value);
        $url->query_arg_insert('ключ_1', 'значение 2');
        $received = $url->absolute_get();
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }

        ########################
        ### query_arg_delete ###
        ########################

        $value = 'http://example.com/?scalar=encode_%5B%5D%3D_value&array[0]=value%201&array[string]=value%202';
        $expected = 'http://example.com/?array[0]=value%201&array[string]=value%202';
        $url = new URL($value);
        $url->query_arg_delete('scalar');
        $received = $url->absolute_get();
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }

        $value = 'http://example.com/?scalar=encode_%5B%5D%3D_value&array[0]=value%201&array[string]=value%202';
        $expected = 'http://example.com/?scalar=encode_%5B%5D%3D_value';
        $url = new URL($value);
        $url->query_arg_delete('array');
        $received = $url->absolute_get();
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }

        $value = 'http://example.com/?scalar=encode_%5B%5D%3D_value&array[0]=value%201&array[string]=value%202';
        $expected = 'http://example.com';
        $url = new URL($value);
        $url->query_arg_delete('scalar');
        $url->query_arg_delete('array');
        $received = $url->absolute_get();
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }

        $value = 'http://example.com/?%D0%BA%D0%BB%D1%8E%D1%87_1=%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D0%B5%201';
        $expected = 'http://example.com';
        $url = new URL($value);
        $url->query_arg_delete('ключ_1');
        $received = $url->absolute_get();
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => mb_strimwidth($value, 0, 80, '…', 'UTF-8'), 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }
    }

}
