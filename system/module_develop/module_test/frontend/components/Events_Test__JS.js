
//////////////////////////////////////////////////////////////////
/// Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ///
//////////////////////////////////////////////////////////////////

'use strict';

import Core from '/system/module_core/frontend/components/Core.jsd';

// ─────────────────────────────────────────────────────────────────────
// Class Events_Test__JS
// ─────────────────────────────────────────────────────────────────────

export default class Events_Test__JS {

    static methods() {
        return [
            this.test_step_code__Array_isArray,
            this.test_step_code__Object_toString,
            this.test_step_code__Core_getType,
            this.test_step_code__typeof
        ]
    }

    static *test_step_code__Array_isArray(dpath) {

        let data = {
            'int'              : {'value' : 100               , 'expected' : false},
            'float'            : {'value' : 3.14              , 'expected' : false},
            'ln2'              : {'value' : Math.LN2          , 'expected' : false},
            'infinity'         : {'value' : Infinity          , 'expected' : false},
            'nan'              : {'value' : NaN               , 'expected' : false},
            'number_1'         : {'value' : new Number(1)     , 'expected' : false},
            'number_2'         : {'value' :     Number(1)     , 'expected' : false},
            'string_empty_1'   : {'value' : ''                , 'expected' : false},
            'string_text'      : {'value' : 'text'            , 'expected' : false},
            'string_0'         : {'value' : '0'               , 'expected' : false},
            'string_1'         : {'value' : '1'               , 'expected' : false},
            'string_empty_2'   : {'value' : new String('')    , 'expected' : false},
            'string_empty_3'   : {'value' :     String('')    , 'expected' : false},
            'string_true'      : {'value' : 'true'            , 'expected' : false},
            'string_false'     : {'value' : 'false'           , 'expected' : false},
            'bool_true_1'      : {'value' : true              , 'expected' : false},
            'bool_false_1'     : {'value' : false             , 'expected' : false},
            'bool_true_2'      : {'value' : new Boolean(true) , 'expected' : false},
            'bool_false_2'     : {'value' :     Boolean(true) , 'expected' : false},
            'symbol'           : {'value' : Symbol()          , 'expected' : false},
            'symbol_text'      : {'value' : Symbol('text')    , 'expected' : false},
            'symbol_iterator'  : {'value' : Symbol.iterator   , 'expected' : false},
            'function'         : {'value' : function(){}      , 'expected' : false},
            'sin'              : {'value' : Math.sin          , 'expected' : false},
            'null'             : {'value' : null              , 'expected' : false},
            'undefined'        : {'value' :  undefined        , 'expected' : false},
            'string_undefined' : {'value' : 'undefined'       , 'expected' : false},
            'date_1'           : {'value' : new Date()        , 'expected' : false},
            'date_2'           : {'value' :     Date()        , 'expected' : false},
            'regexp_1'         : {'value' : /s/               , 'expected' : false},
            'regexp_2'         : {'value' : new RegExp('')    , 'expected' : false},
            'regexp_3'         : {'value' :     RegExp('')    , 'expected' : false},
            'object_1'         : {'value' : {}                , 'expected' : false},
            'object_2'         : {'value' : new Object()      , 'expected' : false},
            'object_3'         : {'value' :     Object()      , 'expected' : false},
            'array_1'          : {'value' : []                , 'expected' : true },
            'array_2'          : {'value' : new Array()       , 'expected' : true },
            'array_3'          : {'value' :     Array()       , 'expected' : true },
            'node'             : {'value' : document.querySelector('x-area')      , 'expected' : false},
            'nodelist'         : {'value' : document.querySelectorAll('x-area')   , 'expected' : false},
            'nodelist-node'    : {'value' : document.querySelectorAll('x-area')[0], 'expected' : false},
        };

        for (let c_key in data) {
            let c_expected = data[c_key].expected;
            let c_value = data[c_key].value;
            let с_received = Array.isArray(c_value);
            let c_result = с_received === c_expected;
            if (c_result === true) yield Core.argsApply(Core.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_key, 'result' : Core.getTranslation('success')});
            if (c_result !== true) yield Core.argsApply(Core.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_key, 'result' : Core.getTranslation('failure')});
            if (c_result !== true) {
                yield Core.argsApply(Core.getTranslation('expected value: "@@_value"'), {'value' : c_expected ? 'true' : 'false' });
                yield Core.argsApply(Core.getTranslation('received value: "@@_value"'), {'value' : с_received ? 'true' : 'false' });
                yield false;
            }
        }
    }

    static *test_step_code__Object_toString(dpath) {

        let data = {
            'int'              : {'value' : 100               , 'expected' : '[object Number]'   },
            'float'            : {'value' : 3.14              , 'expected' : '[object Number]'   },
            'ln2'              : {'value' : Math.LN2          , 'expected' : '[object Number]'   },
            'infinity'         : {'value' : Infinity          , 'expected' : '[object Number]'   },
            'nan'              : {'value' : NaN               , 'expected' : '[object Number]'   },
            'number_1'         : {'value' : new Number(1)     , 'expected' : '[object Number]'   },
            'number_2'         : {'value' :     Number(1)     , 'expected' : '[object Number]'   },
            'string_123…'      : {'value' : 1234567890n       , 'expected' : '[object BigInt]'   },
            'string_empty_1'   : {'value' : ''                , 'expected' : '[object String]'   },
            'string_text'      : {'value' : 'text'            , 'expected' : '[object String]'   },
            'string_0'         : {'value' : '0'               , 'expected' : '[object String]'   },
            'string_1'         : {'value' : '1'               , 'expected' : '[object String]'   },
            'string_empty_2'   : {'value' : new String('')    , 'expected' : '[object String]'   },
            'string_empty_3'   : {'value' :     String('')    , 'expected' : '[object String]'   },
            'string_true'      : {'value' : 'true'            , 'expected' : '[object String]'   },
            'string_false'     : {'value' : 'false'           , 'expected' : '[object String]'   },
            'bool_true_1'      : {'value' : true              , 'expected' : '[object Boolean]'  },
            'bool_false_1'     : {'value' : false             , 'expected' : '[object Boolean]'  },
            'bool_true_2'      : {'value' : new Boolean(true) , 'expected' : '[object Boolean]'  },
            'bool_false_2'     : {'value' :     Boolean(true) , 'expected' : '[object Boolean]'  },
            'symbol'           : {'value' : Symbol()          , 'expected' : '[object Symbol]'   },
            'symbol_text'      : {'value' : Symbol('text')    , 'expected' : '[object Symbol]'   },
            'symbol_iterator'  : {'value' : Symbol.iterator   , 'expected' : '[object Symbol]'   },
            'function'         : {'value' : function(){}      , 'expected' : '[object Function]' },
            'sin'              : {'value' : Math.sin          , 'expected' : '[object Function]' },
            'null'             : {'value' : null              , 'expected' : '[object Null]'     },
            'undefined'        : {'value' :  undefined        , 'expected' : '[object Undefined]'},
            'string_undefined' : {'value' : 'undefined'       , 'expected' : '[object String]'   },
            'date_1'           : {'value' : new Date()        , 'expected' : '[object Date]'     },
            'date_2'           : {'value' :     Date()        , 'expected' : '[object String]'   },
            'regexp_1'         : {'value' : /s/               , 'expected' : '[object RegExp]'   },
            'regexp_2'         : {'value' : new RegExp('')    , 'expected' : '[object RegExp]'   },
            'regexp_3'         : {'value' :     RegExp('')    , 'expected' : '[object RegExp]'   },
            'object_1'         : {'value' : {}                , 'expected' : '[object Object]'   },
            'object_2'         : {'value' : new Object()      , 'expected' : '[object Object]'   },
            'object_3'         : {'value' :     Object()      , 'expected' : '[object Object]'   },
            'array_1'          : {'value' : []                , 'expected' : '[object Array]'    },
            'array_2'          : {'value' : new Array()       , 'expected' : '[object Array]'    },
            'array_3'          : {'value' :     Array()       , 'expected' : '[object Array]'    },
            'node'             : {'value' : document.querySelector('x-area')      , 'expected' : '[object HTMLElement]'},
            'nodelist'         : {'value' : document.querySelectorAll('x-area')   , 'expected' : '[object NodeList]'   },
            'nodelist-node'    : {'value' : document.querySelectorAll('x-area')[0], 'expected' : '[object HTMLElement]'},
        };

        for (let c_key in data) {
            let c_expected = data[c_key].expected;
            let c_value = data[c_key].value;
            let с_received = Object.prototype.toString.call(c_value);
            let c_result = с_received === c_expected;
            if (c_result === true) yield Core.argsApply(Core.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_key, 'result' : Core.getTranslation('success')});
            if (c_result !== true) yield Core.argsApply(Core.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_key, 'result' : Core.getTranslation('failure')});
            if (c_result !== true) {
                yield Core.argsApply(Core.getTranslation('expected value: "@@_value"'), {'value' : c_expected});
                yield Core.argsApply(Core.getTranslation('received value: "@@_value"'), {'value' : с_received});
                yield false;
            }
        }
    }

    static *test_step_code__Core_getType(dpath) {

        let data = {
            'int'              : {'value' : 100               , 'expected' : 'Number'   },
            'float'            : {'value' : 3.14              , 'expected' : 'Number'   },
            'ln2'              : {'value' : Math.LN2          , 'expected' : 'Number'   },
            'infinity'         : {'value' : Infinity          , 'expected' : 'Number'   },
            'nan'              : {'value' : NaN               , 'expected' : 'Number'   },
            'number_1'         : {'value' : new Number(1)     , 'expected' : 'Number'   },
            'number_2'         : {'value' :     Number(1)     , 'expected' : 'Number'   },
            'string_123…'      : {'value' : 1234567890n       , 'expected' : 'BigInt'   },
            'string_empty_1'   : {'value' : ''                , 'expected' : 'String'   },
            'string_text'      : {'value' : 'text'            , 'expected' : 'String'   },
            'string_0'         : {'value' : '0'               , 'expected' : 'String'   },
            'string_1'         : {'value' : '1'               , 'expected' : 'String'   },
            'string_empty_2'   : {'value' : new String('')    , 'expected' : 'String'   },
            'string_empty_3'   : {'value' :     String('')    , 'expected' : 'String'   },
            'string_true'      : {'value' : 'true'            , 'expected' : 'String'   },
            'string_false'     : {'value' : 'false'           , 'expected' : 'String'   },
            'bool_true_1'      : {'value' : true              , 'expected' : 'Boolean'  },
            'bool_false_1'     : {'value' : false             , 'expected' : 'Boolean'  },
            'bool_true_2'      : {'value' : new Boolean(true) , 'expected' : 'Boolean'  },
            'bool_false_2'     : {'value' :     Boolean(true) , 'expected' : 'Boolean'  },
            'symbol'           : {'value' : Symbol()          , 'expected' : 'Symbol'   },
            'symbol_text'      : {'value' : Symbol('text')    , 'expected' : 'Symbol'   },
            'symbol_iterator'  : {'value' : Symbol.iterator   , 'expected' : 'Symbol'   },
            'function'         : {'value' : function(){}      , 'expected' : 'Function' },
            'sin'              : {'value' : Math.sin          , 'expected' : 'Function' },
            'null'             : {'value' : null              , 'expected' : 'Null'     },
            'undefined'        : {'value' :  undefined        , 'expected' : 'Undefined'},
            'string_undefined' : {'value' : 'undefined'       , 'expected' : 'String'   },
            'date_1'           : {'value' : new Date()        , 'expected' : 'Date'     },
            'date_2'           : {'value' :     Date()        , 'expected' : 'String'   },
            'regexp_1'         : {'value' : /s/               , 'expected' : 'RegExp'   },
            'regexp_2'         : {'value' : new RegExp('')    , 'expected' : 'RegExp'   },
            'regexp_3'         : {'value' :     RegExp('')    , 'expected' : 'RegExp'   },
            'object_1'         : {'value' : {}                , 'expected' : 'Object'   },
            'object_2'         : {'value' : new Object()      , 'expected' : 'Object'   },
            'object_3'         : {'value' :     Object()      , 'expected' : 'Object'   },
            'array_1'          : {'value' : []                , 'expected' : 'Array'    },
            'array_2'          : {'value' : new Array()       , 'expected' : 'Array'    },
            'array_3'          : {'value' :     Array()       , 'expected' : 'Array'    },
            'node'             : {'value' : document.querySelector('x-area')      , 'expected' : 'HTMLElement'},
            'nodelist'         : {'value' : document.querySelectorAll('x-area')   , 'expected' : 'NodeList'   },
            'nodelist-node'    : {'value' : document.querySelectorAll('x-area')[0], 'expected' : 'HTMLElement'},
        };

        for (let c_key in data) {
            let c_expected = data[c_key].expected;
            let c_value = data[c_key].value;
            let с_received = Core.getType(c_value);
            let c_result = с_received === c_expected;
            if (c_result === true) yield Core.argsApply(Core.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_key, 'result' : Core.getTranslation('success')});
            if (c_result !== true) yield Core.argsApply(Core.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_key, 'result' : Core.getTranslation('failure')});
            if (c_result !== true) {
                yield Core.argsApply(Core.getTranslation('expected value: "@@_value"'), {'value' : c_expected});
                yield Core.argsApply(Core.getTranslation('received value: "@@_value"'), {'value' : с_received});
                yield false;
            }
        }
    }

    static *test_step_code__typeof(dpath) {

        let data = {
            'int'              : {'value' : 100               , 'expected' : 'number'   },
            'float'            : {'value' : 3.14              , 'expected' : 'number'   },
            'ln2'              : {'value' : Math.LN2          , 'expected' : 'number'   },
            'infinity'         : {'value' : Infinity          , 'expected' : 'number'   },
            'nan'              : {'value' : NaN               , 'expected' : 'number'   }, /* !!! */
            'number_1'         : {'value' : new Number(1)     , 'expected' : 'object'   },
            'number_2'         : {'value' :     Number(1)     , 'expected' : 'number'   },
            'string_123…'      : {'value' : 1234567890n       , 'expected' : 'bigint'   },
            'string_empty_1'   : {'value' : ''                , 'expected' : 'string'   },
            'string_text'      : {'value' : 'text'            , 'expected' : 'string'   },
            'string_0'         : {'value' : '0'               , 'expected' : 'string'   },
            'string_1'         : {'value' : '1'               , 'expected' : 'string'   },
            'string_empty_2'   : {'value' : new String('')    , 'expected' : 'object'   },
            'string_empty_3'   : {'value' :     String('')    , 'expected' : 'string'   },
            'string_true'      : {'value' : 'true'            , 'expected' : 'string'   },
            'string_false'     : {'value' : 'false'           , 'expected' : 'string'   },
            'bool_true_1'      : {'value' : true              , 'expected' : 'boolean'  },
            'bool_false_1'     : {'value' : false             , 'expected' : 'boolean'  },
            'bool_true_2'      : {'value' : new Boolean(true) , 'expected' : 'object'   },
            'bool_false_2'     : {'value' :     Boolean(true) , 'expected' : 'boolean'  },
            'symbol'           : {'value' : Symbol()          , 'expected' : 'symbol'   },
            'symbol_text'      : {'value' : Symbol('text')    , 'expected' : 'symbol'   },
            'symbol_iterator'  : {'value' : Symbol.iterator   , 'expected' : 'symbol'   },
            'function'         : {'value' : function(){}      , 'expected' : 'function' },
            'sin'              : {'value' : Math.sin          , 'expected' : 'function' },
            'null'             : {'value' : null              , 'expected' : 'object'   }, /* !!! */
            'undefined'        : {'value' :  undefined        , 'expected' : 'undefined'},
            'string_undefined' : {'value' : 'undefined'       , 'expected' : 'string'   },
            'date_1'           : {'value' : new Date()        , 'expected' : 'object'   },
            'date_2'           : {'value' :     Date()        , 'expected' : 'string'   },
            'regexp_1'         : {'value' : /s/               , 'expected' : 'object'   },
            'regexp_2'         : {'value' : new RegExp('')    , 'expected' : 'object'   },
            'regexp_3'         : {'value' :     RegExp('')    , 'expected' : 'object'   },
            'object_1'         : {'value' : {}                , 'expected' : 'object'   },
            'object_2'         : {'value' : new Object()      , 'expected' : 'object'   },
            'object_3'         : {'value' :     Object()      , 'expected' : 'object'   },
            'array_1'          : {'value' : []                , 'expected' : 'object'   },
            'array_2'          : {'value' : new Array()       , 'expected' : 'object'   },
            'array_3'          : {'value' :     Array()       , 'expected' : 'object'   },
            'node'             : {'value' : document.querySelector('x-area')      , 'expected' : 'object'},
            'nodelist'         : {'value' : document.querySelectorAll('x-area')   , 'expected' : 'object'},
            'nodelist-node'    : {'value' : document.querySelectorAll('x-area')[0], 'expected' : 'object'},
        };

        for (let c_key in data) {
            let c_expected = data[c_key].expected;
            let c_value = data[c_key].value;
            let с_received = typeof(c_value);
            let c_result = с_received === c_expected;
            if (c_result === true) yield Core.argsApply(Core.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_key, 'result' : Core.getTranslation('success')});
            if (c_result !== true) yield Core.argsApply(Core.getTranslation('checking of item "@@_id": "@@_result"'), {'id' : c_key, 'result' : Core.getTranslation('failure')});
            if (c_result !== true) {
                yield Core.argsApply(Core.getTranslation('expected value: "@@_value"'), {'value' : c_expected});
                yield Core.argsApply(Core.getTranslation('received value: "@@_value"'), {'value' : с_received});
                yield false;
            }
        }

        yield true;
    }

}
