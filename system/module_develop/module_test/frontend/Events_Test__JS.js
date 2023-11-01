
//////////////////////////////////////////////////////////////////
/// Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ///
//////////////////////////////////////////////////////////////////

'use strict';

// ─────────────────────────────────────────────────────────────────────
// Class Events_Test__JS
// ─────────────────────────────────────────────────────────────────────

class Events_Test__JS {

    static test_step_code__Array_isArray(test, dpath, c_results) {
        let data = {
            'int'              : Array.isArray( 100                            ) === false,
            'float'            : Array.isArray( 3.14                           ) === false,
            'ln2'              : Array.isArray( Math.LN2                       ) === false,
            'infinity'         : Array.isArray( Infinity                       ) === false,
            'nan'              : Array.isArray( NaN                            ) === false,
            'number_1'         : Array.isArray( new Number(1)                  ) === false,
            'number_2'         : Array.isArray(     Number(1)                  ) === false,
            'string_empty_1'   : Array.isArray( ''                             ) === false,
            'string_text'      : Array.isArray( 'text'                         ) === false,
            'string_0'         : Array.isArray( '0'                            ) === false,
            'string_1'         : Array.isArray( '1'                            ) === false,
            'string_empty_2'   : Array.isArray( new String('')                 ) === false,
            'string_empty_3'   : Array.isArray(     String('')                 ) === false,
            'string_true'      : Array.isArray( 'true'                         ) === false,
            'string_false'     : Array.isArray( 'false'                        ) === false,
            'bool_true_1'      : Array.isArray( true                           ) === false,
            'bool_false_1'     : Array.isArray( false                          ) === false,
            'bool_true_2'      : Array.isArray( new Boolean(true)              ) === false,
            'bool_false_2'     : Array.isArray(     Boolean(true)              ) === false,
            'symbol'           : Array.isArray( Symbol()                       ) === false,
            'symbol_text'      : Array.isArray( Symbol('text')                 ) === false,
            'symbol_iterator'  : Array.isArray( Symbol.iterator                ) === false,
            'function'         : Array.isArray( function(){}                   ) === false,
            'sin'              : Array.isArray( Math.sin                       ) === false,
            'null'             : Array.isArray( null                           ) === false,
            'undefined'        : Array.isArray(  undefined                     ) === false,
            'string_undefined' : Array.isArray( 'undefined'                    ) === false,
            'date_1'           : Array.isArray( new Date()                     ) === false,
            'date_2'           : Array.isArray(     Date()                     ) === false,
            'regexp_1'         : Array.isArray( /s/                            ) === false,
            'regexp_2'         : Array.isArray( new RegExp('')                 ) === false,
            'regexp_3'         : Array.isArray(     RegExp('')                 ) === false,
            'object_1'         : Array.isArray( {}                             ) === false,
            'object_2'         : Array.isArray( new Object()                   ) === false,
            'object_3'         : Array.isArray(     Object()                   ) === false,
            'array_1'          : Array.isArray( []                             ) === true,
            'array_2'          : Array.isArray( new Array()                    ) === true,
            'array_3'          : Array.isArray(     Array()                    ) === true,
            'document'         : Array.isArray( document.querySelectorAll('*') ) === false
        };

        for (let c_key in data) {
            let c_expected = true;
            let c_gotten = data[c_key];
            let c_result = c_gotten === c_expected;
            if (c_result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_key, 'result' : Effcore.getTranslation('success')}) );
            if (c_result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_key, 'result' : Effcore.getTranslation('failure')}) );
            if (c_result !== true) {
                c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : c_expected ? 'true' : 'false' }) );
                c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : c_gotten ? 'true' : 'false' }) );
                c_results['return'] = 0;
                return;
            }
        }
    }

    static test_step_code__Object_toString(test, dpath, c_results) {
        let data = {
            'int'              : Object.prototype.toString.call( 100                            ) === '[object Number]',
            'float'            : Object.prototype.toString.call( 3.14                           ) === '[object Number]',
            'ln2'              : Object.prototype.toString.call( Math.LN2                       ) === '[object Number]',
            'infinity'         : Object.prototype.toString.call( Infinity                       ) === '[object Number]',
            'nan'              : Object.prototype.toString.call( NaN                            ) === '[object Number]',
            'number_1'         : Object.prototype.toString.call( new Number(1)                  ) === '[object Number]',
            'number_2'         : Object.prototype.toString.call(     Number(1)                  ) === '[object Number]',
            'string_123…'      : Object.prototype.toString.call( 1234567890n                    ) === '[object BigInt]',
            'string_empty_1'   : Object.prototype.toString.call( ''                             ) === '[object String]',
            'string_text'      : Object.prototype.toString.call( 'text'                         ) === '[object String]',
            'string_0'         : Object.prototype.toString.call( '0'                            ) === '[object String]',
            'string_1'         : Object.prototype.toString.call( '1'                            ) === '[object String]',
            'string_empty_2'   : Object.prototype.toString.call( new String('')                 ) === '[object String]',
            'string_empty_3'   : Object.prototype.toString.call(     String('')                 ) === '[object String]',
            'string_true'      : Object.prototype.toString.call( 'true'                         ) === '[object String]',
            'string_false'     : Object.prototype.toString.call( 'false'                        ) === '[object String]',
            'bool_true_1'      : Object.prototype.toString.call( true                           ) === '[object Boolean]',
            'bool_false_1'     : Object.prototype.toString.call( false                          ) === '[object Boolean]',
            'bool_true_2'      : Object.prototype.toString.call( new Boolean(true)              ) === '[object Boolean]',
            'bool_false_2'     : Object.prototype.toString.call(     Boolean(true)              ) === '[object Boolean]',
            'symbol'           : Object.prototype.toString.call( Symbol()                       ) === '[object Symbol]',
            'symbol_text'      : Object.prototype.toString.call( Symbol('text')                 ) === '[object Symbol]',
            'symbol_iterator'  : Object.prototype.toString.call( Symbol.iterator                ) === '[object Symbol]',
            'function'         : Object.prototype.toString.call( function(){}                   ) === '[object Function]',
            'sin'              : Object.prototype.toString.call( Math.sin                       ) === '[object Function]',
            'null'             : Object.prototype.toString.call( null                           ) === '[object Null]',
            'undefined'        : Object.prototype.toString.call(  undefined                     ) === '[object Undefined]',
            'string_undefined' : Object.prototype.toString.call( 'undefined'                    ) === '[object String]',
            'date_1'           : Object.prototype.toString.call( new Date()                     ) === '[object Date]',
            'date_2'           : Object.prototype.toString.call(     Date()                     ) === '[object String]',
            'regexp_1'         : Object.prototype.toString.call( /s/                            ) === '[object RegExp]',
            'regexp_2'         : Object.prototype.toString.call( new RegExp('')                 ) === '[object RegExp]',
            'regexp_3'         : Object.prototype.toString.call(     RegExp('')                 ) === '[object RegExp]',
            'object_1'         : Object.prototype.toString.call( {}                             ) === '[object Object]',
            'object_2'         : Object.prototype.toString.call( new Object()                   ) === '[object Object]',
            'object_3'         : Object.prototype.toString.call(     Object()                   ) === '[object Object]',
            'array_1'          : Object.prototype.toString.call( []                             ) === '[object Array]',
            'array_2'          : Object.prototype.toString.call( new Array()                    ) === '[object Array]',
            'array_3'          : Object.prototype.toString.call(     Array()                    ) === '[object Array]',
            'document'         : Object.prototype.toString.call( document.querySelectorAll('*') ) === '[object NodeList]'
        };

        for (let c_key in data) {
            let c_expected = true;
            let c_gotten = data[c_key];
            let c_result = c_gotten === c_expected;
            if (c_result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_key, 'result' : Effcore.getTranslation('success')}) );
            if (c_result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_key, 'result' : Effcore.getTranslation('failure')}) );
            if (c_result !== true) {
                c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : c_expected ? 'true' : 'false' }) );
                c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : c_gotten ? 'true' : 'false' }) );
                c_results['return'] = 0;
                return;
            }
        }
    }

    static test_step_code__Effcore_getType(test, dpath, c_results) {
        let data = {
            'int'              : Effcore.getType( 100                            ) === 'Number',
            'float'            : Effcore.getType( 3.14                           ) === 'Number',
            'ln2'              : Effcore.getType( Math.LN2                       ) === 'Number',
            'infinity'         : Effcore.getType( Infinity                       ) === 'Number',
            'nan'              : Effcore.getType( NaN                            ) === 'Number',
            'number_1'         : Effcore.getType( new Number(1)                  ) === 'Number',
            'number_2'         : Effcore.getType(     Number(1)                  ) === 'Number',
            'string_123…'      : Effcore.getType( 1234567890n                    ) === 'BigInt',
            'string_empty_1'   : Effcore.getType( ''                             ) === 'String',
            'string_text'      : Effcore.getType( 'text'                         ) === 'String',
            'string_0'         : Effcore.getType( '0'                            ) === 'String',
            'string_1'         : Effcore.getType( '1'                            ) === 'String',
            'string_empty_2'   : Effcore.getType( new String('')                 ) === 'String',
            'string_empty_3'   : Effcore.getType(     String('')                 ) === 'String',
            'string_true'      : Effcore.getType( 'true'                         ) === 'String',
            'string_false'     : Effcore.getType( 'false'                        ) === 'String',
            'bool_true_1'      : Effcore.getType( true                           ) === 'Boolean',
            'bool_false_1'     : Effcore.getType( false                          ) === 'Boolean',
            'bool_true_2'      : Effcore.getType( new Boolean(true)              ) === 'Boolean',
            'bool_false_2'     : Effcore.getType(     Boolean(true)              ) === 'Boolean',
            'symbol'           : Effcore.getType( Symbol()                       ) === 'Symbol',
            'symbol_text'      : Effcore.getType( Symbol('text')                 ) === 'Symbol',
            'symbol_iterator'  : Effcore.getType( Symbol.iterator                ) === 'Symbol',
            'function'         : Effcore.getType( function(){}                   ) === 'Function',
            'sin'              : Effcore.getType( Math.sin                       ) === 'Function',
            'null'             : Effcore.getType( null                           ) === 'Null',
            'undefined'        : Effcore.getType(  undefined                     ) === 'Undefined',
            'string_undefined' : Effcore.getType( 'undefined'                    ) === 'String',
            'date_1'           : Effcore.getType( new Date()                     ) === 'Date',
            'date_2'           : Effcore.getType(     Date()                     ) === 'String',
            'regexp_1'         : Effcore.getType( /s/                            ) === 'RegExp',
            'regexp_2'         : Effcore.getType( new RegExp('')                 ) === 'RegExp',
            'regexp_3'         : Effcore.getType(     RegExp('')                 ) === 'RegExp',
            'object_1'         : Effcore.getType( {}                             ) === 'Object',
            'object_2'         : Effcore.getType( new Object()                   ) === 'Object',
            'object_3'         : Effcore.getType(     Object()                   ) === 'Object',
            'array_1'          : Effcore.getType( []                             ) === 'Array',
            'array_2'          : Effcore.getType( new Array()                    ) === 'Array',
            'array_3'          : Effcore.getType(     Array()                    ) === 'Array',
            'document'         : Effcore.getType( document.querySelectorAll('*') ) === 'NodeList'
        };

        for (let c_key in data) {
            let c_expected = true;
            let c_gotten = data[c_key];
            let c_result = c_gotten === c_expected;
            if (c_result === true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_key, 'result' : Effcore.getTranslation('success')}) );
            if (c_result !== true) c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_key, 'result' : Effcore.getTranslation('failure')}) );
            if (c_result !== true) {
                c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('expected value: "@@_value"'), {'value' : c_expected ? 'true' : 'false' }) );
                c_results['reports'][dpath].push( Effcore.argsApply(Effcore.getTranslation('gotten value: "@@_value"'), {'value' : c_gotten ? 'true' : 'false' }) );
                c_results['return'] = 0;
                return;
            }
        }
    }

}
