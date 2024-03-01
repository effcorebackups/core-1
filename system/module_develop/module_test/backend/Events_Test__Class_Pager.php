<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use effcore\Core;
use effcore\Pager;
use effcore\Test;
use effcore\Text;
use effcore\URL;

abstract class Events_Test__Class_Pager {

    #                                             ERR_CODE_MAX_LT_MIN ──────────────────┐
    #                                             ERR_CODE_CUR_GT_MAX ──────────────┐   │
    #                                             ERR_CODE_CUR_LT_MIN ──────────┐   │   │
    #                                             ERR_CODE_CUR_NO_INT ──────┐   │   │   │
    #                                                     ERR_CODE_OK ──┐   │   │   │   │
    #                                                                   ▼   ▼   ▼   ▼   ▼
    # ──────────────────────────────────────┬────────────┬────────────┬───┬───┬───┬───┬───┐
    # http://domain/path                    │ max: 0 → 1 │ out cur: 1 │   │   │   │   │ + │
    # http://domain/path?page               │ max: 0 → 1 │ out cur: 1 │   │   │   │   │ + │
    # http://domain/path?page=-1            │ max: 0 → 1 │ out cur: 1 │   │   │ + │   │ + │
    # http://domain/path?page=0             │ max: 0 → 1 │ out cur: 1 │   │   │ + │   │ + │
    # http://domain/path?page=1             │ max: 0 → 1 │ out cur: 1 │   │   │   │   │ + │
    # http://domain/path?page=3             │ max: 0 → 1 │ out cur: 1 │   │   │   │ + │ + │
    # http://domain/path?page=value         │ max: 0 → 1 │ out cur: 1 │   │ + │   │   │ + │
    # http://domain/path?page[]=            │ max: 0 → 1 │ out cur: 1 │   │   │   │   │ + │
    # http://domain/path?page[]=-1          │ max: 0 → 1 │ out cur: 1 │   │   │ + │   │ + │
    # http://domain/path?page[]=0           │ max: 0 → 1 │ out cur: 1 │   │   │ + │   │ + │
    # http://domain/path?page[]=1           │ max: 0 → 1 │ out cur: 1 │   │   │   │   │ + │
    # http://domain/path?page[]=3           │ max: 0 → 1 │ out cur: 1 │   │   │   │ + │ + │
    # http://domain/path?page[]=value       │ max: 0 → 1 │ out cur: 1 │   │ + │   │   │ + │
    # http://domain/path?page[1]=           │ max: 0 → 1 │ out cur: 1 │   │   │   │   │ + │
    # http://domain/path?page[1]=-1         │ max: 0 → 1 │ out cur: 1 │   │   │   │   │ + │
    # http://domain/path?page[1]=0          │ max: 0 → 1 │ out cur: 1 │   │   │   │   │ + │
    # http://domain/path?page[1]=1          │ max: 0 → 1 │ out cur: 1 │   │   │   │   │ + │
    # http://domain/path?page[1]=3          │ max: 0 → 1 │ out cur: 1 │   │   │   │   │ + │
    # http://domain/path?page[1]=value      │ max: 0 → 1 │ out cur: 1 │   │   │   │   │ + │
    # ──────────────────────────────────────┼────────────┼────────────┼───┼───┼───┼───┼───┤
    # http://domain/path                    │ max: 1 → 1 │ out cur: 1 │ + │   │   │   │   │
    # http://domain/path?page               │ max: 1 → 1 │ out cur: 1 │ + │   │   │   │   │
    # http://domain/path?page=-1            │ max: 1 → 1 │ out cur: 1 │   │   │ + │   │   │
    # http://domain/path?page=0             │ max: 1 → 1 │ out cur: 1 │   │   │ + │   │   │
    # http://domain/path?page=1             │ max: 1 → 1 │ out cur: 1 │ + │   │   │   │   │
    # http://domain/path?page=3             │ max: 1 → 1 │ out cur: 1 │   │   │   │ + │   │
    # http://domain/path?page=value         │ max: 1 → 1 │ out cur: 1 │   │ + │   │   │   │
    # http://domain/path?page[]=            │ max: 1 → 1 │ out cur: 1 │ + │   │   │   │   │
    # http://domain/path?page[]=-1          │ max: 1 → 1 │ out cur: 1 │   │   │ + │   │   │
    # http://domain/path?page[]=0           │ max: 1 → 1 │ out cur: 1 │   │   │ + │   │   │
    # http://domain/path?page[]=1           │ max: 1 → 1 │ out cur: 1 │ + │   │   │   │   │
    # http://domain/path?page[]=3           │ max: 1 → 1 │ out cur: 1 │   │   │   │ + │   │
    # http://domain/path?page[]=value       │ max: 1 → 1 │ out cur: 1 │   │ + │   │   │   │
    # http://domain/path?page[1]=           │ max: 1 → 1 │ out cur: 1 │ + │   │   │   │   │
    # http://domain/path?page[1]=-1         │ max: 1 → 1 │ out cur: 1 │ + │   │   │   │   │
    # http://domain/path?page[1]=0          │ max: 1 → 1 │ out cur: 1 │ + │   │   │   │   │
    # http://domain/path?page[1]=1          │ max: 1 → 1 │ out cur: 1 │ + │   │   │   │   │
    # http://domain/path?page[1]=3          │ max: 1 → 1 │ out cur: 1 │ + │   │   │   │   │
    # http://domain/path?page[1]=value      │ max: 1 → 1 │ out cur: 1 │ + │   │   │   │   │
    # ──────────────────────────────────────┼────────────┼────────────┼───┼───┼───┼───┼───┤
    # http://domain/path                    │ max: 2 → 2 │ out cur: 1 │ + │   │   │   │   │
    # http://domain/path?page               │ max: 2 → 2 │ out cur: 1 │ + │   │   │   │   │
    # http://domain/path?page=-1            │ max: 2 → 2 │ out cur: 1 │   │   │ + │   │   │
    # http://domain/path?page=0             │ max: 2 → 2 │ out cur: 1 │   │   │ + │   │   │
    # http://domain/path?page=1             │ max: 2 → 2 │ out cur: 1 │ + │   │   │   │   │
    # http://domain/path?page=3             │ max: 2 → 2 │ out cur: 2 │   │   │   │ + │   │
    # http://domain/path?page=value         │ max: 2 → 2 │ out cur: 1 │   │ + │   │   │   │
    # http://domain/path?page[]=            │ max: 2 → 2 │ out cur: 1 │ + │   │   │   │   │
    # http://domain/path?page[]=-1          │ max: 2 → 2 │ out cur: 1 │   │   │ + │   │   │
    # http://domain/path?page[]=0           │ max: 2 → 2 │ out cur: 1 │   │   │ + │   │   │
    # http://domain/path?page[]=1           │ max: 2 → 2 │ out cur: 1 │ + │   │   │   │   │
    # http://domain/path?page[]=3           │ max: 2 → 2 │ out cur: 2 │   │   │   │ + │   │
    # http://domain/path?page[]=value       │ max: 2 → 2 │ out cur: 1 │   │ + │   │   │   │
    # http://domain/path?page[1]=           │ max: 2 → 2 │ out cur: 1 │ + │   │   │   │   │
    # http://domain/path?page[1]=-1         │ max: 2 → 2 │ out cur: 1 │ + │   │   │   │   │
    # http://domain/path?page[1]=0          │ max: 2 → 2 │ out cur: 1 │ + │   │   │   │   │
    # http://domain/path?page[1]=1          │ max: 2 → 2 │ out cur: 1 │ + │   │   │   │   │
    # http://domain/path?page[1]=3          │ max: 2 → 2 │ out cur: 1 │ + │   │   │   │   │
    # http://domain/path?page[1]=value      │ max: 2 → 2 │ out cur: 1 │ + │   │   │   │   │
    # ──────────────────────────────────────┴────────────┴────────────┴───┴───┴───┴───┴───┘

