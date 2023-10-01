<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use effcore\Pager;
use effcore\Text;

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

    static function test_step_code__build(&$test, $dpath, &$c_results) {
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
            $c_gotten = $c_info['max'] === true &&
                        $c_info['cur'] === true &&
                        $c_info['err'] === true;
            $c_expected = true;
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"', ['value' => $c_expected ? 'true' : 'false']);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => $c_gotten ? 'true' : 'false']);
                $c_results['return'] = 0;
                return;
            }
        }
    }

}
