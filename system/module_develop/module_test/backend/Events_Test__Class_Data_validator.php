<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use const effcore\DIR_ROOT;
use effcore\Core;
use effcore\Data_validator;
use effcore\Test;
use effcore\Text_simple;
use effcore\Text;

abstract class Events_Test__Class_Data_validator {

    static function test_step_code__validate(&$test, $dpath, &$c_results) {

        $gotten = array_keys(Data_validator::get('attributes')->validate([
            'attributes_other_1' => /* error */ [[['some value 1']]],
            'attributes_other_2' => /* error */ [[['some value 2']]],
            'attributes'         => /* error */ null,
            'attributes_other_3' => /* error */ [[['some value 3']]],
            'attributes_other_4' => /* error */ [[['some value 4']]],
        ])['errors']);

        $expected = [
            'attributes_other_1'.'|is-attributes:on_failure/error_register',
            'attributes_other_2'.'|is-attributes:on_failure/error_register',
            'attributes'        .'|is-attributes:on_success/is-attributes:root:on_success/is-type:on_failure/error_register',
            'attributes_other_3'.'|is-attributes:on_failure/error_register',
            'attributes_other_4'.'|is-attributes:on_failure/error_register'
        ];

        foreach ($expected as $c_expected) {
            $c_result = Core::in_array($c_expected, $gotten);
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_expected, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_expected, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => 'undefined']);
                $c_results['return'] = 0;
                return;
            }
        }

        $structure_mismatching = array_diff($gotten, $expected);
        foreach ($structure_mismatching as $c_gotten) {
            $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => 'n/a']);
            $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => $c_gotten]);
        }
        if (count($structure_mismatching)) {
            $c_results['return'] = 0;
            return;
        }

        #####################
        ### extended test ###
        #####################

        $object_text = new Text('some translated text');
        $object_text->custom_preperty      = 'string';
        $object_text->args                 = null; /* error */
        $object_text->is_apply_translation = null; /* error */
        $object_text->is_apply_tokens      = null; /* error */
        $object_text->text                 = null; /* error */
        $object_text->weight               = null; /* error */
        $object_text->delimiter            = null; /* error */

        $object_text_simple = new Text_simple('some raw text');
        $object_text_simple->custom_preperty = 'string';
        $object_text_simple->text            = null; /* error */
        $object_text_simple->weight          = null; /* error */
        $object_text_simple->delimiter       = null; /* error */

        $gotten = array_keys(Data_validator::get('attributes')->validate([
            'attributes_other_1' => /* error */ [[['some value 1']]],
            'attributes_other_2' => /* error */ [[['some value 2']]],
            'attributes'         => [
                'object_text'        => $object_text,
                'object_text_simple' => $object_text_simple,
                'object_empty'       => /* error */ (object)[],
                'object'             => /* error */ (object)['property_1' => 'value 1', 'property_2' => 'value 2', 'property_3' => 'value 3'],
                'object_nested'      => /* error */ (object)[0 => (object)[0 => 'nested value']],
                'resource'           => /* error */ fopen(DIR_ROOT.'license.md', 'r'),
                'string'             => 'text',
                'string_empty'       => '',
                'string_true'        => 'true',
                'string_false'       => 'false',
                'integer'            => 123,
                'float'              => 0.000001,
                'boolean_true'       => true,
                'boolean_false'      => false,
                'null'               => null,
                'array_empty'        => [],
                'array'              => [
                    'item_string'             => 'text',
                    'item_string_empty'       => '',
                    'item_string_true'        => 'true',
                    'item_string_false'       => 'false',
                    'item_integer'            => 123,
                    'item_float'              => 0.000001,
                    'item_boolean_true'       => /* error */ true,
                    'item_boolean_false'      => /* error */ false,
                    'item_null'               => /* error */ null,
                    'item_array_empty'        => /* error */ [],
                    'item_array_nested'       => /* error */ ['nested string'],
                    'item_object_text'        => /* error */ $object_text,
                    'item_object_text_simple' => /* error */ $object_text_simple,
                    'item_object_empty'       => /* error */ (object)[],
                    'item_object'             => /* error */ (object)['property_1' => 'value 1', 'property_2' => 'value 2', 'property_3' => 'value 3'],
                    'item_object_nested'      => /* error */ (object)[0 => (object)[0 => 'nested value']],
                    'item_resource'           => /* error */ fopen(DIR_ROOT.'license.md', 'r')
                ],
            ],
            'attributes_other_3' => /* error */ [[['some value 3']]],
            'attributes_other_4' => /* error */ [[['some value 4']]],
        ])['errors']);

        $expected = [
            'attributes_other_1'                          .'|is-attributes:on_failure/error_register',
            'attributes_other_2'                          .'|is-attributes:on_failure/error_register',
            'attributes/object_text/args'                 .'|is-attributes:on_success/is-attributes-object:text-args'                .':on_success/is-type:parent:on_success/is-type:on_failure/error_register',
            'attributes/object_text/is_apply_translation' .'|is-attributes:on_success/is-attributes-object:text-is_apply_translation'.':on_success/is-type:parent:on_success/is-type:on_failure/error_register',
            'attributes/object_text/is_apply_tokens'      .'|is-attributes:on_success/is-attributes-object:text-is_apply_tokens'     .':on_success/is-type:parent:on_success/is-type:on_failure/error_register',
            'attributes/object_text/text'                 .'|is-attributes:on_success/is-attributes-object:text_simple-text'         .':on_success/is-type:parent:on_success/is-type:on_failure/error_register',
            'attributes/object_text/weight'               .'|is-attributes:on_success/is-attributes-object:text_simple-weight'       .':on_success/is-type:parent:on_success/is-type:on_failure/error_register',
            'attributes/object_text/delimiter'            .'|is-attributes:on_success/is-attributes-object:text_simple-delimiter'    .':on_success/is-type:parent:on_success/is-type:on_failure/error_register',
            'attributes/object_text_simple/text'          .'|is-attributes:on_success/is-attributes-object:text_simple-text'         .':on_success/is-type:parent:on_success/is-type:on_failure/error_register',
            'attributes/object_text_simple/weight'        .'|is-attributes:on_success/is-attributes-object:text_simple-weight'       .':on_success/is-type:parent:on_success/is-type:on_failure/error_register',
            'attributes/object_text_simple/delimiter'     .'|is-attributes:on_success/is-attributes-object:text_simple-delimiter'    .':on_success/is-type:parent:on_success/is-type:on_failure/error_register',
            'attributes/object_empty'                     .'|is-attributes:on_success/is-attributes-item:on_success/is-type:on_failure/error_register',
            'attributes/object'                           .'|is-attributes:on_success/is-attributes-item:on_success/is-type:on_failure/error_register',
            'attributes/object_nested'                    .'|is-attributes:on_success/is-attributes-item:on_success/is-type:on_failure/error_register',
            'attributes/resource'                         .'|is-attributes:on_success/is-attributes-item:on_success/is-type:on_failure/error_register',
            'attributes/array/item_boolean_true'          .'|is-attributes:on_success/is-attributes-array-item:on_success/is-type:parent:on_success/is-type:on_failure/error_register',
            'attributes/array/item_boolean_false'         .'|is-attributes:on_success/is-attributes-array-item:on_success/is-type:parent:on_success/is-type:on_failure/error_register',
            'attributes/array/item_null'                  .'|is-attributes:on_success/is-attributes-array-item:on_success/is-type:parent:on_success/is-type:on_failure/error_register',
            'attributes/array/item_array_empty'           .'|is-attributes:on_success/is-attributes-array-item:on_success/is-type:parent:on_success/is-type:on_failure/error_register',
            'attributes/array/item_array_nested'          .'|is-attributes:on_success/is-attributes-array-item:on_success/is-type:parent:on_success/is-type:on_failure/error_register',
            'attributes/array/item_object_text'           .'|is-attributes:on_success/is-attributes-array-item:on_success/is-type:parent:on_success/is-type:on_failure/error_register',
            'attributes/array/item_object_text_simple'    .'|is-attributes:on_success/is-attributes-array-item:on_success/is-type:parent:on_success/is-type:on_failure/error_register',
            'attributes/array/item_object_empty'          .'|is-attributes:on_success/is-attributes-array-item:on_success/is-type:parent:on_success/is-type:on_failure/error_register',
            'attributes/array/item_object'                .'|is-attributes:on_success/is-attributes-array-item:on_success/is-type:parent:on_success/is-type:on_failure/error_register',
            'attributes/array/item_object_nested'         .'|is-attributes:on_success/is-attributes-array-item:on_success/is-type:parent:on_success/is-type:on_failure/error_register',
            'attributes/array/item_resource'              .'|is-attributes:on_success/is-attributes-array-item:on_success/is-type:parent:on_success/is-type:on_failure/error_register',
            'attributes_other_3'                          .'|is-attributes:on_failure/error_register',
            'attributes_other_4'                          .'|is-attributes:on_failure/error_register'
        ];

        foreach ($expected as $c_expected) {
            $c_result = Core::in_array($c_expected, $gotten);
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_expected, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_expected, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => 'undefined']);
                $c_results['return'] = 0;
                return;
            }
        }

        $structure_mismatching = array_diff($gotten, $expected);
        foreach ($structure_mismatching as $c_gotten) {
            $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => 'n/a']);
            $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => $c_gotten]);
        }
        if (count($structure_mismatching)) {
            $c_results['return'] = 0;
            return;
        }
    }

}
