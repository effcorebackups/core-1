<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Test_step_Request {

    public $url;
    public $is_https = false;
    public $proxy    = '';
    public $headers  = [];
    public $post     = [];
    static $history  = [];

    function run(&$test, $dpath, &$c_results) {
        $proxy = $this->proxy instanceof Param_from_form ?
                 $this->proxy->get() :
                 $this->proxy;
        $prepared_url     = $this->prepared_url_get    ();
        $prepared_headers = $this->prepared_headers_get();
        $prepared_post    = $this->prepared_post_get   ();
                    $c_results['reports'][$dpath]['dpath'] = '### dpath: '.$dpath;
                    $c_results['reports'][$dpath][] = new Text('make request to "%%_url"', ['url'   => $this->prepared_url_get()]);
        if ($proxy) $c_results['reports'][$dpath][] = new Text('proxy server = %%_proxy',  ['proxy' => $proxy]);
        foreach ($prepared_headers as           $c_value) $c_results['reports'][$dpath][] = new Text('&ndash; request header param "%%_value"',           [                  'value' => $c_value]);
        foreach ($prepared_post    as $c_key => $c_value) $c_results['reports'][$dpath][] = new Text('&ndash; request post param "%%_name" = "%%_value"', ['name' => $c_key, 'value' => $c_value]);
        # make request
        $response = Request::make(
            $prepared_url,
            $prepared_headers,
            $prepared_post, ['proxy' => $proxy]);
        if (isset($response['info'   ]['http_code'                 ])) $c_results['reports'][$dpath][] = new Text('&ndash; response '.    'param "%%_name" = "%%_value"', ['name' => 'http_code',                  'value' => $response['info'   ]['http_code'   ]]);
        if (isset($response['info'   ]['primary_ip'                ])) $c_results['reports'][$dpath][] = new Text('&ndash; response '.    'param "%%_name" = "%%_value"', ['name' => 'primary_ip',                 'value' => $response['info'   ]['primary_ip'  ]]);
        if (isset($response['info'   ]['primary_port'              ])) $c_results['reports'][$dpath][] = new Text('&ndash; response '.    'param "%%_name" = "%%_value"', ['name' => 'primary_port',               'value' => $response['info'   ]['primary_port']]);
        if (isset($response['info'   ]['local_ip'                  ])) $c_results['reports'][$dpath][] = new Text('&ndash; response '.    'param "%%_name" = "%%_value"', ['name' => 'local_ip',                   'value' => $response['info'   ]['local_ip'    ]]);
        if (isset($response['info'   ]['local_port'                ])) $c_results['reports'][$dpath][] = new Text('&ndash; response '.    'param "%%_name" = "%%_value"', ['name' => 'local_port',                 'value' => $response['info'   ]['local_port'  ]]);
        if (isset($response['headers']['X-PHP-Memory-usage'        ])) $c_results['reports'][$dpath][] = new Text('&ndash; response header param "%%_name" = "%%_value"', ['name' => 'X-PHP-Memory-usage',         'value' => $response['headers']['X-PHP-Memory-usage'].' ('.Locale::format_bytes  ($response['headers']['X-PHP-Memory-usage']).')' ]);
        if (isset($response['headers']['X-Time-total'              ])) $c_results['reports'][$dpath][] = new Text('&ndash; response header param "%%_name" = "%%_value"', ['name' => 'X-Time-total',               'value' => $response['headers']['X-Time-total'      ].' ('.Locale::format_msecond($response['headers']['X-Time-total'      ]).')' ]);
        if (isset($response['headers']['X-Form-Submit-Errors-Count'])) $c_results['reports'][$dpath][] = new Text('&ndash; response header param "%%_name" = "%%_value"', ['name' => 'X-Form-Submit-Errors-Count', 'value' => $response['headers']['X-Form-Submit-Errors-Count'] ]);
        if (isset($response['headers']['X-Return-level'            ])) $c_results['reports'][$dpath][] = new Text('&ndash; response header param "%%_name" = "%%_value"', ['name' => 'X-Return-level',             'value' => $response['headers']['X-Return-level'            ] ]);
        if (isset($response['headers']['Location'                  ])) $c_results['reports'][$dpath][] = new Text('&ndash; response header param "%%_name" = "%%_value"', ['name' => 'Location',                   'value' => $response['headers']['Location'                  ] ]);
        if (isset($response['headers']['Content-Length'            ])) $c_results['reports'][$dpath][] = new Text('&ndash; response header param "%%_name" = "%%_value"', ['name' => 'Content-Length',             'value' => $response['headers']['Content-Length'            ] ]);
        if (isset($response['headers']['Set-Cookie'                ])) {
            foreach ($response['headers']['Set-Cookie'] as $c_cookie) {
                $c_results['reports'][$dpath][] = new Text('&ndash; response header param "%%_name" = "%%_value"', ['name' => 'Set-Cookie', 'value' => $c_cookie['raw']]);
            }
        }
        $c_results['response'] = $response;
        static::$history[    ] = $response;
    }

    function prepared_url_get() {
        $is_https = $this->is_https instanceof Param_from_form ?
                    $this->is_https->get() :
                    $this->is_https;
        return ($is_https ? 'https' : 'http').'://'.Url::get_current()->domain.$this->url;
    }

    function prepared_headers_get() {
        $result = [];
        foreach ($this->headers as $c_key => $c_value)
            if (is_string($c_value))
                $result[$c_key] = Token::apply($c_value);
        return $result;
    }

    function prepared_post_get() {
        $result = [];
        foreach ($this->post as $c_key => $c_value)
            if (is_string($c_value))
                $result[$c_key] = Token::apply($c_value);
        return $result;
    }

}
