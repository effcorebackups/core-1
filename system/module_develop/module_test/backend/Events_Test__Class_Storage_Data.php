<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use const effcore\NL;
use effcore\Core;
use effcore\Storage;
use effcore\Storage_Data;
use effcore\Text;

abstract class Events_Test__Class_Storage_Data {

    static function test_step_code__parse(&$test, $dpath, &$c_results) {

        $data[0] =
            'корень'                 .NL. # 01
            '- |'                    .NL. # 02
            '- | '                   .NL. # 03
            '- | текст'              .NL. # 04
            '- ||'                   .NL. # 05
            '- || '                  .NL. # 06
            '- || текст'             .NL. # 07: error class_name '\effcore\текст'
            '- |:'                   .NL. # 08
            '- |: '                  .NL. # 09
            '- |: текст'             .NL. # 10
            '- |='                   .NL. # 11
            '- |= '                  .NL. # 12
            '- |= текст';                 # 13

        $data[1] =
            'корень'                 .NL. # 01
            '- :'                    .NL. # 02
            '- : '                   .NL. # 03
            '- : текст'              .NL. # 04
            '- :|'                   .NL. # 05
            '- :| '                  .NL. # 06
            '- :| текст'             .NL. # 07: error class_name '\effcore\текст'
            '- ::'                   .NL. # 08
            '- :: '                  .NL. # 09
            '- :: текст'             .NL. # 10
            '- :='                   .NL. # 11
            '- := '                  .NL. # 12
            '- := текст';                 # 13

        $data[2] =
            'корень'                 .NL. # 01
            '- ='                    .NL. # 02
            '- = '                   .NL. # 03
            '- = текст'              .NL. # 04
            '- =|'                   .NL. # 05
            '- =| '                  .NL. # 06
            '- =| текст'             .NL. # 07: error class_name '\effcore\текст'
            '- =:'                   .NL. # 08
            '- =: '                  .NL. # 09
            '- =: текст'             .NL. # 10
            '- =='                   .NL. # 11
            '- == '                  .NL. # 12
            '- == текст';                 # 13

        $data[3] =
            'корень'                 .NL. # 01
            '- ключ_1'               .NL. # 02
            '- ключ_1 '              .NL. # 03
            '- ключ_2|'              .NL. # 04
            '- ключ_3|имя_класса'    .NL. # 05: error class_name '\effcore\имя_класса'
            '- ключ_4:'              .NL. # 06
            '- ключ_5: '             .NL. # 07
            '- ключ_6: текст'        .NL. # 08
            '- ключ_7='              .NL. # 09
            '- ключ_8= '             .NL. # 10
            '- ключ_9= текст';            # 11

        $data[4] =
            'корень'                 .NL. # 01
            '  |'                    .NL. # 02
            '  | '                   .NL. # 03
            '  | текст'              .NL. # 04
            '  ||'                   .NL. # 05
            '  || '                  .NL. # 06
            '  || текст'             .NL. # 07: error class_name '\effcore\текст'
            '  |:'                   .NL. # 08
            '  |: '                  .NL. # 09
            '  |: текст'             .NL. # 10
            '  |='                   .NL. # 11
            '  |= '                  .NL. # 12
            '  |= текст';                 # 13

        $data[5] =
            'корень'                 .NL. # 01
            '  :'                    .NL. # 02
            '  : '                   .NL. # 03
            '  : текст'              .NL. # 04
            '  :|'                   .NL. # 05
            '  :| '                  .NL. # 06
            '  :| текст'             .NL. # 07: error class_name '\effcore\текст'
            '  ::'                   .NL. # 08
            '  :: '                  .NL. # 09
            '  :: текст'             .NL. # 10
            '  :='                   .NL. # 11
            '  := '                  .NL. # 12
            '  := текст';                 # 13

        $data[6] =
            'корень'                 .NL. # 01
            '  ='                    .NL. # 02
            '  = '                   .NL. # 03
            '  = текст'              .NL. # 04
            '  =|'                   .NL. # 05
            '  =| '                  .NL. # 06
            '  =| текст'             .NL. # 07: error class_name '\effcore\текст'
            '  =:'                   .NL. # 08
            '  =: '                  .NL. # 09
            '  =: текст'             .NL. # 10
            '  =='                   .NL. # 11
            '  == '                  .NL. # 12
            '  == текст';                 # 13

        $data[7] =
            'корень'                 .NL. # 01
            '  свойство_1'           .NL. # 02
            '  свойство_1 '          .NL. # 03
            '  свойство_2|'          .NL. # 04
            '  свойство_3|имя_класса'.NL. # 05: error class_name '\effcore\имя_класса'
            '  свойство_4:'          .NL. # 06
            '  свойство_5: '         .NL. # 07
            '  свойство_6: текст'    .NL. # 08
            '  свойство_7='          .NL. # 09
            '  свойство_8= '         .NL. # 10
            '  свойство_9= текст';        # 11

        $expected[0] = (object)[
            'корень' => [
                '|' => 'текст',
                '| ' => (object)[],
                '| текст' => (object)[],
                '|:' => (object)[],
                '|=' => (object)[],
                '|= ' => (object)[],
                '|= текст' => (object)[]
        ]];

        $expected[1] = (object)[
            'корень' => [
                ':' => 'текст',
                ': ' => (object)[],
                ': текст' => (object)[],
                '::' => (object)[],
                ':=' =>(object)[],
                ':= ' => (object)[],
                ':= текст' => (object)[]
        ]];

        $expected[2] = (object)[
            'корень' => [
                '=' => '',
                '= ' => (object)[],
                '= текст' => (object)[],
                '=:' => (object)[],
                'текст' => 'текст',
                '==' => (object)[],
                '== ' => (object)[],
                '== текст' => (object)[]
        ]];

        $expected[3] = (object)[
            'корень' => [
                'ключ_1' => (object)[],
                'ключ_1 ' => (object)[],
                'ключ_2' => (object)[],
                'ключ_3' => (object)[],
                'ключ_4:' => (object)[],
                'ключ_5' => '',
                'ключ_6' => 'текст',
                'ключ_7=' => (object)[],
                'ключ_8= ' => (object)[],
                'ключ_9= текст' => (object)[]
        ]];

        $expected[4] = (object)[
            'корень' => (object)[
                '|' => 'текст',
                '| ' => (object)[],
                '| текст' => (object)[],
                '|:' => (object)[],
                '|=' => (object)[],
                '|= ' => (object)[],
                '|= текст' => (object)[]
        ]];

        $expected[5] = (object)[
            'корень' => (object)[
                ':' => 'текст',
                ': ' => (object)[],
                ': текст' => (object)[],
                '::' => (object)[],
                ':=' => (object)[],
                ':= ' => (object)[],
                ':= текст' => (object)[]
        ]];

        $expected[6] = (object)[
            'корень' => (object)[
                '=' => '',
                '= ' => (object)[],
                '= текст' => (object)[],
                '=:' => (object)[],
                'текст' => 'текст',
                '==' => (object)[],
                '== ' => (object)[],
                '== текст' => (object)[]
        ]];

        $expected[7] = (object)[
            'корень' => (object)[
                'свойство_1' => (object)[],
                'свойство_1 ' => (object)[],
                'свойство_2' => (object)[],
                'свойство_3' => (object)[],
                'свойство_4:' => (object)[],
                'свойство_5' => '',
                'свойство_6' => 'текст',
                'свойство_7=' => (object)[],
                'свойство_8= ' => (object)[],
                'свойство_9= текст' => (object)[]
        ]];

        foreach ($data as $c_key => $c_text) {
            $c_expected = $expected[$c_key];
            $c_gotten = Storage_Data::text_to_data($data[$c_key])->data;
            $c_result = serialize($c_gotten) === serialize($c_expected);
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_key, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_key, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"', ['value' => Core::return_encoded(serialize($c_expected))]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded(serialize($c_gotten))]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__text_to_data_to_text(&$test, $dpath, &$c_results) {

        $data = 'example'                        .NL.
            '- integer: 123'                     .NL.
            '- float: 0.000001'                  .NL.
            '- boolean_true: true'               .NL.
            '- boolean_false: false'             .NL.
            '- string: text'                     .NL.
            '- string_empty: '                   .NL.
            '- string_true|_string_true'         .NL.
            '- string_false|_string_false'       .NL.
            '- null: null'                       .NL.
            '- array_empty|_empty_array'         .NL.
            '- array'                            .NL.
            '  - item_integer: 123'              .NL.
            '  - item_float: 0.000001'           .NL.
            '  - item_boolean_true: true'        .NL.
            '  - item_boolean_false: false'      .NL.
            '  - item_string: text'              .NL.
            '  - item_string_empty: '            .NL.
            '  - item_string_true|_string_true'  .NL.
            '  - item_string_false|_string_false'.NL.
            '  - item_null: null'                .NL.
            '  - item_array_empty|_empty_array'  .NL.
            '- object_text|Text'                 .NL.
            '    is_apply_tokens: false'         .NL.
            '    is_apply_translation: true'     .NL.
            '    text: some text'                .NL.
            '    weight: 0'                      .NL.
            '    custom_preperty: null'          .NL.
            '- object_text_simple|Text_simple'   .NL.
            '    delimiter|_string_nl'           .NL.
            '    text: some text'                .NL.
            '    weight: 0'                      .NL.
            '    custom_preperty: null';

        $expected = 'example'                    .NL.
            '- integer: 123'                     .NL.
            '- float: 0.000001'                  .NL.
            '- boolean_true: true'               .NL.
            '- boolean_false: false'             .NL.
            '- string: text'                     .NL.
            '- string_empty: '                   .NL.
            '- string_true|_string_true'         .NL.
            '- string_false|_string_false'       .NL.
            '- null: null'                       .NL.
            '- array_empty|_empty_array'         .NL.
            '- array'                            .NL.
            '  - item_integer: 123'              .NL.
            '  - item_float: 0.000001'           .NL.
            '  - item_boolean_true: true'        .NL.
            '  - item_boolean_false: false'      .NL.
            '  - item_string: text'              .NL.
            '  - item_string_empty: '            .NL.
            '  - item_string_true|_string_true'  .NL.
            '  - item_string_false|_string_false'.NL.
            '  - item_null: null'                .NL.
            '  - item_array_empty|_empty_array'  .NL.
            '- object_text|Text'                 .NL.
            '    text: some text'                .NL.
            '    custom_preperty: null'          .NL.
            '- object_text_simple|Text_simple'   .NL.
            '    text: some text'                .NL.
            '    custom_preperty: null';

        $gotten = Storage_Data::data_to_text(
            Storage_Data::text_to_data($data)->data->example, 'example'
        );

        $result = $gotten === $expected;
        if ($result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Storage_Data::text_to_data() → Storage_Data::data_to_text()', 'result' => (new Text('success'))->render()]);
        if ($result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Storage_Data::text_to_data() → Storage_Data::data_to_text()', 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"', ['value' => Core::return_encoded($expected)]);
            $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded($gotten)]);
            $c_results['return'] = 0;
            return;
        }
    }

    static function test_step_code__select_array(&$test, $dpath, &$c_results) {

        $storage = Storage::get('data');

        $data = [
            'integer'            => 'test_data/test/integer',
            'float'              => 'test_data/test/float',
            'boolean_true'       => 'test_data/test/boolean_true',
            'boolean_false'      => 'test_data/test/boolean_false',
            'string'             => 'test_data/test/string',
            'string_empty'       => 'test_data/test/string_empty',
            'string_true'        => 'test_data/test/string_true',
            'string_false'       => 'test_data/test/string_false',
            'string_single_line' => 'test_data/test/string_single_line',
            'string_multiline'   => 'test_data/test/string_multiline',
            'null'               => 'test_data/test/null',
            'array'              => 'test_data/test/array',
            'array_empty'        => 'test_data/test/array_empty',
            'object'             => 'test_data/test/object',
            'object_empty'       => 'test_data/test/object_empty',
            'object_text'        => 'test_data/test/object_text',
            'object_text_simple' => 'test_data/test/object_text_simple',
            'nested'             => 'test_data/test/nested'
        ];

        $expected = [
            'integer'            => [123],
            'float'              => [0.000001],
            'boolean_true'       => [true],
            'boolean_false'      => [false],
            'string'             => ['text'],
            'string_empty'       => [''],
            'string_true'        => ['true'],
            'string_false'       => ['false'],
            'string_single_line' => ['line 1 still line 1 still line 1'],
            'string_multiline'   => ['line 1'.NL.' line 2'.NL.' line 3'],
            'null'               => [],
            'array' => [
                'key_1' => 'item value 1',
                'key_2' => 'item value 2',
                'key_3' => 'item value 3',
                'key_5' => 'item value 5 [new] + [modified]',
                'key_6' => 'item value 6 [new]'],
            'array_empty' => [],
            'object' => [
                'property_1' => 'property value 1',
                'property_2' => 'property value 2',
                'property_3' => 'property value 3',
                'property_5' => 'property value 5 [new] + [modified]',
                'property_6' => 'property value 6 [new]'],
            'object_empty' => [],
            'object_text' => [
                'text' => 'some translated text',
                'weight' => 0,
                'delimiter' => NL,
                'args' => [],
                'is_apply_translation' => true,
                'is_apply_tokens' => false],
            'object_text_simple' => [
                'text' => 'some raw text',
                'weight' => 0,
                'delimiter' => NL
            ],
            'nested' => [
                'property_1' => 'nested value 1',
                'nested' => (object)[
                    'property_1' => 'nested value 1.1',
                    'nested' => (object)[
                        'property_1' => 'nested value 1.1.1'
                    ]
                ]
            ]
        ];

        foreach ($data as $c_key => $c_path) {
            $c_expected = $expected[$c_key];
            $c_gotten = $storage->select_array($c_path);
            $c_result = serialize($c_gotten) === serialize($c_expected);
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_key, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_key, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"', ['value' => Core::return_encoded(serialize($c_expected))]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded(serialize($c_gotten))]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

}
