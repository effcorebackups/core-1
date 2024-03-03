<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use effcore\Frontend;
use effcore\Request;
use effcore\Test;
use effcore\Text;

abstract class Events_Test__Class_Frontend {

    static function test_step_code__path_resolve(&$test, $dpath) {
        $protocol = Request::scheme_get();
        $domain = Request::host_get();

        $data = [
            ['path' => 'http://example.com/page.css',            'module_id' =>  null,  'return_absolute' => false],
            ['path' => 'http://example.com/page.css',            'module_id' =>  null,  'return_absolute' => true ],
            ['path' => 'frontend/page.cssd',                     'module_id' => 'page', 'return_absolute' => false],
            ['path' => 'frontend/page.cssd',                     'module_id' => 'page', 'return_absolute' => true ],
            ['path' => '/system/module_page/frontend/page.cssd', 'module_id' => 'page', 'return_absolute' => false],
            ['path' => '/system/module_page/frontend/page.cssd', 'module_id' => 'page', 'return_absolute' => true ],
        ];

        $expected = [
            'http://example.com/page.css',
            'http://example.com/page.css',
                                   '/system/module_page/frontend/page.cssd',
            $protocol.'://'.$domain.'/system/module_page/frontend/page.cssd',
                                    '/system/module_page/frontend/page.cssd',
            $protocol.'://'.$domain.'/system/module_page/frontend/page.cssd',
        ];

        foreach ($data as $c_row_id => $c_info) {
            $с_received = Frontend::path_resolve(
                $c_info['path'],
                $c_info['module_id'],
                $c_info['return_absolute']
            );
            $c_expected = $expected[$c_row_id];
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_info['path'], 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_info['path'], 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }
    }

}
