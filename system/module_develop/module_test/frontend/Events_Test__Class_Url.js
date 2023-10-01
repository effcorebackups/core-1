
//////////////////////////////////////////////////////////////////
/// Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ///
//////////////////////////////////////////////////////////////////

'use strict';

// ─────────────────────────────────────────────────────────────────────
// Class Events_Test__Class_Url
// ─────────────────────────────────────────────────────────────────────

class Events_Test__Class_Url {

    static test_step_code__construct(test, dpath, c_results) {
        let protocol = window.location.protocol.replace(/[:]*$/g, '');
        let domain = window.location.host;
        let data = {
                                   '/'                                         : {'protocol' : protocol, 'domain' : domain,                'path' : '/',                    'query' : '',              'anchor' : ''      },
                                   '/?key=value'                               : {'protocol' : protocol, 'domain' : domain,                'path' : '/',                    'query' : 'key=value',     'anchor' : ''      },
                                   '/#anchor'                                  : {'protocol' : protocol, 'domain' : domain,                'path' : '/',                    'query' : '',              'anchor' : 'anchor'},
                                   '/?key=value#anchor'                        : {'protocol' : protocol, 'domain' : domain,                'path' : '/',                    'query' : 'key=value',     'anchor' : 'anchor'},
                                   '/dir/subdir/page'                          : {'protocol' : protocol, 'domain' : domain,                'path' : '/dir/subdir/page',     'query' : '',              'anchor' : ''      },
                                   '/dir/subdir/page?key=value'                : {'protocol' : protocol, 'domain' : domain,                'path' : '/dir/subdir/page',     'query' : 'key=value',     'anchor' : ''      },
                                   '/dir/subdir/page#anchor'                   : {'protocol' : protocol, 'domain' : domain,                'path' : '/dir/subdir/page',     'query' : '',              'anchor' : 'anchor'},
                                   '/dir/subdir/page?key=value#anchor'         : {'protocol' : protocol, 'domain' : domain,                'path' : '/dir/subdir/page',     'query' : 'key=value',     'anchor' : 'anchor'},
                   'subdomain.domain'                                          : {'protocol' : 'http',   'domain' : 'subdomain.domain',    'path' : '/',                    'query' : '',              'anchor' : ''      },
                   'subdomain.domain/?key=value'                               : {'protocol' : 'http',   'domain' : 'subdomain.domain',    'path' : '/',                    'query' : 'key=value',     'anchor' : ''      },
                   'subdomain.domain/#anchor'                                  : {'protocol' : 'http',   'domain' : 'subdomain.domain',    'path' : '/',                    'query' : '',              'anchor' : 'anchor'},
                   'subdomain.domain/?key=value#anchor'                        : {'protocol' : 'http',   'domain' : 'subdomain.domain',    'path' : '/',                    'query' : 'key=value',     'anchor' : 'anchor'},
                   'subdomain.domain/dir/subdir/page'                          : {'protocol' : 'http',   'domain' : 'subdomain.domain',    'path' : '/dir/subdir/page',     'query' : '',              'anchor' : ''      },
                   'subdomain.domain/dir/subdir/page?key=value'                : {'protocol' : 'http',   'domain' : 'subdomain.domain',    'path' : '/dir/subdir/page',     'query' : 'key=value',     'anchor' : ''      },
                   'subdomain.domain/dir/subdir/page#anchor'                   : {'protocol' : 'http',   'domain' : 'subdomain.domain',    'path' : '/dir/subdir/page',     'query' : '',              'anchor' : 'anchor'},
                   'subdomain.domain/dir/subdir/page?key=value#anchor'         : {'protocol' : 'http',   'domain' : 'subdomain.domain',    'path' : '/dir/subdir/page',     'query' : 'key=value',     'anchor' : 'anchor'},
                   'subdomain.domain:80'                                       : {'protocol' : 'http',   'domain' : 'subdomain.domain:80', 'path' : '/',                    'query' : '',              'anchor' : ''      },
                   'subdomain.domain:80/?key=value'                            : {'protocol' : 'http',   'domain' : 'subdomain.domain:80', 'path' : '/',                    'query' : 'key=value',     'anchor' : ''      },
                   'subdomain.domain:80/#anchor'                               : {'protocol' : 'http',   'domain' : 'subdomain.domain:80', 'path' : '/',                    'query' : '',              'anchor' : 'anchor'},
                   'subdomain.domain:80/?key=value#anchor'                     : {'protocol' : 'http',   'domain' : 'subdomain.domain:80', 'path' : '/',                    'query' : 'key=value',     'anchor' : 'anchor'},
                   'subdomain.domain:80/dir/subdir/page'                       : {'protocol' : 'http',   'domain' : 'subdomain.domain:80', 'path' : '/dir/subdir/page',     'query' : '',              'anchor' : ''      },
                   'subdomain.domain:80/dir/subdir/page?key=value'             : {'protocol' : 'http',   'domain' : 'subdomain.domain:80', 'path' : '/dir/subdir/page',     'query' : 'key=value',     'anchor' : ''      },
                   'subdomain.domain:80/dir/subdir/page#anchor'                : {'protocol' : 'http',   'domain' : 'subdomain.domain:80', 'path' : '/dir/subdir/page',     'query' : '',              'anchor' : 'anchor'},
                   'subdomain.domain:80/dir/subdir/page?key=value#anchor'      : {'protocol' : 'http',   'domain' : 'subdomain.domain:80', 'path' : '/dir/subdir/page',     'query' : 'key=value',     'anchor' : 'anchor'},
            'http://subdomain.domain'                                          : {'protocol' : 'http',   'domain' : 'subdomain.domain',    'path' : '/',                    'query' : '',              'anchor' : ''      },
            'http://subdomain.domain/?key=value'                               : {'protocol' : 'http',   'domain' : 'subdomain.domain',    'path' : '/',                    'query' : 'key=value',     'anchor' : ''      },
            'http://subdomain.domain/#anchor'                                  : {'protocol' : 'http',   'domain' : 'subdomain.domain',    'path' : '/',                    'query' : '',              'anchor' : 'anchor'},
            'http://subdomain.domain/?key=value#anchor'                        : {'protocol' : 'http',   'domain' : 'subdomain.domain',    'path' : '/',                    'query' : 'key=value',     'anchor' : 'anchor'},
            'http://subdomain.domain/dir/subdir/page'                          : {'protocol' : 'http',   'domain' : 'subdomain.domain',    'path' : '/dir/subdir/page',     'query' : '',              'anchor' : ''      },
            'http://subdomain.domain/dir/subdir/page?key=value'                : {'protocol' : 'http',   'domain' : 'subdomain.domain',    'path' : '/dir/subdir/page',     'query' : 'key=value',     'anchor' : ''      },
            'http://subdomain.domain/dir/subdir/page#anchor'                   : {'protocol' : 'http',   'domain' : 'subdomain.domain',    'path' : '/dir/subdir/page',     'query' : '',              'anchor' : 'anchor'},
            'http://subdomain.domain/dir/subdir/page?key=value#anchor'         : {'protocol' : 'http',   'domain' : 'subdomain.domain',    'path' : '/dir/subdir/page',     'query' : 'key=value',     'anchor' : 'anchor'},
            'http://subdomain.domain:80'                                       : {'protocol' : 'http',   'domain' : 'subdomain.domain:80', 'path' : '/',                    'query' : '',              'anchor' : ''      },
            'http://subdomain.domain:80/?key=value'                            : {'protocol' : 'http',   'domain' : 'subdomain.domain:80', 'path' : '/',                    'query' : 'key=value',     'anchor' : ''      },
            'http://subdomain.domain:80/#anchor'                               : {'protocol' : 'http',   'domain' : 'subdomain.domain:80', 'path' : '/',                    'query' : '',              'anchor' : 'anchor'},
            'http://subdomain.domain:80/?key=value#anchor'                     : {'protocol' : 'http',   'domain' : 'subdomain.domain:80', 'path' : '/',                    'query' : 'key=value',     'anchor' : 'anchor'},
            'http://subdomain.domain:80/dir/subdir/page'                       : {'protocol' : 'http',   'domain' : 'subdomain.domain:80', 'path' : '/dir/subdir/page',     'query' : '',              'anchor' : ''      },
            'http://subdomain.domain:80/dir/subdir/page?key=value'             : {'protocol' : 'http',   'domain' : 'subdomain.domain:80', 'path' : '/dir/subdir/page',     'query' : 'key=value',     'anchor' : ''      },
            'http://subdomain.domain:80/dir/subdir/page#anchor'                : {'protocol' : 'http',   'domain' : 'subdomain.domain:80', 'path' : '/dir/subdir/page',     'query' : '',              'anchor' : 'anchor'},
            'http://subdomain.domain:80/dir/subdir/page?key=value#anchor'      : {'protocol' : 'http',   'domain' : 'subdomain.domain:80', 'path' : '/dir/subdir/page',     'query' : 'key=value',     'anchor' : 'anchor'},
                                 '/?ключ=значение'                             : {'protocol' : protocol, 'domain' : domain,                'path' : '/',                    'query' : 'ключ=значение', 'anchor' : ''      },
                                 '/#якорь'                                     : {'protocol' : protocol, 'domain' : domain,                'path' : '/',                    'query' : '',              'anchor' : 'якорь' },
                                 '/?ключ=значение#якорь'                       : {'protocol' : protocol, 'domain' : domain,                'path' : '/',                    'query' : 'ключ=значение', 'anchor' : 'якорь' },
                                 '/дир/субдир/страница'                        : {'protocol' : protocol, 'domain' : domain,                'path' : '/дир/субдир/страница', 'query' : '',              'anchor' : ''      },
                                 '/дир/субдир/страница?ключ=значение'          : {'protocol' : protocol, 'domain' : domain,                'path' : '/дир/субдир/страница', 'query' : 'ключ=значение', 'anchor' : ''      },
                                 '/дир/субдир/страница#якорь'                  : {'protocol' : protocol, 'domain' : domain,                'path' : '/дир/субдир/страница', 'query' : '',              'anchor' : 'якорь' },
                                 '/дир/субдир/страница?ключ=значение#якорь'    : {'protocol' : protocol, 'domain' : domain,                'path' : '/дир/субдир/страница', 'query' : 'ключ=значение', 'anchor' : 'якорь' },
                   'субдомен.домен/?ключ=значение'                             : {'protocol' : 'http',   'domain' : 'субдомен.домен',      'path' : '/',                    'query' : 'ключ=значение', 'anchor' : ''      },
                   'субдомен.домен/#якорь'                                     : {'protocol' : 'http',   'domain' : 'субдомен.домен',      'path' : '/',                    'query' : '',              'anchor' : 'якорь' },
                   'субдомен.домен/?ключ=значение#якорь'                       : {'protocol' : 'http',   'domain' : 'субдомен.домен',      'path' : '/',                    'query' : 'ключ=значение', 'anchor' : 'якорь' },
                   'субдомен.домен/дир/субдир/страница'                        : {'protocol' : 'http',   'domain' : 'субдомен.домен',      'path' : '/дир/субдир/страница', 'query' : '',              'anchor' : ''      },
                   'субдомен.домен/дир/субдир/страница?ключ=значение'          : {'protocol' : 'http',   'domain' : 'субдомен.домен',      'path' : '/дир/субдир/страница', 'query' : 'ключ=значение', 'anchor' : ''      },
                   'субдомен.домен/дир/субдир/страница#якорь'                  : {'protocol' : 'http',   'domain' : 'субдомен.домен',      'path' : '/дир/субдир/страница', 'query' : '',              'anchor' : 'якорь' },
                   'субдомен.домен/дир/субдир/страница?ключ=значение#якорь'    : {'protocol' : 'http',   'domain' : 'субдомен.домен',      'path' : '/дир/субдир/страница', 'query' : 'ключ=значение', 'anchor' : 'якорь' },
                   'субдомен.домен:80/?ключ=значение'                          : {'protocol' : 'http',   'domain' : 'субдомен.домен:80',   'path' : '/',                    'query' : 'ключ=значение', 'anchor' : ''      },
                   'субдомен.домен:80/#якорь'                                  : {'protocol' : 'http',   'domain' : 'субдомен.домен:80',   'path' : '/',                    'query' : '',              'anchor' : 'якорь' },
                   'субдомен.домен:80/?ключ=значение#якорь'                    : {'protocol' : 'http',   'domain' : 'субдомен.домен:80',   'path' : '/',                    'query' : 'ключ=значение', 'anchor' : 'якорь' },
                   'субдомен.домен:80/дир/субдир/страница'                     : {'protocol' : 'http',   'domain' : 'субдомен.домен:80',   'path' : '/дир/субдир/страница', 'query' : '',              'anchor' : ''      },
                   'субдомен.домен:80/дир/субдир/страница?ключ=значение'       : {'protocol' : 'http',   'domain' : 'субдомен.домен:80',   'path' : '/дир/субдир/страница', 'query' : 'ключ=значение', 'anchor' : ''      },
                   'субдомен.домен:80/дир/субдир/страница#якорь'               : {'protocol' : 'http',   'domain' : 'субдомен.домен:80',   'path' : '/дир/субдир/страница', 'query' : '',              'anchor' : 'якорь' },
                   'субдомен.домен:80/дир/субдир/страница?ключ=значение#якорь' : {'protocol' : 'http',   'domain' : 'субдомен.домен:80',   'path' : '/дир/субдир/страница', 'query' : 'ключ=значение', 'anchor' : 'якорь' },
            'http://субдомен.домен/?ключ=значение'                             : {'protocol' : 'http',   'domain' : 'субдомен.домен',      'path' : '/',                    'query' : 'ключ=значение', 'anchor' : ''      },
            'http://субдомен.домен/#якорь'                                     : {'protocol' : 'http',   'domain' : 'субдомен.домен',      'path' : '/',                    'query' : '',              'anchor' : 'якорь' },
            'http://субдомен.домен/?ключ=значение#якорь'                       : {'protocol' : 'http',   'domain' : 'субдомен.домен',      'path' : '/',                    'query' : 'ключ=значение', 'anchor' : 'якорь' },
            'http://субдомен.домен/дир/субдир/страница'                        : {'protocol' : 'http',   'domain' : 'субдомен.домен',      'path' : '/дир/субдир/страница', 'query' : '',              'anchor' : ''      },
            'http://субдомен.домен/дир/субдир/страница?ключ=значение'          : {'protocol' : 'http',   'domain' : 'субдомен.домен',      'path' : '/дир/субдир/страница', 'query' : 'ключ=значение', 'anchor' : ''      },
            'http://субдомен.домен/дир/субдир/страница#якорь'                  : {'protocol' : 'http',   'domain' : 'субдомен.домен',      'path' : '/дир/субдир/страница', 'query' : '',              'anchor' : 'якорь' },
            'http://субдомен.домен/дир/субдир/страница?ключ=значение#якорь'    : {'protocol' : 'http',   'domain' : 'субдомен.домен',      'path' : '/дир/субдир/страница', 'query' : 'ключ=значение', 'anchor' : 'якорь' },
            'http://субдомен.домен:80/?ключ=значение'                          : {'protocol' : 'http',   'domain' : 'субдомен.домен:80',   'path' : '/',                    'query' : 'ключ=значение', 'anchor' : ''      },
            'http://субдомен.домен:80/#якорь'                                  : {'protocol' : 'http',   'domain' : 'субдомен.домен:80',   'path' : '/',                    'query' : '',              'anchor' : 'якорь' },
            'http://субдомен.домен:80/?ключ=значение#якорь'                    : {'protocol' : 'http',   'domain' : 'субдомен.домен:80',   'path' : '/',                    'query' : 'ключ=значение', 'anchor' : 'якорь' },
            'http://субдомен.домен:80/дир/субдир/страница'                     : {'protocol' : 'http',   'domain' : 'субдомен.домен:80',   'path' : '/дир/субдир/страница', 'query' : '',              'anchor' : ''      },
            'http://субдомен.домен:80/дир/субдир/страница?ключ=значение'       : {'protocol' : 'http',   'domain' : 'субдомен.домен:80',   'path' : '/дир/субдир/страница', 'query' : 'ключ=значение', 'anchor' : ''      },
            'http://субдомен.домен:80/дир/субдир/страница#якорь'               : {'protocol' : 'http',   'domain' : 'субдомен.домен:80',   'path' : '/дир/субдир/страница', 'query' : '',              'anchor' : 'якорь' },
            'http://субдомен.домен:80/дир/субдир/страница?ключ=значение#якорь' : {'protocol' : 'http',   'domain' : 'субдомен.домен:80',   'path' : '/дир/субдир/страница', 'query' : 'ключ=значение', 'anchor' : 'якорь' },
        };

        for (let c_value in data) {
            let c_expected = data[c_value];
            let c_url = new EffURL(c_value);
            let c_result = c_url.protocol === c_expected.protocol &&
                           c_url.domain   === c_expected.domain   &&
                           c_url.path     === c_expected.path     &&
                           c_url.query    === c_expected.query    &&
                           c_url.anchor   === c_expected.anchor;
            if (c_result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_value, 'result' : Effcore.getTranslation('success')}) );
            if (c_result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_value, 'result' : Effcore.getTranslation('failure')}) );
            if (c_result !== true) {
                c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : JSON.stringify(c_expected)}) );
                c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : JSON.stringify(c_url)}) );
                c_results['return'] = 0;
                return;
            }
        }
    }

    static test_step_code__has_error(test, dpath, c_results) {
        let urls = [
            ':',
            ':/',
            'http:',
            'http:/',
            'http:///',
            'http:///path/',
            'http:/domain/path?key=value',
            'javascript://%0Aalert(document.cookie)'
        ];

        for (let c_value of urls) {
            let c_url = new EffURL(c_value);
            let c_expected = true;
            let c_gotten = c_url.has_error;
            let c_result = c_gotten === c_expected;
            if (c_result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_value, 'result' : Effcore.getTranslation('success')}) );
            if (c_result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_value, 'result' : Effcore.getTranslation('failure')}) );
            if (c_result !== true) {
                c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : JSON.stringify(c_expected)}) );
                c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : JSON.stringify(c_gotten)}) );
                c_results['return'] = 0;
                return;
            }
        }

        // ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        let expected = {
            '://:80?key=value' : true,
            '://:80?key=value#anchor' : true,
            '://:80' : true,
            '://:80/dir/subdir/page?key=value' : true,
            '://:80/dir/subdir/page?key=value#anchor' : true,
            '://:80/dir/subdir/page' : true,
            '://:80/dir/subdir/page#anchor' : true,
            '://:80#anchor' : true,
            '://?key=value' : true,
            '://?key=value#anchor' : true,
            '://' : true,
            ':///dir/subdir/page?key=value' : true,
            ':///dir/subdir/page?key=value#anchor' : true,
            ':///dir/subdir/page' : true,
            ':///dir/subdir/page#anchor' : true,
            '://#anchor' : true,
            '://subdomain.domain:80?key=value' : true,
            '://subdomain.domain:80?key=value#anchor' : true,
            '://subdomain.domain:80' : true,
            '://subdomain.domain:80/dir/subdir/page?key=value' : true,
            '://subdomain.domain:80/dir/subdir/page?key=value#anchor' : true,
            '://subdomain.domain:80/dir/subdir/page' : true,
            '://subdomain.domain:80/dir/subdir/page#anchor' : true,
            '://subdomain.domain:80#anchor' : true,
            '://subdomain.domain?key=value' : true,
            '://subdomain.domain?key=value#anchor' : true,
            '://subdomain.domain' : true,
            '://subdomain.domain/dir/subdir/page?key=value' : true,
            '://subdomain.domain/dir/subdir/page?key=value#anchor' : true,
            '://subdomain.domain/dir/subdir/page' : true,
            '://subdomain.domain/dir/subdir/page#anchor' : true,
            '://subdomain.domain#anchor' : true,
            ':80?key=value' : true,
            ':80?key=value#anchor' : true,
            ':80' : true,
            ':80/dir/subdir/page?key=value' : true,
            ':80/dir/subdir/page?key=value#anchor' : true,
            ':80/dir/subdir/page' : true,
            ':80/dir/subdir/page#anchor' : true,
            ':80#anchor' : true,
            '?key=value' : true,
            '?key=value#anchor' : true,
            '' : true,
            '#anchor' : true,
            'http://:80?key=value' : true,
            'http://:80?key=value#anchor' : true,
            'http://:80' : true,
            'http://:80/dir/subdir/page?key=value' : true,
            'http://:80/dir/subdir/page?key=value#anchor' : true,
            'http://:80/dir/subdir/page' : true,
            'http://:80/dir/subdir/page#anchor' : true,
            'http://:80#anchor' : true,
            'http://?key=value' : true,
            'http://?key=value#anchor' : true,
            'http://' : true,
            'http:///dir/subdir/page?key=value' : true,
            'http:///dir/subdir/page?key=value#anchor' : true,
            'http:///dir/subdir/page' : true,
            'http:///dir/subdir/page#anchor' : true,
            'http://#anchor' : true,
            'http://subdomain.domain:80?key=value' : true,
            'http://subdomain.domain:80?key=value#anchor' : true,
            'http://subdomain.domain:80#anchor' : true,
            'http://subdomain.domain?key=value' : true,
            'http://subdomain.domain?key=value#anchor' : true,
            'http://subdomain.domain#anchor' : true,
            'http:80?key=value' : true,
            'http:80?key=value#anchor' : true,
            'http:80#anchor' : true,
            'http?key=value' : true,
            'http?key=value#anchor' : true,
            'http#anchor' : true,
            'httpsubdomain.domain:80?key=value' : true,
            'httpsubdomain.domain:80?key=value#anchor' : true,
            'httpsubdomain.domain:80#anchor' : true,
            'httpsubdomain.domain?key=value' : true,
            'httpsubdomain.domain?key=value#anchor' : true,
            'httpsubdomain.domain#anchor' : true,
            'subdomain.domain:80?key=value' : true,
            'subdomain.domain:80?key=value#anchor' : true,
            'subdomain.domain:80#anchor' : true,
            'subdomain.domain?key=value' : true,
            'subdomain.domain?key=value#anchor' : true,
            'subdomain.domain#anchor' : true,
            // ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            '/dir/subdir/page?key=value' : false,
            '/dir/subdir/page?key=value#anchor' : false,
            '/dir/subdir/page' : false,
            '/dir/subdir/page#anchor' : false,
            'http://subdomain.domain:80' : false,
            'http://subdomain.domain:80/dir/subdir/page?key=value' : false,
            'http://subdomain.domain:80/dir/subdir/page?key=value#anchor' : false,
            'http://subdomain.domain:80/dir/subdir/page' : false,
            'http://subdomain.domain:80/dir/subdir/page#anchor' : false,
            'http://subdomain.domain' : false,
            'http://subdomain.domain/dir/subdir/page?key=value' : false,
            'http://subdomain.domain/dir/subdir/page?key=value#anchor' : false,
            'http://subdomain.domain/dir/subdir/page' : false,
            'http://subdomain.domain/dir/subdir/page#anchor' : false,
            'http:80' : false,
            'http:80/dir/subdir/page?key=value' : false,
            'http:80/dir/subdir/page?key=value#anchor' : false,
            'http:80/dir/subdir/page' : false,
            'http:80/dir/subdir/page#anchor' : false,
            'http' : false,
            'http/dir/subdir/page?key=value' : false,
            'http/dir/subdir/page?key=value#anchor' : false,
            'http/dir/subdir/page' : false,
            'http/dir/subdir/page#anchor' : false,
            'httpsubdomain.domain:80' : false,
            'httpsubdomain.domain:80/dir/subdir/page?key=value' : false,
            'httpsubdomain.domain:80/dir/subdir/page?key=value#anchor' : false,
            'httpsubdomain.domain:80/dir/subdir/page' : false,
            'httpsubdomain.domain:80/dir/subdir/page#anchor' : false,
            'httpsubdomain.domain' : false,
            'httpsubdomain.domain/dir/subdir/page?key=value' : false,
            'httpsubdomain.domain/dir/subdir/page?key=value#anchor' : false,
            'httpsubdomain.domain/dir/subdir/page' : false,
            'httpsubdomain.domain/dir/subdir/page#anchor' : false,
            'subdomain.domain:80' : false,
            'subdomain.domain:80/dir/subdir/page?key=value' : false,
            'subdomain.domain:80/dir/subdir/page?key=value#anchor' : false,
            'subdomain.domain:80/dir/subdir/page' : false,
            'subdomain.domain:80/dir/subdir/page#anchor' : false,
            'subdomain.domain' : false,
            'subdomain.domain/dir/subdir/page?key=value' : false,
            'subdomain.domain/dir/subdir/page?key=value#anchor' : false,
            'subdomain.domain/dir/subdir/page' : false,
            'subdomain.domain/dir/subdir/page#anchor' : false
        };

        let parts = {
            1 : 'http',
            2 : '://',
            3 : 'subdomain.domain',
            4 : ':80',
            5 : '/dir/subdir/page',
            6 : '?key=value',
            7 : '#anchor'
        };

        for (let i = 0b0000000; i <= 0b1111111; i++) {
            let c_value = '';
            if (i & 0b0000001) c_value += parts[1];
            if (i & 0b0000010) c_value += parts[2];
            if (i & 0b0000100) c_value += parts[3];
            if (i & 0b0001000) c_value += parts[4];
            if (i & 0b0010000) c_value += parts[5];
            if (i & 0b0100000) c_value += parts[6];
            if (i & 0b1000000) c_value += parts[7];
            let c_url = new EffURL(c_value);
            let c_expected = expected[c_value];
            let c_gotten = c_url.has_error;
            let c_result = c_gotten === c_expected;
            if (c_result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_value, 'result' : Effcore.getTranslation('success')}) );
            if (c_result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_value, 'result' : Effcore.getTranslation('failure')}) );
            if (c_result !== true) {
                c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : JSON.stringify(c_expected)}) );
                c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : JSON.stringify(c_gotten)}) );
                c_results['return'] = 0;
                return;
            }
        }

        // ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        expected = {
            '://:80?key=value' : true,
            '://:80?key=value#anchor' : true,
            '://:80' : true,
            '://:80/?key=value' : true,
            '://:80/?key=value#anchor' : true,
            '://:80/' : true,
            '://:80/#anchor' : true,
            '://:80#anchor' : true,
            '://?key=value' : true,
            '://?key=value#anchor' : true,
            '://' : true,
            ':///?key=value' : true,
            ':///?key=value#anchor' : true,
            ':///' : true,
            ':///#anchor' : true,
            '://#anchor' : true,
            '://subdomain.domain:80?key=value' : true,
            '://subdomain.domain:80?key=value#anchor' : true,
            '://subdomain.domain:80' : true,
            '://subdomain.domain:80/?key=value' : true,
            '://subdomain.domain:80/?key=value#anchor' : true,
            '://subdomain.domain:80/' : true,
            '://subdomain.domain:80/#anchor' : true,
            '://subdomain.domain:80#anchor' : true,
            '://subdomain.domain?key=value' : true,
            '://subdomain.domain?key=value#anchor' : true,
            '://subdomain.domain' : true,
            '://subdomain.domain/?key=value' : true,
            '://subdomain.domain/?key=value#anchor' : true,
            '://subdomain.domain/' : true,
            '://subdomain.domain/#anchor' : true,
            '://subdomain.domain#anchor' : true,
            ':80?key=value' : true,
            ':80?key=value#anchor' : true,
            ':80' : true,
            ':80/?key=value' : true,
            ':80/?key=value#anchor' : true,
            ':80/' : true,
            ':80/#anchor' : true,
            ':80#anchor' : true,
            '?key=value' : true,
            '?key=value#anchor' : true,
            '' : true,
            '#anchor' : true,
            'http://:80?key=value' : true,
            'http://:80?key=value#anchor' : true,
            'http://:80' : true,
            'http://:80/?key=value' : true,
            'http://:80/?key=value#anchor' : true,
            'http://:80/' : true,
            'http://:80/#anchor' : true,
            'http://:80#anchor' : true,
            'http://?key=value' : true,
            'http://?key=value#anchor' : true,
            'http://' : true,
            'http:///?key=value' : true,
            'http:///?key=value#anchor' : true,
            'http:///' : true,
            'http:///#anchor' : true,
            'http://#anchor' : true,
            'http://subdomain.domain:80?key=value' : true,
            'http://subdomain.domain:80?key=value#anchor' : true,
            'http://subdomain.domain:80#anchor' : true,
            'http://subdomain.domain?key=value' : true,
            'http://subdomain.domain?key=value#anchor' : true,
            'http://subdomain.domain#anchor' : true,
            'http:80?key=value' : true,
            'http:80?key=value#anchor' : true,
            'http:80#anchor' : true,
            'http?key=value' : true,
            'http?key=value#anchor' : true,
            'http#anchor' : true,
            'httpsubdomain.domain:80?key=value' : true,
            'httpsubdomain.domain:80?key=value#anchor' : true,
            'httpsubdomain.domain:80#anchor' : true,
            'httpsubdomain.domain?key=value' : true,
            'httpsubdomain.domain?key=value#anchor' : true,
            'httpsubdomain.domain#anchor' : true,
            'subdomain.domain:80?key=value' : true,
            'subdomain.domain:80?key=value#anchor' : true,
            'subdomain.domain:80#anchor' : true,
            'subdomain.domain?key=value' : true,
            'subdomain.domain?key=value#anchor' : true,
            'subdomain.domain#anchor' : true,
            // ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            '/?key=value' : false,
            '/?key=value#anchor' : false,
            '/' : false,
            '/#anchor' : false,
            'http://subdomain.domain:80' : false,
            'http://subdomain.domain:80/?key=value' : false,
            'http://subdomain.domain:80/?key=value#anchor' : false,
            'http://subdomain.domain:80/' : false,
            'http://subdomain.domain:80/#anchor' : false,
            'http://subdomain.domain' : false,
            'http://subdomain.domain/?key=value' : false,
            'http://subdomain.domain/?key=value#anchor' : false,
            'http://subdomain.domain/' : false,
            'http://subdomain.domain/#anchor' : false,
            'http:80' : false,
            'http:80/?key=value' : false,
            'http:80/?key=value#anchor' : false,
            'http:80/' : false,
            'http:80/#anchor' : false,
            'http' : false,
            'http/?key=value' : false,
            'http/?key=value#anchor' : false,
            'http/' : false,
            'http/#anchor' : false,
            'httpsubdomain.domain:80' : false,
            'httpsubdomain.domain:80/?key=value' : false,
            'httpsubdomain.domain:80/?key=value#anchor' : false,
            'httpsubdomain.domain:80/' : false,
            'httpsubdomain.domain:80/#anchor' : false,
            'httpsubdomain.domain' : false,
            'httpsubdomain.domain/?key=value' : false,
            'httpsubdomain.domain/?key=value#anchor' : false,
            'httpsubdomain.domain/' : false,
            'httpsubdomain.domain/#anchor' : false,
            'subdomain.domain:80' : false,
            'subdomain.domain:80/?key=value' : false,
            'subdomain.domain:80/?key=value#anchor' : false,
            'subdomain.domain:80/' : false,
            'subdomain.domain:80/#anchor' : false,
            'subdomain.domain' : false,
            'subdomain.domain/?key=value' : false,
            'subdomain.domain/?key=value#anchor' : false,
            'subdomain.domain/' : false,
            'subdomain.domain/#anchor' : false
        };

        parts = {
            1 : 'http',
            2 : '://',
            3 : 'subdomain.domain',
            4 : ':80',
            5 : '/',
            6 : '?key=value',
            7 : '#anchor'
        };

        for (let i = 0b0000000; i <= 0b1111111; i++) {
            let c_value = '';
            if (i & 0b0000001) c_value += parts[1];
            if (i & 0b0000010) c_value += parts[2];
            if (i & 0b0000100) c_value += parts[3];
            if (i & 0b0001000) c_value += parts[4];
            if (i & 0b0010000) c_value += parts[5];
            if (i & 0b0100000) c_value += parts[6];
            if (i & 0b1000000) c_value += parts[7];
            let c_url = new EffURL(c_value);
            let c_expected = expected[c_value];
            let c_gotten = c_url.has_error;
            let c_result = c_gotten === c_expected;
            if (c_result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_value, 'result' : Effcore.getTranslation('success')}) );
            if (c_result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_value, 'result' : Effcore.getTranslation('failure')}) );
            if (c_result !== true) {
                c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : JSON.stringify(c_expected)}) );
                c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : JSON.stringify(c_gotten)}) );
                c_results['return'] = 0;
                return;
            }
        }
    }

    static test_step_code__full_get(test, dpath, c_results) {
        let protocol = window.location.protocol.replace(/[:]*$/g, '');
        let domain = window.location.host;
        let data = {};
            data[                            '/'                                ] = protocol + '://' + domain;
            data[                            '/?key=value'                      ] = protocol + '://' + domain + '/?key=value';
            data[                            '/#anchor'                         ] = protocol + '://' + domain + '/#anchor';
            data[                            '/?key=value#anchor'               ] = protocol + '://' + domain + '/?key=value#anchor';
            data[                            '/dir/subdir/page'                 ] = protocol + '://' + domain + '/dir/subdir/page';
            data[                            '/dir/subdir/page?key=value'       ] = protocol + '://' + domain + '/dir/subdir/page?key=value';
            data[                            '/dir/subdir/page#anchor'          ] = protocol + '://' + domain + '/dir/subdir/page#anchor';
            data[                            '/dir/subdir/page?key=value#anchor'] = protocol + '://' + domain + '/dir/subdir/page?key=value#anchor';
            data[                   domain                                      ] = protocol + '://' + domain;
            data[                   domain + '/?key=value'                      ] = protocol + '://' + domain + '/?key=value';
            data[                   domain + '/#anchor'                         ] = protocol + '://' + domain + '/#anchor';
            data[                   domain + '/?key=value#anchor'               ] = protocol + '://' + domain + '/?key=value#anchor';
            data[                   domain + '/dir/subdir/page'                 ] = protocol + '://' + domain + '/dir/subdir/page';
            data[                   domain + '/dir/subdir/page?key=value'       ] = protocol + '://' + domain + '/dir/subdir/page?key=value';
            data[                   domain + '/dir/subdir/page#anchor'          ] = protocol + '://' + domain + '/dir/subdir/page#anchor';
            data[                   domain + '/dir/subdir/page?key=value#anchor'] = protocol + '://' + domain + '/dir/subdir/page?key=value#anchor';
            data[protocol + '://' + domain                                      ] = protocol + '://' + domain;
            data[protocol + '://' + domain + '/?key=value'                      ] = protocol + '://' + domain + '/?key=value';
            data[protocol + '://' + domain + '/#anchor'                         ] = protocol + '://' + domain + '/#anchor';
            data[protocol + '://' + domain + '/?key=value#anchor'               ] = protocol + '://' + domain + '/?key=value#anchor';
            data[protocol + '://' + domain + '/dir/subdir/page'                 ] = protocol + '://' + domain + '/dir/subdir/page';
            data[protocol + '://' + domain + '/dir/subdir/page?key=value'       ] = protocol + '://' + domain + '/dir/subdir/page?key=value';
            data[protocol + '://' + domain + '/dir/subdir/page#anchor'          ] = protocol + '://' + domain + '/dir/subdir/page#anchor';
            data[protocol + '://' + domain + '/dir/subdir/page?key=value#anchor'] = protocol + '://' + domain + '/dir/subdir/page?key=value#anchor';

        for (let c_value in data) {
            let c_expected = data[c_value];
            let c_url = new EffURL(c_value);
            let c_gotten = c_url.fullGet();
            let c_result = c_gotten === c_expected;
            if (c_result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_value, 'result' : Effcore.getTranslation('success')}) );
            if (c_result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_value, 'result' : Effcore.getTranslation('failure')}) );
            if (c_result !== true) {
                c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : c_expected}) );
                c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : c_gotten}) );
                c_results['return'] = 0;
                return;
            }
        }
    }

    static test_step_code__tiny_get(test, dpath, c_results) {
        let protocol = window.location.protocol.replace(/[:]*$/g, '');
        let domain = window.location.host;
        let data = {};
            data[                            '/'                                ] = '/';
            data[                            '/?key=value'                      ] = '/?key=value';
            data[                            '/#anchor'                         ] = '/#anchor';
            data[                            '/?key=value#anchor'               ] = '/?key=value#anchor';
            data[                            '/dir/subdir/page'                 ] = '/dir/subdir/page';
            data[                            '/dir/subdir/page?key=value'       ] = '/dir/subdir/page?key=value';
            data[                            '/dir/subdir/page#anchor'          ] = '/dir/subdir/page#anchor';
            data[                            '/dir/subdir/page?key=value#anchor'] = '/dir/subdir/page?key=value#anchor';
            data[                   domain                                      ] = '/';
            data[                   domain + '/?key=value'                      ] = '/?key=value';
            data[                   domain + '/#anchor'                         ] = '/#anchor';
            data[                   domain + '/?key=value#anchor'               ] = '/?key=value#anchor';
            data[                   domain + '/dir/subdir/page'                 ] = '/dir/subdir/page';
            data[                   domain + '/dir/subdir/page?key=value'       ] = '/dir/subdir/page?key=value';
            data[                   domain + '/dir/subdir/page#anchor'          ] = '/dir/subdir/page#anchor';
            data[                   domain + '/dir/subdir/page?key=value#anchor'] = '/dir/subdir/page?key=value#anchor';
            data[protocol + '://' + domain                                      ] = '/';
            data[protocol + '://' + domain + '/?key=value'                      ] = '/?key=value';
            data[protocol + '://' + domain + '/#anchor'                         ] = '/#anchor';
            data[protocol + '://' + domain + '/?key=value#anchor'               ] = '/?key=value#anchor';
            data[protocol + '://' + domain + '/dir/subdir/page'                 ] = '/dir/subdir/page';
            data[protocol + '://' + domain + '/dir/subdir/page?key=value'       ] = '/dir/subdir/page?key=value';
            data[protocol + '://' + domain + '/dir/subdir/page#anchor'          ] = '/dir/subdir/page#anchor';
            data[protocol + '://' + domain + '/dir/subdir/page?key=value#anchor'] = '/dir/subdir/page?key=value#anchor';

        for (let c_value in data) {
            let c_expected = data[c_value];
            let c_url = new EffURL(c_value);
            let c_gotten = c_url.tinyGet();
            let c_result = c_gotten === c_expected;
            if (c_result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_value, 'result' : Effcore.getTranslation('success')}) );
            if (c_result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_value, 'result' : Effcore.getTranslation('failure')}) );
            if (c_result !== true) {
                c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : c_expected}) );
                c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : c_gotten}) );
                c_results['return'] = 0;
                return;
            }
        }
    }

    static test_step_code__query_args(test, dpath, c_results) {
        let url, data, value, gotten, expected, result;

        //////////////////
        /// buildQuery ///
        //////////////////

        data = {
            'main' : {
                'first name' : 'John Doe',
                'birthday' : '01/02/2003'
            },
            'pastimes' : {0 : 'moto', 1 : 'bike'},
            'children' : {
                'Baby Doe Jr.' : {'age' : 10},
                'Baby Doe' : {'age' : 20}
            },
            0 : 'programmer',
            'encode_[]=_key' : 'encode_[]=_value',
            'ключ 1' : 'значение 1',
            'no_value' : ''
        };

        expected = '0=programmer&' +
                   'main[first%20name]=John%20Doe&' +
                   'main[birthday]=01%2F02%2F2003&' +
                   'pastimes[0]=moto&' +
                   'pastimes[1]=bike&' +
                   'children[Baby%20Doe%20Jr.][age]=10&' +
                   'children[Baby%20Doe][age]=20&' +
                   'encode_%5B%5D%3D_key=encode_%5B%5D%3D_value&' +
                   '%D0%BA%D0%BB%D1%8E%D1%87%201=%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D0%B5%201&' +
                   'no_value=';

        gotten = EffURL.buildQuery(data);
        result = gotten === expected;
        if (result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : 'buildQuery', 'result' : Effcore.getTranslation('success')}) );
        if (result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : 'buildQuery', 'result' : Effcore.getTranslation('failure')}) );
        if (result !== true) {
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : expected}) );
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : gotten}) );
            c_results['return'] = 0;
            return;
        }

        /////////////////
        /// parse_str ///
        /////////////////

        data = {
            ''                                                        : {},
            '&'                                                       : {},
            '='                                                       : {},
            '=value_1'                                                : {},
            'key_1'                                                   : {'key_1' : ''},
            'key_1='                                                  : {'key_1' : ''},
            'key_1=value_1'                                           : {'key_1' : 'value_1'},
            'key_1=value_1=still_value_1'                             : {'key_1' : 'value_1=still_value_1'},
            'key_1=value_1%3Dstill_value_1'                           : {'key_1' : 'value_1=still_value_1'},
            '&&&key_2'                                                : {'key_2' : ''},
            '&key_2'                                                  : {'key_2' : ''},
            'key_1&'                                                  : {'key_1' : ''},
            'key_1&key_2'                                             : {'key_1' : '', 'key_2' : ''},
            'key_1=value_1&key_arr[]=value_2&key_arr[]=value_3'       : {'key_1' : 'value_1', 'key_arr' : {   0  : 'value_2',    1  : 'value_3'}},
            'key_1=value_1&key_arr[0]=value_2&key_arr[1]=value_3'     : {'key_1' : 'value_1', 'key_arr' : {   0  : 'value_2',    1  : 'value_3'}},
            'key_1=value_1&key_arr[2]=value_2&key_arr[3]=value_3'     : {'key_1' : 'value_1', 'key_arr' : {   2  : 'value_2',    3  : 'value_3'}},
            'key_1=value_1&key_arr[k-1]=value_2&key_arr[k-2]=value_3' : {'key_1' : 'value_1', 'key_arr' : {'k-1' : 'value_2', 'k-2' : 'value_3'}},
            'key_1=value_1&key_arr[]=value_2&key_arr[k-2]=value_3'    : {'key_1' : 'value_1', 'key_arr' : {   0  : 'value_2', 'k-2' : 'value_3'}},
            'key_1=value_1&key_arr[k-1]=value_2&key_arr[]=value_3'    : {'key_1' : 'value_1', 'key_arr' : {'k-1' : 'value_2',    0  : 'value_3'}},
            'key_arr[][]=value_1'                                     : {'key_arr'  : {0 :           {0 : 'value_1'}}},
            'key_arr[][][]=value_1'                                   : {'key_arr'  : {0 : {0 :      {0 : 'value_1'}}}},
            'key_arr[][][][]=value_1'                                 : {'key_arr'  : {0 : {0 : {0 : {0 : 'value_1'}}}}},
            'key_arr=value_1&key_arr[]=value_2'                       : {'key_arr'  : {0 :                'value_2'}},
            'key_arr[]=value_1&key_arr=value_2'                       : {'key_arr'  :                     'value_2'},
            'key_arr[=value_1'                                        : {'key_arr[' :                     'value_1'},
            'key_arr]=value_1'                                        : {'key_arr]' :                     'value_1'},
            '%D0%BA%D0%BB%D1%8E%D1%87=%D0%BC%D0%BE%D1%91%20%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D0%B5' : {'ключ' : 'моё значение'},
            '%D0%BA%D0%BB%D1%8E%D1%87=%D0%BC%D0%BE%D1%91%2B%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D0%B5' : {'ключ' : 'моё+значение'}
        };

        for (let c_value in data) {
            let c_expected = data[c_value];
            let c_gotten = EffURL.parseQuery(c_value);
            let c_result = JSON.stringify(c_gotten) === JSON.stringify(c_expected);
            if (c_result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (c_value.length < 80 ? c_value : c_value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('success')}) );
            if (c_result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (c_value.length < 80 ? c_value : c_value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('failure')}) );
            if (c_result !== true) {
                c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : JSON.stringify(c_expected)}) );
                c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : JSON.stringify(c_gotten)}) );
                c_results['return'] = 0;
                return;
            }
        }

        //////////////////////
        /// queryArgSelect ///
        //////////////////////

        value = 'http://example.com/?scalar=encode_%5B%5D%3D_value&array[0]=value%201&array[string]=value%202&%D0%BA%D0%BB%D1%8E%D1%87_1=%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D0%B5%201';
        url = new EffURL(value);
        expected = 'encode_[]=_value';
        gotten = url.queryArgSelect('scalar');
        result = gotten === expected;
        if (result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('success')}) );
        if (result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('failure')}) );
        if (result !== true) {
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : expected}) );
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : gotten}) );
            c_results['return'] = 0;
            return;
        }

        expected = {0 : 'value 1', 'string' : 'value 2'};
        gotten = url.queryArgSelect('array');
        result = JSON.stringify(gotten) === JSON.stringify(expected);
        if (result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('success')}) );
        if (result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('failure')}) );
        if (result !== true) {
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : JSON.stringify(expected)}) );
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : JSON.stringify(gotten)}) );
            c_results['return'] = 0;
            return;
        }

        expected = 'значение 1';
        gotten = url.queryArgSelect('ключ_1');
        result = JSON.stringify(gotten) === JSON.stringify(expected);
        if (result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('success')}) );
        if (result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('failure')}) );
        if (result !== true) {
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : JSON.stringify(expected)}) );
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : JSON.stringify(gotten)}) );
            c_results['return'] = 0;
            return;
        }

        //////////////////////
        /// queryArgInsert ///
        //////////////////////

        value = 'http://example.com/';
        expected = 'http://example.com/?scalar=encode_%5B%5D%3D_value';
        url = new EffURL(value);
        url.queryArgInsert('scalar', 'encode_[]=_value');
        gotten = url.fullGet();
        result = gotten === expected;
        if (result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('success')}) );
        if (result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('failure')}) );
        if (result !== true) {
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : expected}) );
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : gotten}) );
            c_results['return'] = 0;
            return;
        }

        value = 'http://example.com/';
        expected = 'http://example.com/?array[0]=value%201&array[string]=value%202';
        url = new EffURL(value);
        url.queryArgInsert('array', {0 : 'value 1', 'string' : 'value 2'});
        gotten = url.fullGet();
        result = gotten === expected;
        if (result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('success')}) );
        if (result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('failure')}) );
        if (result !== true) {
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : expected}) );
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : gotten}) );
            c_results['return'] = 0;
            return;
        }

        value = 'http://example.com/';
        expected = 'http://example.com/?scalar=encode_%5B%5D%3D_value&array[0]=value%201&array[string]=value%202';
        url = new EffURL(value);
        url.queryArgInsert('scalar', 'encode_[]=_value');
        url.queryArgInsert('array', {0 : 'value 1', 'string' : 'value 2'});
        gotten = url.fullGet();
        result = gotten === expected;
        if (result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('success')}) );
        if (result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('failure')}) );
        if (result !== true) {
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : expected}) );
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : gotten}) );
            c_results['return'] = 0;
            return;
        }

        value = 'http://example.com/';
        expected = 'http://example.com/?%D0%BA%D0%BB%D1%8E%D1%87_1=%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D0%B5%201';
        url = new EffURL(value);
        url.queryArgInsert('ключ_1', 'значение 1');
        gotten = url.fullGet();
        result = gotten === expected;
        if (result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('success')}) );
        if (result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('failure')}) );
        if (result !== true) {
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : expected}) );
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : gotten}) );
            c_results['return'] = 0;
            return;
        }

        ////////////////////////////////
        /// queryArgInsert (replace) ///
        ////////////////////////////////

        value = 'http://example.com/?scalar=encode_%5B%5D%3D_value';
        expected = 'http://example.com/?scalar=encode_%5B%5D%3D_new_value';
        url = new EffURL(value);
        url.queryArgInsert('scalar', 'encode_[]=_new_value');
        gotten = url.fullGet();
        result = gotten === expected;
        if (result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('success')}) );
        if (result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('failure')}) );
        if (result !== true) {
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : expected}) );
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : gotten}) );
            c_results['return'] = 0;
            return;
        }

        value = 'http://example.com/?array[0]=value%201&array[string]=value%202';
        expected = 'http://example.com/?array[0]=new%20value%201&array[string]=new%20value%202';
        url = new EffURL(value);
        url.queryArgInsert('array', {0 : 'new value 1', 'string' : 'new value 2'});
        gotten = url.fullGet();
        result = gotten === expected;
        if (result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('success')}) );
        if (result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('failure')}) );
        if (result !== true) {
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : expected}) );
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : gotten}) );
            c_results['return'] = 0;
            return;
        }

        value = 'http://example.com/?scalar=encode_%5B%5D%3D_value&array[0]=value%201&array[string]=value%202';
        expected = 'http://example.com/?scalar=encode_%5B%5D%3D_new_value&array[0]=new%20value%201&array[string]=new%20value%202';
        url = new EffURL(value);
        url.queryArgInsert('scalar', 'encode_[]=_new_value');
        url.queryArgInsert('array', {0 : 'new value 1', 'string' : 'new value 2'});
        gotten = url.fullGet();
        result = gotten === expected;
        if (result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('success')}) );
        if (result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('failure')}) );
        if (result !== true) {
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : expected}) );
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : gotten}) );
            c_results['return'] = 0;
            return;
        }

        value = 'http://example.com/?%D0%BA%D0%BB%D1%8E%D1%87_1=%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D0%B5%201';
        expected = 'http://example.com/?%D0%BA%D0%BB%D1%8E%D1%87_1=%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D0%B5%202';
        url = new EffURL(value);
        url.queryArgInsert('ключ_1', 'значение 2');
        gotten = url.fullGet();
        result = gotten === expected;
        if (result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('success')}) );
        if (result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('failure')}) );
        if (result !== true) {
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : expected}) );
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : gotten}) );
            c_results['return'] = 0;
            return;
        }

        //////////////////////
        /// queryArgDelete ///
        //////////////////////

        value = 'http://example.com/?scalar=encode_%5B%5D%3D_value&array[0]=value%201&array[string]=value%202';
        expected = 'http://example.com/?array[0]=value%201&array[string]=value%202';
        url = new EffURL(value);
        url.queryArgDelete('scalar');
        gotten = url.fullGet();
        result = gotten === expected;
        if (result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('success')}) );
        if (result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('failure')}) );
        if (result !== true) {
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : expected}) );
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : gotten}) );
            c_results['return'] = 0;
            return;
        }

        value = 'http://example.com/?scalar=encode_%5B%5D%3D_value&array[0]=value%201&array[string]=value%202';
        expected = 'http://example.com/?scalar=encode_%5B%5D%3D_value';
        url = new EffURL(value);
        url.queryArgDelete('array');
        gotten = url.fullGet();
        result = gotten === expected;
        if (result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('success')}) );
        if (result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('failure')}) );
        if (result !== true) {
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : expected}) );
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : gotten}) );
            c_results['return'] = 0;
            return;
        }

        value = 'http://example.com/?scalar=encode_%5B%5D%3D_value&array[0]=value%201&array[string]=value%202';
        expected = 'http://example.com';
        url = new EffURL(value);
        url.queryArgDelete('scalar');
        url.queryArgDelete('array');
        gotten = url.fullGet();
        result = gotten === expected;
        if (result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('success')}) );
        if (result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('failure')}) );
        if (result !== true) {
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : expected}) );
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : gotten}) );
            c_results['return'] = 0;
            return;
        }

        value = 'http://example.com/?%D0%BA%D0%BB%D1%8E%D1%87_1=%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D0%B5%201';
        expected = 'http://example.com';
        url = new EffURL(value);
        url.queryArgDelete('ключ_1');
        gotten = url.fullGet();
        result = gotten === expected;
        if (result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('success')}) );
        if (result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : (value.length < 80 ? value : value.substring(0, 80) + '…'), 'result' : Effcore.getTranslation('failure')}) );
        if (result !== true) {
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : expected}) );
            c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : gotten}) );
            c_results['return'] = 0;
            return;
        }
    }

}
