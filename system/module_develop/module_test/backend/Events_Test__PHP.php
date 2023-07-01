<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use effcore\Core;
use effcore\Locale;
use effcore\Text;

abstract class Events_Test__PHP {

    static function test_step_code__hash(&$test, $dpath, &$c_results) {

        # crc32()
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) crc32($i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) crc32($i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) crc32($i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) crc32($i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) crc32($i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'01';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('crc32()', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # md5()
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) md5($i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) md5($i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) md5($i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) md5($i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) md5($i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'02';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('md5()', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # sha1()
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) sha1($i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) sha1($i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) sha1($i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) sha1($i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) sha1($i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'03';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('sha1()', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('md2')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('md2', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('md2', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('md2', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('md2', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('md2', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'04';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'md2\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('md4')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('md4', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('md4', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('md4', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('md4', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('md4', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'05';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'md4\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('md5')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('md5', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('md5', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('md5', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('md5', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('md5', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'06';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'md5\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('sha1')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha1', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha1', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha1', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha1', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha1', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'07';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'sha1\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('sha256')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha256', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha256', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha256', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha256', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha256', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'08';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'sha256\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('sha512/256')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha512/256', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha512/256', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha512/256', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha512/256', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha512/256', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'09';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'sha512/256\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('sha512')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha512', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha512', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha512', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha512', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha512', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'10';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'sha512\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('crc32')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('crc32', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('crc32', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('crc32', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('crc32', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('crc32', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'11';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'crc32\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('crc32b')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('crc32b', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('crc32b', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('crc32b', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('crc32b', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('crc32b', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'12';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'crc32b\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('sha3-224')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha3-224', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha3-224', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha3-224', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha3-224', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha3-224', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'13';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'sha3-224\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('sha3-256')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha3-256', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha3-256', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha3-256', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha3-256', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha3-256', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'14';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'sha3-256\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('sha3-512')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha3-512', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha3-512', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha3-512', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha3-512', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('sha3-512', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'15';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'sha3-512\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('ripemd128')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('ripemd128', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('ripemd128', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('ripemd128', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('ripemd128', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('ripemd128', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'16';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'ripemd128\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('ripemd320')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('ripemd320', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('ripemd320', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('ripemd320', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('ripemd320', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('ripemd320', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'17';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'ripemd320\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('whirlpool')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('whirlpool', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('whirlpool', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('whirlpool', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('whirlpool', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('whirlpool', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'18';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'whirlpool\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('tiger128,3')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('tiger128,3', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('tiger128,3', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('tiger128,3', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('tiger128,3', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('tiger128,3', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'19';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'tiger128,3\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('tiger192,4')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('tiger192,4', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('tiger192,4', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('tiger192,4', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('tiger192,4', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('tiger192,4', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'20';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'tiger192,4\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('snefru')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('snefru', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('snefru', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('snefru', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('snefru', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('snefru', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'21';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'snefru\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('snefru256')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('snefru256', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('snefru256', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('snefru256', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('snefru256', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('snefru256', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'22';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'snefru256\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('gost')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('gost', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('gost', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('gost', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('gost', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('gost', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'23';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'gost\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('gost-crypto')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('gost-crypto', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('gost-crypto', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('gost-crypto', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('gost-crypto', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('gost-crypto', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'24';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'gost-crypto\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('adler32')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('adler32', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('adler32', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('adler32', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('adler32', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('adler32', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'25';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'adler32\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('fnv132')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('fnv132', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('fnv132', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('fnv132', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('fnv132', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('fnv132', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'26';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'fnv132\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('fnv1a32')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('fnv1a32', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('fnv1a32', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('fnv1a32', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('fnv1a32', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('fnv1a32', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'27';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'fnv1a32\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('fnv164')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('fnv164', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('fnv164', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('fnv164', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('fnv164', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('fnv164', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'28';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'fnv164\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('fnv1a64')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('fnv1a64', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('fnv1a64', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('fnv1a64', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('fnv1a64', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('fnv1a64', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'29';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'fnv1a64\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('joaat')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('joaat', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('joaat', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('joaat', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('joaat', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('joaat', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'30';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'joaat\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('haval128,3')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('haval128,3', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('haval128,3', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('haval128,3', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('haval128,3', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('haval128,3', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'31';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'haval128,3\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        # hash('haval256,5')
        $t0 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('haval256,5', $i);  $t1 = microtime(true);
        $t2 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('haval256,5', $i);  $t3 = microtime(true);
        $t4 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('haval256,5', $i);  $t5 = microtime(true);
        $t6 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('haval256,5', $i);  $t7 = microtime(true);
        $t8 = microtime(true);  for($i = 0; $i < 10000; $i++) hash('haval256,5', $i);  $t9 = microtime(true);

        $speed = (($t1-$t0) + ($t3-$t2) + ($t5-$t4) + ($t7-$t6) + ($t9-$t8)) / 5;
        $unique_key = Core::format_number($speed, 5, '.', '', false).'32';
        $c_results['reports'][$dpath.':hash'][$unique_key] = new Text('speed of "%%_name": %%_value', ['name' => str_pad('hash(\'haval256,5\')', 19, '.'),
            'value' => Locale::format_msecond($speed)
        ]);

        ksort($c_results['reports'][$dpath.':hash'], SORT_NUMERIC);
    }

    static function test_step_code__isset(&$test, $dpath, &$c_results) {
        $data = [
            'value_null' => null,
            'value_bool_true' => true,
            'value_bool_false' => false,
            'value_int_0' => 0,
            'value_int_1' => 1,
            'value_float_0_0' => 0.0,
            'value_float_1_0' => 1.0,
            'value_string_empty' => '',
            'value_string_0' => '0',
            'value_string_1' => '1',
            'value_string_X' => 'X',
            'value_array_empty' => [],
            'value_array_null' => [null],
            'value_array_bool_true' => [true],
            'value_array_bool_false' => [false],
            'value_array_int_0' => [0],
            'value_array_int_1' => [1],
            'value_array_float_0_0' => [0.0],
            'value_array_float_1_0' => [1.0],
            'value_array_string_empty' => [''],
            'value_array_string_0' => ['0'],
            'value_array_string_1' => ['1'],
            'value_array_string_X' => ['X'],
            'value_array_array_empty' => [[]]
        ];

        $expected = [
            'value_undefined' => false,
            'value_null' => false,
            'value_bool_true' => true,
            'value_bool_false' => true,
            'value_int_0' => true,
            'value_int_1' => true,
            'value_float_0_0' => true,
            'value_float_1_0' => true,
            'value_string_empty' => true,
            'value_string_0' => true,
            'value_string_1' => true,
            'value_string_X' => true,
            'value_array_empty' => true,
            'value_array_null' => true,
            'value_array_bool_true' => true,
            'value_array_bool_false' => true,
            'value_array_int_0' => true,
            'value_array_int_1' => true,
            'value_array_float_0_0' => true,
            'value_array_float_1_0' => true,
            'value_array_string_empty' => true,
            'value_array_string_0' => true,
            'value_array_string_1' => true,
            'value_array_string_X' => true,
            'value_array_array_empty' => true
        ];

        foreach ($expected as $c_row_id => $c_expected) {
            $c_gotten = isset($data[$c_row_id]);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => $c_expected ? 'true' : 'false']);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => $c_gotten ? 'true' : 'false']);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__empty(&$test, $dpath, &$c_results) {
        $data = [
            'value_null' => null,
            'value_bool_true' => true,
            'value_bool_false' => false,
            'value_int_0' => 0,
            'value_int_1' => 1,
            'value_float_0_0' => 0.0,
            'value_float_1_0' => 1.0,
            'value_string_empty' => '',
            'value_string_0' => '0',
            'value_string_1' => '1',
            'value_string_X' => 'X',
            'value_array_empty' => [],
            'value_array_null' => [null],
            'value_array_bool_true' => [true],
            'value_array_bool_false' => [false],
            'value_array_int_0' => [0],
            'value_array_int_1' => [1],
            'value_array_float_0_0' => [0.0],
            'value_array_float_1_0' => [1.0],
            'value_array_string_empty' => [''],
            'value_array_string_0' => ['0'],
            'value_array_string_1' => ['1'],
            'value_array_string_X' => ['X'],
            'value_array_array_empty' => [[]]
        ];

        $expected = [
            'value_undefined' => true,
            'value_null' => true,
            'value_bool_true' => false,
            'value_bool_false' => true,
            'value_int_0' => true,
            'value_int_1' => false,
            'value_float_0_0' => true,
            'value_float_1_0' => false,
            'value_string_empty' => true,
            'value_string_0' => true,
            'value_string_1' => false,
            'value_string_X' => false,
            'value_array_empty' => true,
            'value_array_null' => false,
            'value_array_bool_true' => false,
            'value_array_bool_false' => false,
            'value_array_int_0' => false,
            'value_array_int_1' => false,
            'value_array_float_0_0' => false,
            'value_array_float_1_0' => false,
            'value_array_string_empty' => false,
            'value_array_string_0' => false,
            'value_array_string_1' => false,
            'value_array_string_X' => false,
            'value_array_array_empty' => false
        ];

        foreach ($expected as $c_row_id => $c_expected) {
            $c_gotten = empty($data[$c_row_id]);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => $c_expected ? 'true' : 'false']);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => $c_gotten ? 'true' : 'false']);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__is_numeric(&$test, $dpath, &$c_results) {
        $data = [
            'value_string_empty' => '',
            'value_string_int' => '-1',
            'value_string_float_dot' => '-1.1',
            'value_string_float_comma' => '-1,1',
            'value_string_int_delimiter' => '-1 000',
            'value_string_int_exponential' => '123e1',
            'value_string_int_hexadecimal' => '0x123',
            'value_string_int_octal' => '01234',
            'value_string_int_binary' => '0b101',
            'value_string_int_prefix' => 'а123',
            'value_string_int_suffix' => '123а',
            'value_string_bool_true' => 'true',
            'value_string_bool_false' => 'false',
            'value_string_null' => 'null'
        ];

        $expected = [
            'value_string_empty' => false,
            'value_string_int' => true,
            'value_string_float_dot' => true,
            'value_string_float_comma' => false,
            'value_string_int_delimiter' => false,
            'value_string_int_exponential' => true,
            'value_string_int_hexadecimal' => false,
            'value_string_int_octal' => true,
            'value_string_int_binary' => false,
            'value_string_int_prefix' => false,
            'value_string_int_suffix' => false,
            'value_string_bool_true' => false,
            'value_string_bool_false' => false,
            'value_string_null' => false
        ];

        foreach ($expected as $c_row_id => $c_expected) {
            $c_gotten = is_numeric($data[$c_row_id]);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => $c_expected ? 'true' : 'false']);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => $c_gotten ? 'true' : 'false']);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__is_float(&$test, $dpath, &$c_results) {
        $data = [
            'value_string_empty' => '',
            'value_string_int' => '-1',
            'value_string_float_dot' => '-1.1',
            'value_string_float_comma' => '-1,1',
            'value_string_int_delimiter' => '-1 000',
            'value_string_int_exponential' => '123e1',
            'value_string_int_hexadecimal' => '0x123',
            'value_string_int_octal' => '01234',
            'value_string_int_binary' => '0b101',
            'value_string_int_prefix' => 'а123',
            'value_string_int_suffix' => '123а',
            'value_string_bool_true' => 'true',
            'value_string_bool_false' => 'false',
            'value_string_null' => 'null'
        ];

        $expected = [
            'value_string_empty' => false,
            'value_string_int' => false,
            'value_string_float_dot' => false,
            'value_string_float_comma' => false,
            'value_string_int_delimiter' => false,
            'value_string_int_exponential' => false,
            'value_string_int_hexadecimal' => false,
            'value_string_int_octal' => false,
            'value_string_int_binary' => false,
            'value_string_int_prefix' => false,
            'value_string_int_suffix' => false,
            'value_string_bool_true' => false,
            'value_string_bool_false' => false,
            'value_string_null' => false
        ];

        foreach ($expected as $c_row_id => $c_expected) {
            $c_gotten = is_float($data[$c_row_id]);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => $c_expected ? 'true' : 'false']);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => $c_gotten ? 'true' : 'false']);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__is_int(&$test, $dpath, &$c_results) {
        $data = [
            'value_string_empty' => '',
            'value_string_int' => '-1',
            'value_string_float_dot' => '-1.1',
            'value_string_float_comma' => '-1,1',
            'value_string_int_delimiter' => '-1 000',
            'value_string_int_exponential' => '123e1',
            'value_string_int_hexadecimal' => '0x123',
            'value_string_int_octal' => '01234',
            'value_string_int_binary' => '0b101',
            'value_string_int_prefix' => 'а123',
            'value_string_int_suffix' => '123а',
            'value_string_bool_true' => 'true',
            'value_string_bool_false' => 'false',
            'value_string_null' => 'null'
        ];

        $expected = [
            'value_string_empty' => false,
            'value_string_int' => false,
            'value_string_float_dot' => false,
            'value_string_float_comma' => false,
            'value_string_int_delimiter' => false,
            'value_string_int_exponential' => false,
            'value_string_int_hexadecimal' => false,
            'value_string_int_octal' => false,
            'value_string_int_binary' => false,
            'value_string_int_prefix' => false,
            'value_string_int_suffix' => false,
            'value_string_bool_true' => false,
            'value_string_bool_false' => false,
            'value_string_null' => false
        ];

        foreach ($expected as $c_row_id => $c_expected) {
            $c_gotten = is_int($data[$c_row_id]);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => $c_expected ? 'true' : 'false']);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => $c_gotten ? 'true' : 'false']);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__intval(&$test, $dpath, &$c_results) {
        $data = [
            'value_string_empty' => '',
            'value_string_int' => '-1',
            'value_string_float_dot' => '-1.1',
            'value_string_float_comma' => '-1,1',
            'value_string_int_delimiter' => '-1 000',
            'value_string_int_exponential' => '123e1',
            'value_string_int_hexadecimal' => '0x123',
            'value_string_int_octal' => '01234',
            'value_string_int_binary' => '0b101',
            'value_string_int_prefix' => 'а123',
            'value_string_int_suffix' => '123а',
            'value_string_bool_true' => 'true',
            'value_string_bool_false' => 'false',
            'value_string_null' => 'null'
        ];

        $expected = [
            'value_string_empty' => 0,
            'value_string_int' => -1,
            'value_string_float_dot' => -1,
            'value_string_float_comma' => -1,
            'value_string_int_delimiter' => -1,
            'value_string_int_exponential' => 1230,
            'value_string_int_hexadecimal' => 0,
            'value_string_int_octal' => 1234,
            'value_string_int_binary' => 0,
            'value_string_int_prefix' => 0,
            'value_string_int_suffix' => 123,
            'value_string_bool_true' => 0,
            'value_string_bool_false' => 0,
            'value_string_null' => 0
        ];

        foreach ($expected as $c_row_id => $c_expected) {
            $c_gotten = intval($data[$c_row_id]);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => Core::return_encoded($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__floatval(&$test, $dpath, &$c_results) {
        $data = [
            'value_string_empty' => '',
            'value_string_int' => '-1',
            'value_string_float_dot' => '-1.1',
            'value_string_float_comma' => '-1,1',
            'value_string_int_delimiter' => '-1 000',
            'value_string_int_exponential' => '123e1',
            'value_string_int_hexadecimal' => '0x123',
            'value_string_int_octal' => '01234',
            'value_string_int_binary' => '0b101',
            'value_string_int_prefix' => 'а123',
            'value_string_int_suffix' => '123а',
            'value_string_bool_true' => 'true',
            'value_string_bool_false' => 'false',
            'value_string_null' => 'null'
        ];

        $expected = [
            'value_string_empty' => 0.0,
            'value_string_int' => -1.0,
            'value_string_float_dot' => -1.1,
            'value_string_float_comma' => -1.0,
            'value_string_int_delimiter' => -1.0,
            'value_string_int_exponential' => 1230.0,
            'value_string_int_hexadecimal' => 0.0,
            'value_string_int_octal' => 1234.0,
            'value_string_int_binary' => 0.0,
            'value_string_int_prefix' => 0.0,
            'value_string_int_suffix' => 123.0,
            'value_string_bool_true' => 0.0,
            'value_string_bool_false' => 0.0,
            'value_string_null' => 0.0
        ];

        foreach ($expected as $c_row_id => $c_expected) {
            $c_gotten = floatval($data[$c_row_id]);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => Core::return_encoded($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__in_array(&$test, $dpath, &$c_results) {
        $data = [
            'value_string_empty'           => in_array('',   ['']),
            'value_null'                   => in_array(null, ['']),
            'value_int_0'                  => in_array(0,    ['']),
            'value_string_0'               => in_array('0',  ['']),
            'value_string_empty_is_strict' => in_array('',   [''], true),
            'value_null_is_strict'         => in_array(null, [''], true),
            'value_int_0_is_strict'        => in_array(0,    [''], true),
            'value_string_0_is_strict'     => in_array('0',  [''], true)
        ];

        $expected = [
            'value_string_empty' => true,
            'value_null' => true,
            'value_int_0' => version_compare(phpversion(), '8.0.0', '<') ? true : false,
            'value_string_0' => false,
            'value_string_empty_is_strict' => true,
            'value_null_is_strict' => false,
            'value_int_0_is_strict' => false,
            'value_string_0_is_strict' => false
        ];

        foreach ($data as $c_row_id => $c_gotten) {
            $c_expected = $expected[$c_row_id];
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => $c_expected ? 'true' : 'false']);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => $c_gotten ? 'true' : 'false']);
                $c_results['return'] = 0;
                return;
            }
        }
    }

}
