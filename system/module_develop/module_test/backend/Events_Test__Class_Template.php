<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use const effcore\DIR_ROOT;
use const effcore\NL;
use effcore\Core;
use effcore\Template_markup;
use effcore\Test;
use effcore\Text_simple;
use effcore\Text;
use effcore\Text_multiline;

abstract class Events_Test__Class_Template {

    static function test_step_code__attributes_render_html(&$test, $dpath) {
        $data = [
            'value_string'             => 'text',
            'value_string_empty'       => '',
            'value_string_true'        => 'true',
            'value_string_false'       => 'false',
            'value_integer'            => 123,
            'value_float'              => 0.000001,
            'value_boolean_true'       => true,
            'value_boolean_false'      => false,
            'value_null'               => null,
            'value_object_text'        => new Text('some translated text'),
            'value_object_text_simple' => new Text_simple('some raw text'),
            'value_object_empty'       => (object)[],
            'value_object'             => (object)['property_1' => 'value 1', 'property_2' => 'value 2', 'property_3' => 'value 3'],
            'value_object_nested'      => (object)[0 => (object)[0 => 'nested value']],
            'value_resource'           => fopen(DIR_ROOT.'license.md', 'r'),
            'value_array_empty'        => [],
            'value_array_nested'       => [
                'value_nested_string'             => 'nested text',
                'value_nested_string_empty'       => '',
                'value_nested_string_true'        => 'true',
                'value_nested_string_false'       => 'false',
                'value_nested_integer'            => 456,
                'value_nested_float'              => 0.000002,
                'value_nested_boolean_true'       => true,
                'value_nested_boolean_false'      => false,
                'value_nested_null'               => null,
                'value_nested_object_text'        => new Text('some nested translated text'),
                'value_nested_object_text_simple' => new Text_simple('some nested raw text'),
                'value_nested_object_empty'       => (object)[],
                'value_nested_object'             => (object)['property_4' => 'value 4', 'property_5' => 'value 5', 'property_6' => 'value 6'],
                'value_nested_object_nested'      => (object)[0 => (object)[0 => 'nested/nested value']],
                'value_nested_resource'           => fopen(DIR_ROOT.'license.md', 'r'),
                'value_nested_array_empty'        => []
            ]
        ];

        $expected = 'value_string="text" '.
                    'value_string_empty="" '.
                    'value_string_true="true" '.
                    'value_string_false="false" '.
                    'value_integer="123" '.
                    'value_float="0.000001" '.
                    'value_boolean_true '.
                    'value_object_text="some translated text" '.
                    'value_object_text_simple="some raw text" '.
                    'value_object_empty="' .Core::LABEL_NO_RENDERER.'" '.
                    'value_object="'       .Core::LABEL_NO_RENDERER.'" '.
                    'value_object_nested="'.Core::LABEL_NO_RENDERER.'" '.
                    'value_resource="'     .Core::LABEL_UNSUPPORTED_TYPE.'" '.
                    'value_array_nested="nested text true false 456 0.000002 value_nested_boolean_true some nested translated text some nested raw text '.Core::LABEL_NO_RENDERER.' '.Core::LABEL_NO_RENDERER.' '.Core::LABEL_NO_RENDERER.' '.Core::LABEL_UNSUPPORTED_TYPE.' '.Core::LABEL_UNSUPPORTED_TYPE.'"';

        $received = Template_markup::attributes_render_html($data);
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => '*', 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => '*', 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $expected_for_XML = 'value_string="text" '.
                            'value_string_empty="" '.
                            'value_string_true="true" '.
                            'value_string_false="false" '.
                            'value_integer="123" '.
                            'value_float="0.000001" '.
                            'value_boolean_true="value_boolean_true" '.
                            'value_object_text="some translated text" '.
                            'value_object_text_simple="some raw text" '.
                            'value_object_empty="' .Core::LABEL_NO_RENDERER.'" '.
                            'value_object="'       .Core::LABEL_NO_RENDERER.'" '.
                            'value_object_nested="'.Core::LABEL_NO_RENDERER.'" '.
                            'value_resource="'     .Core::LABEL_UNSUPPORTED_TYPE.'" '.
                            'value_array_nested="nested text true false 456 0.000002 value_nested_boolean_true some nested translated text some nested raw text '.Core::LABEL_NO_RENDERER.' '.Core::LABEL_NO_RENDERER.' '.Core::LABEL_NO_RENDERER.' '.Core::LABEL_UNSUPPORTED_TYPE.' '.Core::LABEL_UNSUPPORTED_TYPE.'"';

        $received = Template_markup::attributes_render_html($data, true);
        $result = $received === $expected_for_XML;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => '* + XML', 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => '* + XML', 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected_for_XML)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }
    }

    static function test_step_code__attributes_render_json(&$test, $dpath) {
        $data = [
            'value_string'                => 'text',
            'value_string_empty'          => '',
            'value_string_true'           => 'true',
            'value_string_false'          => 'false',
            'value_integer'               => 123,
            'value_float'                 => 0.000001,
            'value_boolean_true'          => true,
            'value_boolean_false'         => false,
            'value_null'                  => null,
            'value_object_text'           => new Text('some translated text'),
            'value_object_text_simple'    => new Text_simple('some raw text'),
            'value_object_text_multiline' => new Text_multiline(['string 1', 'string 2'], [], NL),
            'value_object_empty'          => (object)[],
            'value_object'                => (object)['property_1' => 'value 1', 'property_2' => 'value 2', 'property_3' => 'value 3'],
            'value_object_nested'         => (object)[0 => (object)[0 => 'nested value']],
            'value_resource'              => fopen(DIR_ROOT.'license.md', 'r'),
            'value_array_empty'           => [],
            'value_array_nested'          => [
                'value_nested_string'             => 'nested text',
                'value_nested_string_empty'       => '',
                'value_nested_string_true'        => 'true',
                'value_nested_string_false'       => 'false',
                'value_nested_integer'            => 456,
                'value_nested_float'              => 0.000002,
                'value_nested_boolean_true'       => true,
                'value_nested_boolean_false'      => false,
                'value_nested_null'               => null,
                'value_nested_object_text'        => new Text('some nested translated text'),
                'value_nested_object_text_simple' => new Text_simple('some nested raw text'),
                'value_nested_object_empty'       => (object)[],
                'value_nested_object'             => (object)['property_4' => 'value 4', 'property_5' => 'value 5', 'property_6' => 'value 6'],
                'value_nested_object_nested'      => (object)[0 => (object)[0 => 'nested/nested value']],
                'value_nested_resource'           => fopen(DIR_ROOT.'license.md', 'r'),
                'value_nested_array_empty'        => []
            ]
        ];

        $expected = '{'.'"value_string":"text",'.
                        '"value_string_empty":"",'.
                        '"value_string_true":"true",'.
                        '"value_string_false":"false",'.
                        '"value_integer":"123",'.
                        '"value_float":"0.000001",'.
                        '"value_boolean_true":true,'.
                        '"value_object_text":"some translated text",'.
                        '"value_object_text_simple":"some raw text",'.
                        '"value_object_text_multiline":"string 1\\nstring 2",'.
                        '"value_object_empty":"' .Core::LABEL_NO_RENDERER.'",'.
                        '"value_object":"'       .Core::LABEL_NO_RENDERER.'",'.
                        '"value_object_nested":"'.Core::LABEL_NO_RENDERER.'",'.
                        '"value_resource":"'     .Core::LABEL_UNSUPPORTED_TYPE.'",'.
                        '"value_array_nested":"nested text true false 456 0.000002 value_nested_boolean_true some nested translated text some nested raw text '.Core::LABEL_NO_RENDERER.' '.Core::LABEL_NO_RENDERER.' '.Core::LABEL_NO_RENDERER.' '.Core::LABEL_UNSUPPORTED_TYPE.' '.Core::LABEL_UNSUPPORTED_TYPE.'"'.'}';

        $received = Template_markup::attributes_render_json($data);
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => '*', 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => '*', 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $expected_for_XML = '{'.'"value_string":"text",'.
                                '"value_string_empty":"",'.
                                '"value_string_true":"true",'.
                                '"value_string_false":"false",'.
                                '"value_integer":"123",'.
                                '"value_float":"0.000001",'.
                                '"value_boolean_true":"value_boolean_true",'.
                                '"value_object_text":"some translated text",'.
                                '"value_object_text_simple":"some raw text",'.
                                '"value_object_text_multiline":"string 1\\nstring 2",'.
                                '"value_object_empty":"' .Core::LABEL_NO_RENDERER.'",'.
                                '"value_object":"'       .Core::LABEL_NO_RENDERER.'",'.
                                '"value_object_nested":"'.Core::LABEL_NO_RENDERER.'",'.
                                '"value_resource":"'     .Core::LABEL_UNSUPPORTED_TYPE.'",'.
                                '"value_array_nested":"nested text true false 456 0.000002 value_nested_boolean_true some nested translated text some nested raw text '.Core::LABEL_NO_RENDERER.' '.Core::LABEL_NO_RENDERER.' '.Core::LABEL_NO_RENDERER.' '.Core::LABEL_UNSUPPORTED_TYPE.' '.Core::LABEL_UNSUPPORTED_TYPE.'"'.'}';

        $received = Template_markup::attributes_render_json($data, true);
        $result = $received === $expected_for_XML;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => '* + XML', 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => '* + XML', 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected_for_XML)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }
    }

}