    static function test_step_code__build(&$test, $dpath) {

        global $_GET;
        $ORIGINAL = $_GET;

        $result = [];

        $_GET = [];                       $pager = new Pager(1, 0);  $pager->build();  $result['1_0_undefined']            = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_MAX_LT_MIN                             ) ];
        $_GET['page'] = '';               $pager = new Pager(1, 0);  $pager->build();  $result['1_0_string_empty']         = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_MAX_LT_MIN                             ) ];
        $_GET['page'] = '-1';             $pager = new Pager(1, 0);  $pager->build();  $result['1_0_string-1']             = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_MAX_LT_MIN | pager::ERR_CODE_CUR_LT_MIN) ];
        $_GET['page'] = '0';              $pager = new Pager(1, 0);  $pager->build();  $result['1_0_string+0']             = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_MAX_LT_MIN | pager::ERR_CODE_CUR_LT_MIN) ];
        $_GET['page'] = '1';              $pager = new Pager(1, 0);  $pager->build();  $result['1_0_string+1']             = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_MAX_LT_MIN                             ) ];
        $_GET['page'] = '3';              $pager = new Pager(1, 0);  $pager->build();  $result['1_0_string+3']             = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_MAX_LT_MIN | pager::ERR_CODE_CUR_GT_MAX) ];
        $_GET['page'] = 'value';          $pager = new Pager(1, 0);  $pager->build();  $result['1_0_string_value']         = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_MAX_LT_MIN | pager::ERR_CODE_CUR_NO_INT) ];
        $_GET['page'] = [];               $pager = new Pager(1, 0);  $pager->build();  $result['1_0_array_0_empty']        = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_MAX_LT_MIN                             ) ];
        $_GET['page'] = [0 => ''];        $pager = new Pager(1, 0);  $pager->build();  $result['1_0_array_0_string_empty'] = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_MAX_LT_MIN                             ) ];
        $_GET['page'] = [0 => '-1'];      $pager = new Pager(1, 0);  $pager->build();  $result['1_0_array_0_string-1']     = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_MAX_LT_MIN | pager::ERR_CODE_CUR_LT_MIN) ];
        $_GET['page'] = [0 => '0'];       $pager = new Pager(1, 0);  $pager->build();  $result['1_0_array_0_string+0']     = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_MAX_LT_MIN | pager::ERR_CODE_CUR_LT_MIN) ];
        $_GET['page'] = [0 => '1'];       $pager = new Pager(1, 0);  $pager->build();  $result['1_0_array_0_string+1']     = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_MAX_LT_MIN                             ) ];
        $_GET['page'] = [0 => '3'];       $pager = new Pager(1, 0);  $pager->build();  $result['1_0_array_0_string+3']     = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_MAX_LT_MIN | pager::ERR_CODE_CUR_GT_MAX) ];
        $_GET['page'] = [0 => 'value'];   $pager = new Pager(1, 0);  $pager->build();  $result['1_0_array_0_string_value'] = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_MAX_LT_MIN | pager::ERR_CODE_CUR_NO_INT) ];
        $_GET['page'] = [1 => ''];        $pager = new Pager(1, 0);  $pager->build();  $result['1_0_array_1_string_empty'] = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_MAX_LT_MIN                             ) ];
        $_GET['page'] = [1 => '-1'];      $pager = new Pager(1, 0);  $pager->build();  $result['1_0_array_1_string-1']     = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_MAX_LT_MIN                             ) ];
        $_GET['page'] = [1 => '0'];       $pager = new Pager(1, 0);  $pager->build();  $result['1_0_array_1_string+0']     = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_MAX_LT_MIN                             ) ];
        $_GET['page'] = [1 => '1'];       $pager = new Pager(1, 0);  $pager->build();  $result['1_0_array_1_string+1']     = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_MAX_LT_MIN                             ) ];
        $_GET['page'] = [1 => '3'];       $pager = new Pager(1, 0);  $pager->build();  $result['1_0_array_1_string+3']     = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_MAX_LT_MIN                             ) ];
        $_GET['page'] = [1 => 'value'];   $pager = new Pager(1, 0);  $pager->build();  $result['1_0_array_1_string_value'] = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_MAX_LT_MIN                             ) ];

        $_GET = [];                       $pager = new Pager(1, 1);  $pager->build();  $result['1_1_undefined']            = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_OK        ) ];
        $_GET['page'] = '';               $pager = new Pager(1, 1);  $pager->build();  $result['1_1_string_empty']         = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_OK        ) ];
        $_GET['page'] = '-1';             $pager = new Pager(1, 1);  $pager->build();  $result['1_1_string-1']             = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_CUR_LT_MIN) ];
        $_GET['page'] = '0';              $pager = new Pager(1, 1);  $pager->build();  $result['1_1_string+0']             = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_CUR_LT_MIN) ];
        $_GET['page'] = '1';              $pager = new Pager(1, 1);  $pager->build();  $result['1_1_string+1']             = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_OK        ) ];
        $_GET['page'] = '3';              $pager = new Pager(1, 1);  $pager->build();  $result['1_1_string+3']             = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_CUR_GT_MAX) ];
        $_GET['page'] = 'value';          $pager = new Pager(1, 1);  $pager->build();  $result['1_1_string_value']         = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_CUR_NO_INT) ];
        $_GET['page'] = [];               $pager = new Pager(1, 1);  $pager->build();  $result['1_1_array_0_empty']        = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_OK        ) ];
        $_GET['page'] = [0 => ''];        $pager = new Pager(1, 1);  $pager->build();  $result['1_1_array_0_string_empty'] = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_OK        ) ];
        $_GET['page'] = [0 => '-1'];      $pager = new Pager(1, 1);  $pager->build();  $result['1_1_array_0_string-1']     = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_CUR_LT_MIN) ];
        $_GET['page'] = [0 => '0'];       $pager = new Pager(1, 1);  $pager->build();  $result['1_1_array_0_string+0']     = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_CUR_LT_MIN) ];
        $_GET['page'] = [0 => '1'];       $pager = new Pager(1, 1);  $pager->build();  $result['1_1_array_0_string+1']     = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_OK        ) ];
        $_GET['page'] = [0 => '3'];       $pager = new Pager(1, 1);  $pager->build();  $result['1_1_array_0_string+3']     = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_CUR_GT_MAX) ];
        $_GET['page'] = [0 => 'value'];   $pager = new Pager(1, 1);  $pager->build();  $result['1_1_array_0_string_value'] = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_CUR_NO_INT) ];
        $_GET['page'] = [1 => ''];        $pager = new Pager(1, 1);  $pager->build();  $result['1_1_array_1_string_empty'] = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_OK        ) ];
        $_GET['page'] = [1 => '-1'];      $pager = new Pager(1, 1);  $pager->build();  $result['1_1_array_1_string-1']     = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_OK        ) ];
        $_GET['page'] = [1 => '0'];       $pager = new Pager(1, 1);  $pager->build();  $result['1_1_array_1_string+0']     = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_OK        ) ];
        $_GET['page'] = [1 => '1'];       $pager = new Pager(1, 1);  $pager->build();  $result['1_1_array_1_string+1']     = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_OK        ) ];
        $_GET['page'] = [1 => '3'];       $pager = new Pager(1, 1);  $pager->build();  $result['1_1_array_1_string+3']     = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_OK        ) ];
        $_GET['page'] = [1 => 'value'];   $pager = new Pager(1, 1);  $pager->build();  $result['1_1_array_1_string_value'] = ['max' => $pager->max === 1, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_OK        ) ];

        $_GET = [];                       $pager = new Pager(1, 2);  $pager->build();  $result['1_2_undefined']            = ['max' => $pager->max === 2, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_OK        ) ];
        $_GET['page'] = '';               $pager = new Pager(1, 2);  $pager->build();  $result['1_2_string_empty']         = ['max' => $pager->max === 2, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_OK        ) ];
        $_GET['page'] = '-1';             $pager = new Pager(1, 2);  $pager->build();  $result['1_2_string-1']             = ['max' => $pager->max === 2, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_CUR_LT_MIN) ];
        $_GET['page'] = '0';              $pager = new Pager(1, 2);  $pager->build();  $result['1_2_string+0']             = ['max' => $pager->max === 2, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_CUR_LT_MIN) ];
        $_GET['page'] = '1';              $pager = new Pager(1, 2);  $pager->build();  $result['1_2_string+1']             = ['max' => $pager->max === 2, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_OK        ) ];
        $_GET['page'] = '3';              $pager = new Pager(1, 2);  $pager->build();  $result['1_2_string+3']             = ['max' => $pager->max === 2, 'cur' => $pager->cur === 2, 'err' => $pager->error_code_get() === (pager::ERR_CODE_CUR_GT_MAX) ];
        $_GET['page'] = 'value';          $pager = new Pager(1, 2);  $pager->build();  $result['1_2_string_value']         = ['max' => $pager->max === 2, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_CUR_NO_INT) ];
        $_GET['page'] = [];               $pager = new Pager(1, 2);  $pager->build();  $result['1_2_array_0_empty']        = ['max' => $pager->max === 2, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_OK        ) ];
        $_GET['page'] = [0 => ''];        $pager = new Pager(1, 2);  $pager->build();  $result['1_2_array_0_string_empty'] = ['max' => $pager->max === 2, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_OK        ) ];
        $_GET['page'] = [0 => '-1'];      $pager = new Pager(1, 2);  $pager->build();  $result['1_2_array_0_string-1']     = ['max' => $pager->max === 2, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_CUR_LT_MIN) ];
        $_GET['page'] = [0 => '0'];       $pager = new Pager(1, 2);  $pager->build();  $result['1_2_array_0_string+0']     = ['max' => $pager->max === 2, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_CUR_LT_MIN) ];
        $_GET['page'] = [0 => '1'];       $pager = new Pager(1, 2);  $pager->build();  $result['1_2_array_0_string+1']     = ['max' => $pager->max === 2, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_OK        ) ];
        $_GET['page'] = [0 => '3'];       $pager = new Pager(1, 2);  $pager->build();  $result['1_2_array_0_string+3']     = ['max' => $pager->max === 2, 'cur' => $pager->cur === 2, 'err' => $pager->error_code_get() === (pager::ERR_CODE_CUR_GT_MAX) ];
        $_GET['page'] = [0 => 'value'];   $pager = new Pager(1, 2);  $pager->build();  $result['1_2_array_0_string_value'] = ['max' => $pager->max === 2, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_CUR_NO_INT) ];
        $_GET['page'] = [1 => ''];        $pager = new Pager(1, 2);  $pager->build();  $result['1_2_array_1_string_empty'] = ['max' => $pager->max === 2, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_OK        ) ];
        $_GET['page'] = [1 => '-1'];      $pager = new Pager(1, 2);  $pager->build();  $result['1_2_array_1_string-1']     = ['max' => $pager->max === 2, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_OK        ) ];
        $_GET['page'] = [1 => '0'];       $pager = new Pager(1, 2);  $pager->build();  $result['1_2_array_1_string+0']     = ['max' => $pager->max === 2, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_OK        ) ];
        $_GET['page'] = [1 => '1'];       $pager = new Pager(1, 2);  $pager->build();  $result['1_2_array_1_string+1']     = ['max' => $pager->max === 2, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_OK        ) ];
        $_GET['page'] = [1 => '3'];       $pager = new Pager(1, 2);  $pager->build();  $result['1_2_array_1_string+3']     = ['max' => $pager->max === 2, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_OK        ) ];
        $_GET['page'] = [1 => 'value'];   $pager = new Pager(1, 2);  $pager->build();  $result['1_2_array_1_string_value'] = ['max' => $pager->max === 2, 'cur' => $pager->cur === 1, 'err' => $pager->error_code_get() === (pager::ERR_CODE_OK        ) ];

        foreach ($result as $c_row_id => $c_info) {
            $с_received = $c_info['max'] === true &&
                          $c_info['cur'] === true &&
                          $c_info['err'] === true;
            $c_expected = true;
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }

        $_GET = $ORIGINAL;
    }

    static function test_step_code__url_get(&$test, $dpath) {
        global $_GET;
        $ORIGINAL = $_GET;

        if (Core::is_CLI()) {
            URL::set_current('http://example.com');
        }

        $url = clone URL::get_current();
        $url->query_arg_delete('page');
        $base_url = $url->relative_get();

        $incoming_states = [
            '[- |- |-]' => ['pager_0' => null, 'pager_2' => null, 'pager_x' => null], # http://example.com
            '[1 |- |-]' => ['pager_0' => 1   , 'pager_2' => null, 'pager_x' => null], # http://example.com
            '[50|- |-]' => ['pager_0' => 50  , 'pager_2' => null, 'pager_x' => null], # http://example.com ? page=50
            '[- |1 |-]' => ['pager_0' => null, 'pager_2' => 1   , 'pager_x' => null], # http://example.com
            '[1 |1 |-]' => ['pager_0' => 1   , 'pager_2' => 1   , 'pager_x' => null], # http://example.com
            '[50|1 |-]' => ['pager_0' => 50  , 'pager_2' => 1   , 'pager_x' => null], # http://example.com ? page=50
            '[- |60|-]' => ['pager_0' => null, 'pager_2' => 60  , 'pager_x' => null], # http://example.com ?              page[2]=60
            '[1 |60|-]' => ['pager_0' => 1   , 'pager_2' => 60  , 'pager_x' => null], # http://example.com ?              page[2]=60
            '[50|60|-]' => ['pager_0' => 50  , 'pager_2' => 60  , 'pager_x' => null], # http://example.com ? page[0]=50 & page[2]=60
            '[- |- |y]' => ['pager_0' => null, 'pager_2' => null, 'pager_x' => 'y' ], # http://example.com ?                           page[x]=y
            '[1 |- |y]' => ['pager_0' => 1   , 'pager_2' => null, 'pager_x' => 'y' ], # http://example.com ?                           page[x]=y
            '[50|- |y]' => ['pager_0' => 50  , 'pager_2' => null, 'pager_x' => 'y' ], # http://example.com ? page[0]=50 &              page[x]=y
            '[- |1 |y]' => ['pager_0' => null, 'pager_2' => 1   , 'pager_x' => 'y' ], # http://example.com ?                           page[x]=y
            '[1 |1 |y]' => ['pager_0' => 1   , 'pager_2' => 1   , 'pager_x' => 'y' ], # http://example.com ?                           page[x]=y
            '[50|1 |y]' => ['pager_0' => 50  , 'pager_2' => 1   , 'pager_x' => 'y' ], # http://example.com ? page[0]=50 &              page[x]=y
            '[- |60|y]' => ['pager_0' => null, 'pager_2' => 60  , 'pager_x' => 'y' ], # http://example.com ?              page[2]=60 & page[x]=y
            '[1 |60|y]' => ['pager_0' => 1   , 'pager_2' => 60  , 'pager_x' => 'y' ], # http://example.com ?              page[2]=60 & page[x]=y
            '[50|60|y]' => ['pager_0' => 50  , 'pager_2' => 60  , 'pager_x' => 'y' ], # http://example.com ? page[0]=50 & page[2]=60 & page[x]=y
        ];

        $result__pager_0__to_page_1 = [
            '[- |- |-]' => $base_url                 ,
            '[1 |- |-]' => $base_url                 ,
            '[50|- |-]' => $base_url                 ,
            '[- |1 |-]' => $base_url                 ,
            '[1 |1 |-]' => $base_url                 ,
            '[50|1 |-]' => $base_url                 ,
            '[- |60|-]' => $base_url.'?'.'page[2]=60',
            '[1 |60|-]' => $base_url.'?'.'page[2]=60',
            '[50|60|-]' => $base_url.'?'.'page[2]=60',
            '[- |- |y]' => $base_url                 ,
            '[1 |- |y]' => $base_url                 ,
            '[50|- |y]' => $base_url                 ,
            '[- |1 |y]' => $base_url                 ,
            '[1 |1 |y]' => $base_url                 ,
            '[50|1 |y]' => $base_url                 ,
            '[- |60|y]' => $base_url.'?'.'page[2]=60',
            '[1 |60|y]' => $base_url.'?'.'page[2]=60',
            '[50|60|y]' => $base_url.'?'.'page[2]=60',
        ];

        foreach ($incoming_states as $c_row_id => $c_state) {
            $_GET = [];
            if ($c_state['pager_0'] !== null) $_GET['page'][ 0 ] = $c_state['pager_0'];
            if ($c_state['pager_2'] !== null) $_GET['page'][ 2 ] = $c_state['pager_2'];
            if ($c_state['pager_x'] !== null) $_GET['page']['x'] = $c_state['pager_x'];

            $с_received = Pager::url_get('page', 0, 1);
            $c_expected = $result__pager_0__to_page_1[$c_row_id];
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'pager_0__to_page_1: '.$c_row_id.' = '.$c_expected, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'pager_0__to_page_1: '.$c_row_id.' = '.$c_expected, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $result__pager_0__to_page_5 = [
            '[- |- |-]' => $base_url.'?page=5'                    ,
            '[1 |- |-]' => $base_url.'?page=5'                    ,
            '[50|- |-]' => $base_url.'?page=5'                    ,
            '[- |1 |-]' => $base_url.'?page=5'                    ,
            '[1 |1 |-]' => $base_url.'?page=5'                    ,
            '[50|1 |-]' => $base_url.'?page=5'                    ,
            '[- |60|-]' => $base_url.'?page[0]=5'.'&'.'page[2]=60',
            '[1 |60|-]' => $base_url.'?page[0]=5'.'&'.'page[2]=60',
            '[50|60|-]' => $base_url.'?page[0]=5'.'&'.'page[2]=60',
            '[- |- |y]' => $base_url.'?page=5'                    ,
            '[1 |- |y]' => $base_url.'?page=5'                    ,
            '[50|- |y]' => $base_url.'?page=5'                    ,
            '[- |1 |y]' => $base_url.'?page=5'                    ,
            '[1 |1 |y]' => $base_url.'?page=5'                    ,
            '[50|1 |y]' => $base_url.'?page=5'                    ,
            '[- |60|y]' => $base_url.'?page[0]=5'.'&'.'page[2]=60',
            '[1 |60|y]' => $base_url.'?page[0]=5'.'&'.'page[2]=60',
            '[50|60|y]' => $base_url.'?page[0]=5'.'&'.'page[2]=60',
        ];

        foreach ($incoming_states as $c_row_id => $c_state) {
            $_GET = [];
            if ($c_state['pager_0'] !== null) $_GET['page'][ 0 ] = $c_state['pager_0'];
            if ($c_state['pager_2'] !== null) $_GET['page'][ 2 ] = $c_state['pager_2'];
            if ($c_state['pager_x'] !== null) $_GET['page']['x'] = $c_state['pager_x'];

            $с_received = Pager::url_get('page', 0, 5);
            $c_expected = $result__pager_0__to_page_5[$c_row_id];
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'pager_0__to_page_5: '.$c_row_id.' = '.$c_expected, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'pager_0__to_page_5: '.$c_row_id.' = '.$c_expected, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $result__pager_2__to_page_1 = [
            '[- |- |-]' => $base_url           ,
            '[1 |- |-]' => $base_url           ,
            '[50|- |-]' => $base_url.'?page=50',
            '[- |1 |-]' => $base_url           ,
            '[1 |1 |-]' => $base_url           ,
            '[50|1 |-]' => $base_url.'?page=50',
            '[- |60|-]' => $base_url           ,
            '[1 |60|-]' => $base_url           ,
            '[50|60|-]' => $base_url.'?page=50',
            '[- |- |y]' => $base_url           ,
            '[1 |- |y]' => $base_url           ,
            '[50|- |y]' => $base_url.'?page=50',
            '[- |1 |y]' => $base_url           ,
            '[1 |1 |y]' => $base_url           ,
            '[50|1 |y]' => $base_url.'?page=50',
            '[- |60|y]' => $base_url           ,
            '[1 |60|y]' => $base_url           ,
            '[50|60|y]' => $base_url.'?page=50',
        ];

        foreach ($incoming_states as $c_row_id => $c_state) {
            $_GET = [];
            if ($c_state['pager_0'] !== null) $_GET['page'][ 0 ] = $c_state['pager_0'];
            if ($c_state['pager_2'] !== null) $_GET['page'][ 2 ] = $c_state['pager_2'];
            if ($c_state['pager_x'] !== null) $_GET['page']['x'] = $c_state['pager_x'];

            $с_received = Pager::url_get('page', 2, 1);
            $c_expected = $result__pager_2__to_page_1[$c_row_id];
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'pager_2__to_page_1: '.$c_row_id.' = '.$c_expected, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'pager_2__to_page_1: '.$c_row_id.' = '.$c_expected, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $result__pager_2__to_page_6 = [
            '[- |- |-]' => $base_url.'?'.              'page[2]=6',
            '[1 |- |-]' => $base_url.'?'.              'page[2]=6',
            '[50|- |-]' => $base_url.'?page[0]=50'.'&'.'page[2]=6',
            '[- |1 |-]' => $base_url.'?'.              'page[2]=6',
            '[1 |1 |-]' => $base_url.'?'.              'page[2]=6',
            '[50|1 |-]' => $base_url.'?page[0]=50'.'&'.'page[2]=6',
            '[- |60|-]' => $base_url.'?'.              'page[2]=6',
            '[1 |60|-]' => $base_url.'?'.              'page[2]=6',
            '[50|60|-]' => $base_url.'?page[0]=50'.'&'.'page[2]=6',
            '[- |- |y]' => $base_url.'?'.              'page[2]=6',
            '[1 |- |y]' => $base_url.'?'.              'page[2]=6',
            '[50|- |y]' => $base_url.'?page[0]=50'.'&'.'page[2]=6',
            '[- |1 |y]' => $base_url.'?'.              'page[2]=6',
            '[1 |1 |y]' => $base_url.'?'.              'page[2]=6',
            '[50|1 |y]' => $base_url.'?page[0]=50'.'&'.'page[2]=6',
            '[- |60|y]' => $base_url.'?'.              'page[2]=6',
            '[1 |60|y]' => $base_url.'?'.              'page[2]=6',
            '[50|60|y]' => $base_url.'?page[0]=50'.'&'.'page[2]=6',
        ];

        foreach ($incoming_states as $c_row_id => $c_state) {
            $_GET = [];
            if ($c_state['pager_0'] !== null) $_GET['page'][ 0 ] = $c_state['pager_0'];
            if ($c_state['pager_2'] !== null) $_GET['page'][ 2 ] = $c_state['pager_2'];
            if ($c_state['pager_x'] !== null) $_GET['page']['x'] = $c_state['pager_x'];

            $с_received = Pager::url_get('page', 2, 6);
            $c_expected = $result__pager_2__to_page_6[$c_row_id];
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'pager_2__to_page_6: '.$c_row_id.' = '.$c_expected, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'pager_2__to_page_6: '.$c_row_id.' = '.$c_expected, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }

        $_GET = $ORIGINAL;
    }

}
