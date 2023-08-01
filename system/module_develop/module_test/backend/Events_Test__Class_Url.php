<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use const effcore\DIR_ROOT;
use effcore\Core;
use effcore\Request;
use effcore\Text;
use effcore\Url;

abstract class Events_Test__Class_Url {

    static function test_step_code__file_info_get(&$test, $dpath, &$c_results) {
        $data = [

            # ─────────────────────────────────────────────────────────────────────
            # possible transpositions of '"|.|..|...|0'
            # ─────────────────────────────────────────────────────────────────────

            /*  │"  │   │   │ */                 'http://example.com/'.''              => ['dirs' => null,            'name' => null,        'type' => null], # no file
            /*  │.  │   │   │ */                 'http://example.com/'.'.'             => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │.. │   │   │ */                 'http://example.com/'.'..'            => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │...│   │   │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT,        'name' => '...',       'type' => ''  ],
            /*  │0  │   │   │ */                 'http://example.com/'.'0'             => ['dirs' => DIR_ROOT,        'name' => '0',         'type' => ''  ],
            /*  │"  │"  │   │ */                 'http://example.com/'.''              => ['dirs' => null,            'name' => null,        'type' => null], # no file
            /*  │"  │.  │   │ */                 'http://example.com/'.'.'             => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │"  │.. │   │ */                 'http://example.com/'.'..'            => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │"  │...│   │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT,        'name' => '...',       'type' => ''  ],
            /*  │"  │0  │   │ */                 'http://example.com/'.'0'             => ['dirs' => DIR_ROOT,        'name' => '0',         'type' => ''  ],
            /*  │.  │"  │   │ */                 'http://example.com/'.'.'             => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │.  │.  │   │ */                 'http://example.com/'.'..'            => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │.  │.. │   │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT,        'name' => '...',       'type' => ''  ],
            /*  │.  │...│   │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT,        'name' => '....',      'type' => ''  ],
            /*  │.  │0  │   │ */                 'http://example.com/'.'.0'            => ['dirs' => DIR_ROOT,        'name' => '',          'type' => '0' ],
            /*  │.. │"  │   │ */                 'http://example.com/'.'..'            => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │.. │.  │   │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT,        'name' => '...',       'type' => ''  ],
            /*  │.. │.. │   │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT,        'name' => '....',      'type' => ''  ],
            /*  │.. │...│   │ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT,        'name' => '.....',     'type' => ''  ],
            /*  │.. │0  │   │ */                 'http://example.com/'.'..0'           => ['dirs' => DIR_ROOT,        'name' => '.',         'type' => '0' ],
            /*  │...│"  │   │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT,        'name' => '...',       'type' => ''  ],
            /*  │...│.  │   │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT,        'name' => '....',      'type' => ''  ],
            /*  │...│.. │   │ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT,        'name' => '.....',     'type' => ''  ],
            /*  │...│...│   │ */                 'http://example.com/'.'......'        => ['dirs' => DIR_ROOT,        'name' => '......',    'type' => ''  ],
            /*  │...│0  │   │ */                 'http://example.com/'.'...0'          => ['dirs' => DIR_ROOT,        'name' => '..',        'type' => '0' ],
            /*  │0  │"  │   │ */                 'http://example.com/'.'0'             => ['dirs' => DIR_ROOT,        'name' => '0',         'type' => ''  ],
            /*  │0  │.  │   │ */                 'http://example.com/'.'0.'            => ['dirs' => DIR_ROOT,        'name' => '0.',        'type' => ''  ],
            /*  │0  │.. │   │ */                 'http://example.com/'.'0..'           => ['dirs' => DIR_ROOT,        'name' => '0..',       'type' => ''  ],
            /*  │0  │...│   │ */                 'http://example.com/'.'0...'          => ['dirs' => DIR_ROOT,        'name' => '0...',      'type' => ''  ],
            /*  │0  │0  │   │ */                 'http://example.com/'.'00'            => ['dirs' => DIR_ROOT,        'name' => '00',        'type' => ''  ],
            /*  │"  │"  │"  │ */                 'http://example.com/'.''              => ['dirs' => null,            'name' => null,        'type' => null], # no file
            /*  │"  │"  │.  │ */                 'http://example.com/'.'.'             => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │"  │"  │.. │ */                 'http://example.com/'.'..'            => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │"  │"  │...│ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT,        'name' => '...',       'type' => ''  ],
            /*  │"  │"  │0  │ */                 'http://example.com/'.'0'             => ['dirs' => DIR_ROOT,        'name' => '0',         'type' => ''  ],
            /*  │"  │.  │"  │ */                 'http://example.com/'.'.'             => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │"  │.  │.  │ */                 'http://example.com/'.'..'            => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │"  │.  │.. │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT,        'name' => '...',       'type' => ''  ],
            /*  │"  │.  │...│ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT,        'name' => '....',      'type' => ''  ],
            /*  │"  │.  │0  │ */                 'http://example.com/'.'.0'            => ['dirs' => DIR_ROOT,        'name' => '',          'type' => '0' ],
            /*  │"  │.. │"  │ */                 'http://example.com/'.'..'            => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │"  │.. │.  │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT,        'name' => '...',       'type' => ''  ],
            /*  │"  │.. │.. │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT,        'name' => '....',      'type' => ''  ],
            /*  │"  │.. │...│ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT,        'name' => '.....',     'type' => ''  ],
            /*  │"  │.. │0  │ */                 'http://example.com/'.'..0'           => ['dirs' => DIR_ROOT,        'name' => '.',         'type' => '0' ],
            /*  │"  │...│"  │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT,        'name' => '...',       'type' => ''  ],
            /*  │"  │...│.  │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT,        'name' => '....',      'type' => ''  ],
            /*  │"  │...│.. │ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT,        'name' => '.....',     'type' => ''  ],
            /*  │"  │...│...│ */                 'http://example.com/'.'......'        => ['dirs' => DIR_ROOT,        'name' => '......',    'type' => ''  ],
            /*  │"  │...│0  │ */                 'http://example.com/'.'...0'          => ['dirs' => DIR_ROOT,        'name' => '..',        'type' => '0' ],
            /*  │"  │0  │"  │ */                 'http://example.com/'.'0'             => ['dirs' => DIR_ROOT,        'name' => '0',         'type' => ''  ],
            /*  │"  │0  │.  │ */                 'http://example.com/'.'0.'            => ['dirs' => DIR_ROOT,        'name' => '0.',        'type' => ''  ],
            /*  │"  │0  │.. │ */                 'http://example.com/'.'0..'           => ['dirs' => DIR_ROOT,        'name' => '0..',       'type' => ''  ],
            /*  │"  │0  │...│ */                 'http://example.com/'.'0...'          => ['dirs' => DIR_ROOT,        'name' => '0...',      'type' => ''  ],
            /*  │"  │0  │0  │ */                 'http://example.com/'.'00'            => ['dirs' => DIR_ROOT,        'name' => '00',        'type' => ''  ],
            /*  │.  │"  │"  │ */                 'http://example.com/'.'.'             => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │.  │"  │.  │ */                 'http://example.com/'.'..'            => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │.  │"  │.. │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT,        'name' => '...',       'type' => ''  ],
            /*  │.  │"  │...│ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT,        'name' => '....',      'type' => ''  ],
            /*  │.  │"  │0  │ */                 'http://example.com/'.'.0'            => ['dirs' => DIR_ROOT,        'name' => '',          'type' => '0' ],
            /*  │.  │.  │"  │ */                 'http://example.com/'.'..'            => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │.  │.  │.  │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT,        'name' => '...',       'type' => ''  ],
            /*  │.  │.  │.. │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT,        'name' => '....',      'type' => ''  ],
            /*  │.  │.  │...│ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT,        'name' => '.....',     'type' => ''  ],
            /*  │.  │.  │0  │ */                 'http://example.com/'.'..0'           => ['dirs' => DIR_ROOT,        'name' => '.',         'type' => '0' ],
            /*  │.  │.. │"  │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT,        'name' => '...',       'type' => ''  ],
            /*  │.  │.. │.  │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT,        'name' => '....',      'type' => ''  ],
            /*  │.  │.. │.. │ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT,        'name' => '.....',     'type' => ''  ],
            /*  │.  │.. │...│ */                 'http://example.com/'.'......'        => ['dirs' => DIR_ROOT,        'name' => '......',    'type' => ''  ],
            /*  │.  │.. │0  │ */                 'http://example.com/'.'...0'          => ['dirs' => DIR_ROOT,        'name' => '..',        'type' => '0' ],
            /*  │.  │...│"  │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT,        'name' => '....',      'type' => ''  ],
            /*  │.  │...│.  │ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT,        'name' => '.....',     'type' => ''  ],
            /*  │.  │...│.. │ */                 'http://example.com/'.'......'        => ['dirs' => DIR_ROOT,        'name' => '......',    'type' => ''  ],
            /*  │.  │...│...│ */                 'http://example.com/'.'.......'       => ['dirs' => DIR_ROOT,        'name' => '.......',   'type' => ''  ],
            /*  │.  │...│0  │ */                 'http://example.com/'.'....0'         => ['dirs' => DIR_ROOT,        'name' => '...',       'type' => '0' ],
            /*  │.  │0  │"  │ */                 'http://example.com/'.'.0'            => ['dirs' => DIR_ROOT,        'name' => '',          'type' => '0' ],
            /*  │.  │0  │.  │ */                 'http://example.com/'.'.0.'           => ['dirs' => DIR_ROOT,        'name' => '.0.',       'type' => ''  ],
            /*  │.  │0  │.. │ */                 'http://example.com/'.'.0..'          => ['dirs' => DIR_ROOT,        'name' => '.0..',      'type' => ''  ],
            /*  │.  │0  │...│ */                 'http://example.com/'.'.0...'         => ['dirs' => DIR_ROOT,        'name' => '.0...',     'type' => ''  ],
            /*  │.  │0  │0  │ */                 'http://example.com/'.'.00'           => ['dirs' => DIR_ROOT,        'name' => '',          'type' => '00'],
            /*  │.. │"  │"  │ */                 'http://example.com/'.'..'            => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │.. │"  │.  │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT,        'name' => '...',       'type' => ''  ],
            /*  │.. │"  │.. │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT,        'name' => '....',      'type' => ''  ],
            /*  │.. │"  │...│ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT,        'name' => '.....',     'type' => ''  ],
            /*  │.. │"  │0  │ */                 'http://example.com/'.'..0'           => ['dirs' => DIR_ROOT,        'name' => '.',         'type' => '0' ],
            /*  │.. │.  │"  │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT,        'name' => '...',       'type' => ''  ],
            /*  │.. │.  │.  │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT,        'name' => '....',      'type' => ''  ],
            /*  │.. │.  │.. │ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT,        'name' => '.....',     'type' => ''  ],
            /*  │.. │.  │...│ */                 'http://example.com/'.'......'        => ['dirs' => DIR_ROOT,        'name' => '......',    'type' => ''  ],
            /*  │.. │.  │0  │ */                 'http://example.com/'.'...0'          => ['dirs' => DIR_ROOT,        'name' => '..',        'type' => '0' ],
            /*  │.. │.. │"  │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT,        'name' => '....',      'type' => ''  ],
            /*  │.. │.. │.  │ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT,        'name' => '.....',     'type' => ''  ],
            /*  │.. │.. │.. │ */                 'http://example.com/'.'......'        => ['dirs' => DIR_ROOT,        'name' => '......',    'type' => ''  ],
            /*  │.. │.. │...│ */                 'http://example.com/'.'.......'       => ['dirs' => DIR_ROOT,        'name' => '.......',   'type' => ''  ],
            /*  │.. │.. │0  │ */                 'http://example.com/'.'....0'         => ['dirs' => DIR_ROOT,        'name' => '...',       'type' => '0' ],
            /*  │.. │...│"  │ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT,        'name' => '.....',     'type' => ''  ],
            /*  │.. │...│.  │ */                 'http://example.com/'.'......'        => ['dirs' => DIR_ROOT,        'name' => '......',    'type' => ''  ],
            /*  │.. │...│.. │ */                 'http://example.com/'.'.......'       => ['dirs' => DIR_ROOT,        'name' => '.......',   'type' => ''  ],
            /*  │.. │...│...│ */                 'http://example.com/'.'........'      => ['dirs' => DIR_ROOT,        'name' => '........',  'type' => ''  ],
            /*  │.. │...│0  │ */                 'http://example.com/'.'.....0'        => ['dirs' => DIR_ROOT,        'name' => '....',      'type' => '0' ],
            /*  │.. │0  │"  │ */                 'http://example.com/'.'..0'           => ['dirs' => DIR_ROOT,        'name' => '.',         'type' => '0' ],
            /*  │.. │0  │.  │ */                 'http://example.com/'.'..0.'          => ['dirs' => DIR_ROOT,        'name' => '..0.',      'type' => ''  ],
            /*  │.. │0  │.. │ */                 'http://example.com/'.'..0..'         => ['dirs' => DIR_ROOT,        'name' => '..0..',     'type' => ''  ],
            /*  │.. │0  │...│ */                 'http://example.com/'.'..0...'        => ['dirs' => DIR_ROOT,        'name' => '..0...',    'type' => ''  ],
            /*  │.. │0  │0  │ */                 'http://example.com/'.'..00'          => ['dirs' => DIR_ROOT,        'name' => '.',         'type' => '00'],
            /*  │...│"  │"  │ */                 'http://example.com/'.'...'           => ['dirs' => DIR_ROOT,        'name' => '...',       'type' => ''  ],
            /*  │...│"  │.  │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT,        'name' => '....',      'type' => ''  ],
            /*  │...│"  │.. │ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT,        'name' => '.....',     'type' => ''  ],
            /*  │...│"  │...│ */                 'http://example.com/'.'......'        => ['dirs' => DIR_ROOT,        'name' => '......',    'type' => ''  ],
            /*  │...│"  │0  │ */                 'http://example.com/'.'...0'          => ['dirs' => DIR_ROOT,        'name' => '..',        'type' => '0' ],
            /*  │...│.  │"  │ */                 'http://example.com/'.'....'          => ['dirs' => DIR_ROOT,        'name' => '....',      'type' => ''  ],
            /*  │...│.  │.  │ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT,        'name' => '.....',     'type' => ''  ],
            /*  │...│.  │.. │ */                 'http://example.com/'.'......'        => ['dirs' => DIR_ROOT,        'name' => '......',    'type' => ''  ],
            /*  │...│.  │...│ */                 'http://example.com/'.'.......'       => ['dirs' => DIR_ROOT,        'name' => '.......',   'type' => ''  ],
            /*  │...│.  │0  │ */                 'http://example.com/'.'....0'         => ['dirs' => DIR_ROOT,        'name' => '...',       'type' => '0' ],
            /*  │...│.. │"  │ */                 'http://example.com/'.'.....'         => ['dirs' => DIR_ROOT,        'name' => '.....',     'type' => ''  ],
            /*  │...│.. │.  │ */                 'http://example.com/'.'......'        => ['dirs' => DIR_ROOT,        'name' => '......',    'type' => ''  ],
            /*  │...│.. │.. │ */                 'http://example.com/'.'.......'       => ['dirs' => DIR_ROOT,        'name' => '.......',   'type' => ''  ],
            /*  │...│.. │...│ */                 'http://example.com/'.'........'      => ['dirs' => DIR_ROOT,        'name' => '........',  'type' => ''  ],
            /*  │...│.. │0  │ */                 'http://example.com/'.'.....0'        => ['dirs' => DIR_ROOT,        'name' => '....',      'type' => '0' ],
            /*  │...│...│"  │ */                 'http://example.com/'.'......'        => ['dirs' => DIR_ROOT,        'name' => '......',    'type' => ''  ],
            /*  │...│...│.  │ */                 'http://example.com/'.'.......'       => ['dirs' => DIR_ROOT,        'name' => '.......',   'type' => ''  ],
            /*  │...│...│.. │ */                 'http://example.com/'.'........'      => ['dirs' => DIR_ROOT,        'name' => '........',  'type' => ''  ],
            /*  │...│...│...│ */                 'http://example.com/'.'.........'     => ['dirs' => DIR_ROOT,        'name' => '.........', 'type' => ''  ],
            /*  │...│...│0  │ */                 'http://example.com/'.'......0'       => ['dirs' => DIR_ROOT,        'name' => '.....',     'type' => '0' ],
            /*  │...│0  │"  │ */                 'http://example.com/'.'...0'          => ['dirs' => DIR_ROOT,        'name' => '..',        'type' => '0' ],
            /*  │...│0  │.  │ */                 'http://example.com/'.'...0.'         => ['dirs' => DIR_ROOT,        'name' => '...0.',     'type' => ''  ],
            /*  │...│0  │.. │ */                 'http://example.com/'.'...0..'        => ['dirs' => DIR_ROOT,        'name' => '...0..',    'type' => ''  ],
            /*  │...│0  │...│ */                 'http://example.com/'.'...0...'       => ['dirs' => DIR_ROOT,        'name' => '...0...',   'type' => ''  ],
            /*  │...│0  │0  │ */                 'http://example.com/'.'...00'         => ['dirs' => DIR_ROOT,        'name' => '..',        'type' => '00'],
            /*  │0  │"  │"  │ */                 'http://example.com/'.'0'             => ['dirs' => DIR_ROOT,        'name' => '0',         'type' => ''  ],
            /*  │0  │"  │.  │ */                 'http://example.com/'.'0.'            => ['dirs' => DIR_ROOT,        'name' => '0.',        'type' => ''  ],
            /*  │0  │"  │.. │ */                 'http://example.com/'.'0..'           => ['dirs' => DIR_ROOT,        'name' => '0..',       'type' => ''  ],
            /*  │0  │"  │...│ */                 'http://example.com/'.'0...'          => ['dirs' => DIR_ROOT,        'name' => '0...',      'type' => ''  ],
            /*  │0  │"  │0  │ */                 'http://example.com/'.'00'            => ['dirs' => DIR_ROOT,        'name' => '00',        'type' => ''  ],
            /*  │0  │.  │"  │ */                 'http://example.com/'.'0.'            => ['dirs' => DIR_ROOT,        'name' => '0.',        'type' => ''  ],
            /*  │0  │.  │.  │ */                 'http://example.com/'.'0..'           => ['dirs' => DIR_ROOT,        'name' => '0..',       'type' => ''  ],
            /*  │0  │.  │.. │ */                 'http://example.com/'.'0...'          => ['dirs' => DIR_ROOT,        'name' => '0...',      'type' => ''  ],
            /*  │0  │.  │...│ */                 'http://example.com/'.'0....'         => ['dirs' => DIR_ROOT,        'name' => '0....',     'type' => ''  ],
            /*  │0  │.  │0  │ */                 'http://example.com/'.'0.0'           => ['dirs' => DIR_ROOT,        'name' => '0',         'type' => '0' ],
            /*  │0  │.. │"  │ */                 'http://example.com/'.'0..'           => ['dirs' => DIR_ROOT,        'name' => '0..',       'type' => ''  ],
            /*  │0  │.. │.  │ */                 'http://example.com/'.'0...'          => ['dirs' => DIR_ROOT,        'name' => '0...',      'type' => ''  ],
            /*  │0  │.. │.. │ */                 'http://example.com/'.'0....'         => ['dirs' => DIR_ROOT,        'name' => '0....',     'type' => ''  ],
            /*  │0  │.. │...│ */                 'http://example.com/'.'0.....'        => ['dirs' => DIR_ROOT,        'name' => '0.....',    'type' => ''  ],
            /*  │0  │.. │0  │ */                 'http://example.com/'.'0..0'          => ['dirs' => DIR_ROOT,        'name' => '0.',        'type' => '0' ],
            /*  │0  │...│"  │ */                 'http://example.com/'.'0...'          => ['dirs' => DIR_ROOT,        'name' => '0...',      'type' => ''  ],
            /*  │0  │...│.  │ */                 'http://example.com/'.'0....'         => ['dirs' => DIR_ROOT,        'name' => '0....',     'type' => ''  ],
            /*  │0  │...│.. │ */                 'http://example.com/'.'0.....'        => ['dirs' => DIR_ROOT,        'name' => '0.....',    'type' => ''  ],
            /*  │0  │...│...│ */                 'http://example.com/'.'0......'       => ['dirs' => DIR_ROOT,        'name' => '0......',   'type' => ''  ],
            /*  │0  │...│0  │ */                 'http://example.com/'.'0...0'         => ['dirs' => DIR_ROOT,        'name' => '0..',       'type' => '0' ],
            /*  │0  │0  │"  │ */                 'http://example.com/'.'00'            => ['dirs' => DIR_ROOT,        'name' => '00',        'type' => ''  ],
            /*  │0  │0  │.  │ */                 'http://example.com/'.'00.'           => ['dirs' => DIR_ROOT,        'name' => '00.',       'type' => ''  ],
            /*  │0  │0  │.. │ */                 'http://example.com/'.'00..'          => ['dirs' => DIR_ROOT,        'name' => '00..',      'type' => ''  ],
            /*  │0  │0  │...│ */                 'http://example.com/'.'00...'         => ['dirs' => DIR_ROOT,        'name' => '00...',     'type' => ''  ],
            /*  │0  │0  │0  │ */                 'http://example.com/'.'000'           => ['dirs' => DIR_ROOT,        'name' => '000',       'type' => ''  ],
            /*  │.  │0  │.  │0  │.  │ */         'http://example.com/'.'.0.0.'         => ['dirs' => DIR_ROOT,        'name' => '.0.0.',     'type' => ''  ],
            /*  │.. │0  │.  │0  │.. │ */         'http://example.com/'.'..0.0..'       => ['dirs' => DIR_ROOT,        'name' => '..0.0..',   'type' => ''  ],
            /*  │.. │0  │.. │0  │.. │ */         'http://example.com/'.'..0..0..'      => ['dirs' => DIR_ROOT,        'name' => '..0..0..',  'type' => ''  ],

            # ─────────────────────────────────────────────────────────────────────
            # possible transpositions of '"|.|..|...|0' + DIR
            # ─────────────────────────────────────────────────────────────────────

            /*  │dir  │"  │   │   │ */           'http://example.com/'.'dir/'          => ['dirs' => DIR_ROOT,        'name' => 'dir',       'type' => ''  ], # redirect to 'http://example.com/dir'
            /*  │dir  │.  │   │   │ */           'http://example.com/'.'dir/.'         => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │dir  │.. │   │   │ */           'http://example.com/'.'dir/..'        => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │dir  │...│   │   │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...',       'type' => ''  ],
            /*  │dir  │0  │   │   │ */           'http://example.com/'.'dir/0'         => ['dirs' => DIR_ROOT.'dir/', 'name' => '0',         'type' => ''  ],
            /*  │dir  │"  │"  │   │ */           'http://example.com/'.'dir/'          => ['dirs' => DIR_ROOT,        'name' => 'dir',       'type' => ''  ], # redirect to 'http://example.com/dir'
            /*  │dir  │"  │.  │   │ */           'http://example.com/'.'dir/.'         => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │dir  │"  │.. │   │ */           'http://example.com/'.'dir/..'        => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │dir  │"  │...│   │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...',       'type' => ''  ],
            /*  │dir  │"  │0  │   │ */           'http://example.com/'.'dir/0'         => ['dirs' => DIR_ROOT.'dir/', 'name' => '0',         'type' => ''  ],
            /*  │dir  │.  │"  │   │ */           'http://example.com/'.'dir/.'         => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │dir  │.  │.  │   │ */           'http://example.com/'.'dir/..'        => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │dir  │.  │.. │   │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...',       'type' => ''  ],
            /*  │dir  │.  │...│   │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....',      'type' => ''  ],
            /*  │dir  │.  │0  │   │ */           'http://example.com/'.'dir/.0'        => ['dirs' => DIR_ROOT.'dir/', 'name' => '',          'type' => '0' ],
            /*  │dir  │.. │"  │   │ */           'http://example.com/'.'dir/..'        => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │dir  │.. │.  │   │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...',       'type' => ''  ],
            /*  │dir  │.. │.. │   │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....',      'type' => ''  ],
            /*  │dir  │.. │...│   │ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....',     'type' => ''  ],
            /*  │dir  │.. │0  │   │ */           'http://example.com/'.'dir/..0'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '.',         'type' => '0' ],
            /*  │dir  │...│"  │   │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...',       'type' => ''  ],
            /*  │dir  │...│.  │   │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....',      'type' => ''  ],
            /*  │dir  │...│.. │   │ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....',     'type' => ''  ],
            /*  │dir  │...│...│   │ */           'http://example.com/'.'dir/......'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '......',    'type' => ''  ],
            /*  │dir  │...│0  │   │ */           'http://example.com/'.'dir/...0'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '..',        'type' => '0' ],
            /*  │dir  │0  │"  │   │ */           'http://example.com/'.'dir/0'         => ['dirs' => DIR_ROOT.'dir/', 'name' => '0',         'type' => ''  ],
            /*  │dir  │0  │.  │   │ */           'http://example.com/'.'dir/0.'        => ['dirs' => DIR_ROOT.'dir/', 'name' => '0.',        'type' => ''  ],
            /*  │dir  │0  │.. │   │ */           'http://example.com/'.'dir/0..'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '0..',       'type' => ''  ],
            /*  │dir  │0  │...│   │ */           'http://example.com/'.'dir/0...'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '0...',      'type' => ''  ],
            /*  │dir  │0  │0  │   │ */           'http://example.com/'.'dir/00'        => ['dirs' => DIR_ROOT.'dir/', 'name' => '00',        'type' => ''  ],
            /*  │dir  │"  │"  │"  │ */           'http://example.com/'.'dir/'          => ['dirs' => DIR_ROOT,        'name' => 'dir',       'type' => ''  ], # redirect to 'http://example.com/dir'
            /*  │dir  │"  │"  │.  │ */           'http://example.com/'.'dir/.'         => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │dir  │"  │"  │.. │ */           'http://example.com/'.'dir/..'        => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │dir  │"  │"  │...│ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...',       'type' => ''  ],
            /*  │dir  │"  │"  │0  │ */           'http://example.com/'.'dir/0'         => ['dirs' => DIR_ROOT.'dir/', 'name' => '0',         'type' => ''  ],
            /*  │dir  │"  │.  │"  │ */           'http://example.com/'.'dir/.'         => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │dir  │"  │.  │.  │ */           'http://example.com/'.'dir/..'        => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │dir  │"  │.  │.. │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...',       'type' => ''  ],
            /*  │dir  │"  │.  │...│ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....',      'type' => ''  ],
            /*  │dir  │"  │.  │0  │ */           'http://example.com/'.'dir/.0'        => ['dirs' => DIR_ROOT.'dir/', 'name' => '',          'type' => '0' ],
            /*  │dir  │"  │.. │"  │ */           'http://example.com/'.'dir/..'        => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │dir  │"  │.. │.  │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...',       'type' => ''  ],
            /*  │dir  │"  │.. │.. │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....',      'type' => ''  ],
            /*  │dir  │"  │.. │...│ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....',     'type' => ''  ],
            /*  │dir  │"  │.. │0  │ */           'http://example.com/'.'dir/..0'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '.',         'type' => '0' ],
            /*  │dir  │"  │...│"  │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...',       'type' => ''  ],
            /*  │dir  │"  │...│.  │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....',      'type' => ''  ],
            /*  │dir  │"  │...│.. │ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....',     'type' => ''  ],
            /*  │dir  │"  │...│...│ */           'http://example.com/'.'dir/......'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '......',    'type' => ''  ],
            /*  │dir  │"  │...│0  │ */           'http://example.com/'.'dir/...0'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '..',        'type' => '0' ],
            /*  │dir  │"  │0  │"  │ */           'http://example.com/'.'dir/0'         => ['dirs' => DIR_ROOT.'dir/', 'name' => '0',         'type' => ''  ],
            /*  │dir  │"  │0  │.  │ */           'http://example.com/'.'dir/0.'        => ['dirs' => DIR_ROOT.'dir/', 'name' => '0.',        'type' => ''  ],
            /*  │dir  │"  │0  │.. │ */           'http://example.com/'.'dir/0..'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '0..',       'type' => ''  ],
            /*  │dir  │"  │0  │...│ */           'http://example.com/'.'dir/0...'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '0...',      'type' => ''  ],
            /*  │dir  │"  │0  │0  │ */           'http://example.com/'.'dir/00'        => ['dirs' => DIR_ROOT.'dir/', 'name' => '00',        'type' => ''  ],
            /*  │dir  │.  │"  │"  │ */           'http://example.com/'.'dir/.'         => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │dir  │.  │"  │.  │ */           'http://example.com/'.'dir/..'        => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │dir  │.  │"  │.. │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...',       'type' => ''  ],
            /*  │dir  │.  │"  │...│ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....',      'type' => ''  ],
            /*  │dir  │.  │"  │0  │ */           'http://example.com/'.'dir/.0'        => ['dirs' => DIR_ROOT.'dir/', 'name' => '',          'type' => '0' ],
            /*  │dir  │.  │.  │"  │ */           'http://example.com/'.'dir/..'        => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │dir  │.  │.  │.  │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...',       'type' => ''  ],
            /*  │dir  │.  │.  │.. │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....',      'type' => ''  ],
            /*  │dir  │.  │.  │...│ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....',     'type' => ''  ],
            /*  │dir  │.  │.  │0  │ */           'http://example.com/'.'dir/..0'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '.',         'type' => '0' ],
            /*  │dir  │.  │.. │"  │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...',       'type' => ''  ],
            /*  │dir  │.  │.. │.  │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....',      'type' => ''  ],
            /*  │dir  │.  │.. │.. │ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....',     'type' => ''  ],
            /*  │dir  │.  │.. │...│ */           'http://example.com/'.'dir/......'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '......',    'type' => ''  ],
            /*  │dir  │.  │.. │0  │ */           'http://example.com/'.'dir/...0'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '..',        'type' => '0' ],
            /*  │dir  │.  │...│"  │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....',      'type' => ''  ],
            /*  │dir  │.  │...│.  │ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....',     'type' => ''  ],
            /*  │dir  │.  │...│.. │ */           'http://example.com/'.'dir/......'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '......',    'type' => ''  ],
            /*  │dir  │.  │...│...│ */           'http://example.com/'.'dir/.......'   => ['dirs' => DIR_ROOT.'dir/', 'name' => '.......',   'type' => ''  ],
            /*  │dir  │.  │...│0  │ */           'http://example.com/'.'dir/....0'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '...',       'type' => '0' ],
            /*  │dir  │.  │0  │"  │ */           'http://example.com/'.'dir/.0'        => ['dirs' => DIR_ROOT.'dir/', 'name' => '',          'type' => '0' ],
            /*  │dir  │.  │0  │.  │ */           'http://example.com/'.'dir/.0.'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '.0.',       'type' => ''  ],
            /*  │dir  │.  │0  │.. │ */           'http://example.com/'.'dir/.0..'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '.0..',      'type' => ''  ],
            /*  │dir  │.  │0  │...│ */           'http://example.com/'.'dir/.0...'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.0...',     'type' => ''  ],
            /*  │dir  │.  │0  │0  │ */           'http://example.com/'.'dir/.00'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '',          'type' => '00'],
            /*  │dir  │.. │"  │"  │ */           'http://example.com/'.'dir/..'        => ['dirs' => null,            'name' => null,        'type' => null], # reserved
            /*  │dir  │.. │"  │.  │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...',       'type' => ''  ],
            /*  │dir  │.. │"  │.. │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....',      'type' => ''  ],
            /*  │dir  │.. │"  │...│ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....',     'type' => ''  ],
            /*  │dir  │.. │"  │0  │ */           'http://example.com/'.'dir/..0'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '.',         'type' => '0' ],
            /*  │dir  │.. │.  │"  │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...',       'type' => ''  ],
            /*  │dir  │.. │.  │.  │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....',      'type' => ''  ],
            /*  │dir  │.. │.  │.. │ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....',     'type' => ''  ],
            /*  │dir  │.. │.  │...│ */           'http://example.com/'.'dir/......'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '......',    'type' => ''  ],
            /*  │dir  │.. │.  │0  │ */           'http://example.com/'.'dir/...0'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '..',        'type' => '0' ],
            /*  │dir  │.. │.. │"  │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....',      'type' => ''  ],
            /*  │dir  │.. │.. │.  │ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....',     'type' => ''  ],
            /*  │dir  │.. │.. │.. │ */           'http://example.com/'.'dir/......'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '......',    'type' => ''  ],
            /*  │dir  │.. │.. │...│ */           'http://example.com/'.'dir/.......'   => ['dirs' => DIR_ROOT.'dir/', 'name' => '.......',   'type' => ''  ],
            /*  │dir  │.. │.. │0  │ */           'http://example.com/'.'dir/....0'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '...',       'type' => '0' ],
            /*  │dir  │.. │...│"  │ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....',     'type' => ''  ],
            /*  │dir  │.. │...│.  │ */           'http://example.com/'.'dir/......'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '......',    'type' => ''  ],
            /*  │dir  │.. │...│.. │ */           'http://example.com/'.'dir/.......'   => ['dirs' => DIR_ROOT.'dir/', 'name' => '.......',   'type' => ''  ],
            /*  │dir  │.. │...│...│ */           'http://example.com/'.'dir/........'  => ['dirs' => DIR_ROOT.'dir/', 'name' => '........',  'type' => ''  ],
            /*  │dir  │.. │...│0  │ */           'http://example.com/'.'dir/.....0'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '....',      'type' => '0' ],
            /*  │dir  │.. │0  │"  │ */           'http://example.com/'.'dir/..0'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '.',         'type' => '0' ],
            /*  │dir  │.. │0  │.  │ */           'http://example.com/'.'dir/..0.'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '..0.',      'type' => ''  ],
            /*  │dir  │.. │0  │.. │ */           'http://example.com/'.'dir/..0..'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '..0..',     'type' => ''  ],
            /*  │dir  │.. │0  │...│ */           'http://example.com/'.'dir/..0...'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '..0...',    'type' => ''  ],
            /*  │dir  │.. │0  │0  │ */           'http://example.com/'.'dir/..00'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '.',         'type' => '00'],
            /*  │dir  │...│"  │"  │ */           'http://example.com/'.'dir/...'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '...',       'type' => ''  ],
            /*  │dir  │...│"  │.  │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....',      'type' => ''  ],
            /*  │dir  │...│"  │.. │ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....',     'type' => ''  ],
            /*  │dir  │...│"  │...│ */           'http://example.com/'.'dir/......'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '......',    'type' => ''  ],
            /*  │dir  │...│"  │0  │ */           'http://example.com/'.'dir/...0'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '..',        'type' => '0' ],
            /*  │dir  │...│.  │"  │ */           'http://example.com/'.'dir/....'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '....',      'type' => ''  ],
            /*  │dir  │...│.  │.  │ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....',     'type' => ''  ],
            /*  │dir  │...│.  │.. │ */           'http://example.com/'.'dir/......'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '......',    'type' => ''  ],
            /*  │dir  │...│.  │...│ */           'http://example.com/'.'dir/.......'   => ['dirs' => DIR_ROOT.'dir/', 'name' => '.......',   'type' => ''  ],
            /*  │dir  │...│.  │0  │ */           'http://example.com/'.'dir/....0'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '...',       'type' => '0' ],
            /*  │dir  │...│.. │"  │ */           'http://example.com/'.'dir/.....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....',     'type' => ''  ],
            /*  │dir  │...│.. │.  │ */           'http://example.com/'.'dir/......'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '......',    'type' => ''  ],
            /*  │dir  │...│.. │.. │ */           'http://example.com/'.'dir/.......'   => ['dirs' => DIR_ROOT.'dir/', 'name' => '.......',   'type' => ''  ],
            /*  │dir  │...│.. │...│ */           'http://example.com/'.'dir/........'  => ['dirs' => DIR_ROOT.'dir/', 'name' => '........',  'type' => ''  ],
            /*  │dir  │...│.. │0  │ */           'http://example.com/'.'dir/.....0'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '....',      'type' => '0' ],
            /*  │dir  │...│...│"  │ */           'http://example.com/'.'dir/......'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '......',    'type' => ''  ],
            /*  │dir  │...│...│.  │ */           'http://example.com/'.'dir/.......'   => ['dirs' => DIR_ROOT.'dir/', 'name' => '.......',   'type' => ''  ],
            /*  │dir  │...│...│.. │ */           'http://example.com/'.'dir/........'  => ['dirs' => DIR_ROOT.'dir/', 'name' => '........',  'type' => ''  ],
            /*  │dir  │...│...│...│ */           'http://example.com/'.'dir/.........' => ['dirs' => DIR_ROOT.'dir/', 'name' => '.........', 'type' => ''  ],
            /*  │dir  │...│...│0  │ */           'http://example.com/'.'dir/......0'   => ['dirs' => DIR_ROOT.'dir/', 'name' => '.....',     'type' => '0' ],
            /*  │dir  │...│0  │"  │ */           'http://example.com/'.'dir/...0'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '..',        'type' => '0' ],
            /*  │dir  │...│0  │.  │ */           'http://example.com/'.'dir/...0.'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '...0.',     'type' => ''  ],
            /*  │dir  │...│0  │.. │ */           'http://example.com/'.'dir/...0..'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '...0..',    'type' => ''  ],
            /*  │dir  │...│0  │...│ */           'http://example.com/'.'dir/...0...'   => ['dirs' => DIR_ROOT.'dir/', 'name' => '...0...',   'type' => ''  ],
            /*  │dir  │...│0  │0  │ */           'http://example.com/'.'dir/...00'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '..',        'type' => '00'],
            /*  │dir  │0  │"  │"  │ */           'http://example.com/'.'dir/0'         => ['dirs' => DIR_ROOT.'dir/', 'name' => '0',         'type' => ''  ],
            /*  │dir  │0  │"  │.  │ */           'http://example.com/'.'dir/0.'        => ['dirs' => DIR_ROOT.'dir/', 'name' => '0.',        'type' => ''  ],
            /*  │dir  │0  │"  │.. │ */           'http://example.com/'.'dir/0..'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '0..',       'type' => ''  ],
            /*  │dir  │0  │"  │...│ */           'http://example.com/'.'dir/0...'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '0...',      'type' => ''  ],
            /*  │dir  │0  │"  │0  │ */           'http://example.com/'.'dir/00'        => ['dirs' => DIR_ROOT.'dir/', 'name' => '00',        'type' => ''  ],
            /*  │dir  │0  │.  │"  │ */           'http://example.com/'.'dir/0.'        => ['dirs' => DIR_ROOT.'dir/', 'name' => '0.',        'type' => ''  ],
            /*  │dir  │0  │.  │.  │ */           'http://example.com/'.'dir/0..'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '0..',       'type' => ''  ],
            /*  │dir  │0  │.  │.. │ */           'http://example.com/'.'dir/0...'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '0...',      'type' => ''  ],
            /*  │dir  │0  │.  │...│ */           'http://example.com/'.'dir/0....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '0....',     'type' => ''  ],
            /*  │dir  │0  │.  │0  │ */           'http://example.com/'.'dir/0.0'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '0',         'type' => '0' ],
            /*  │dir  │0  │.. │"  │ */           'http://example.com/'.'dir/0..'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '0..',       'type' => ''  ],
            /*  │dir  │0  │.. │.  │ */           'http://example.com/'.'dir/0...'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '0...',      'type' => ''  ],
            /*  │dir  │0  │.. │.. │ */           'http://example.com/'.'dir/0....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '0....',     'type' => ''  ],
            /*  │dir  │0  │.. │...│ */           'http://example.com/'.'dir/0.....'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '0.....',    'type' => ''  ],
            /*  │dir  │0  │.. │0  │ */           'http://example.com/'.'dir/0..0'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '0.',        'type' => '0' ],
            /*  │dir  │0  │...│"  │ */           'http://example.com/'.'dir/0...'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '0...',      'type' => ''  ],
            /*  │dir  │0  │...│.  │ */           'http://example.com/'.'dir/0....'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '0....',     'type' => ''  ],
            /*  │dir  │0  │...│.. │ */           'http://example.com/'.'dir/0.....'    => ['dirs' => DIR_ROOT.'dir/', 'name' => '0.....',    'type' => ''  ],
            /*  │dir  │0  │...│...│ */           'http://example.com/'.'dir/0......'   => ['dirs' => DIR_ROOT.'dir/', 'name' => '0......',   'type' => ''  ],
            /*  │dir  │0  │...│0  │ */           'http://example.com/'.'dir/0...0'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '0..',       'type' => '0' ],
            /*  │dir  │0  │0  │"  │ */           'http://example.com/'.'dir/00'        => ['dirs' => DIR_ROOT.'dir/', 'name' => '00',        'type' => ''  ],
            /*  │dir  │0  │0  │.  │ */           'http://example.com/'.'dir/00.'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '00.',       'type' => ''  ],
            /*  │dir  │0  │0  │.. │ */           'http://example.com/'.'dir/00..'      => ['dirs' => DIR_ROOT.'dir/', 'name' => '00..',      'type' => ''  ],
            /*  │dir  │0  │0  │...│ */           'http://example.com/'.'dir/00...'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '00...',     'type' => ''  ],
            /*  │dir  │0  │0  │0  │ */           'http://example.com/'.'dir/000'       => ['dirs' => DIR_ROOT.'dir/', 'name' => '000',       'type' => ''  ],
            /*  │dir  │.  │0  │.  │0  │.  │ */   'http://example.com/'.'dir/.0.0.'     => ['dirs' => DIR_ROOT.'dir/', 'name' => '.0.0.',     'type' => ''  ],
            /*  │dir  │.. │0  │.  │0  │.. │ */   'http://example.com/'.'dir/..0.0..'   => ['dirs' => DIR_ROOT.'dir/', 'name' => '..0.0..',   'type' => ''  ],
            /*  │dir  │.. │0  │.. │0  │.. │ */   'http://example.com/'.'dir/..0..0..'  => ['dirs' => DIR_ROOT.'dir/', 'name' => '..0..0..',  'type' => ''  ],

        ];

        foreach ($data as $c_value => $c_expected) {
            $c_real_url = rtrim($c_value, '/'); # redirect emulation
            $c_gotten = (array)((new Url($c_real_url))->file_info_get());
            $c_result = $c_gotten                                 &&
                        $c_gotten['dirs'] === $c_expected['dirs'] &&
                        $c_gotten['name'] === $c_expected['name'] &&
                        $c_gotten['type'] === $c_expected['type'];
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"', ['value' => Core::return_encoded(serialize($c_expected))]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded(serialize($c_gotten))]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__construct(&$test, $dpath, &$c_results) {
        $data = [
                                   '/'                                         => ['protocol' => Request::scheme_get(), 'domain' => Request::host_get(),   'path' => '/',                    'query' => '',              'anchor' => ''      ],
                                   '/?key=value'                               => ['protocol' => Request::scheme_get(), 'domain' => Request::host_get(),   'path' => '/',                    'query' => 'key=value',     'anchor' => ''      ],
                                   '/#anchor'                                  => ['protocol' => Request::scheme_get(), 'domain' => Request::host_get(),   'path' => '/',                    'query' => '',              'anchor' => 'anchor'],
                                   '/?key=value#anchor'                        => ['protocol' => Request::scheme_get(), 'domain' => Request::host_get(),   'path' => '/',                    'query' => 'key=value',     'anchor' => 'anchor'],
                                   '/dir/subdir/page'                          => ['protocol' => Request::scheme_get(), 'domain' => Request::host_get(),   'path' => '/dir/subdir/page',     'query' => '',              'anchor' => ''      ],
                                   '/dir/subdir/page?key=value'                => ['protocol' => Request::scheme_get(), 'domain' => Request::host_get(),   'path' => '/dir/subdir/page',     'query' => 'key=value',     'anchor' => ''      ],
                                   '/dir/subdir/page#anchor'                   => ['protocol' => Request::scheme_get(), 'domain' => Request::host_get(),   'path' => '/dir/subdir/page',     'query' => '',              'anchor' => 'anchor'],
                                   '/dir/subdir/page?key=value#anchor'         => ['protocol' => Request::scheme_get(), 'domain' => Request::host_get(),   'path' => '/dir/subdir/page',     'query' => 'key=value',     'anchor' => 'anchor'],
                   'subdomain.domain'                                          => ['protocol' => 'http',                'domain' => 'subdomain.domain',    'path' => '/',                    'query' => '',              'anchor' => ''      ],
                   'subdomain.domain/?key=value'                               => ['protocol' => 'http',                'domain' => 'subdomain.domain',    'path' => '/',                    'query' => 'key=value',     'anchor' => ''      ],
                   'subdomain.domain/#anchor'                                  => ['protocol' => 'http',                'domain' => 'subdomain.domain',    'path' => '/',                    'query' => '',              'anchor' => 'anchor'],
                   'subdomain.domain/?key=value#anchor'                        => ['protocol' => 'http',                'domain' => 'subdomain.domain',    'path' => '/',                    'query' => 'key=value',     'anchor' => 'anchor'],
                   'subdomain.domain/dir/subdir/page'                          => ['protocol' => 'http',                'domain' => 'subdomain.domain',    'path' => '/dir/subdir/page',     'query' => '',              'anchor' => ''      ],
                   'subdomain.domain/dir/subdir/page?key=value'                => ['protocol' => 'http',                'domain' => 'subdomain.domain',    'path' => '/dir/subdir/page',     'query' => 'key=value',     'anchor' => ''      ],
                   'subdomain.domain/dir/subdir/page#anchor'                   => ['protocol' => 'http',                'domain' => 'subdomain.domain',    'path' => '/dir/subdir/page',     'query' => '',              'anchor' => 'anchor'],
                   'subdomain.domain/dir/subdir/page?key=value#anchor'         => ['protocol' => 'http',                'domain' => 'subdomain.domain',    'path' => '/dir/subdir/page',     'query' => 'key=value',     'anchor' => 'anchor'],
                   'subdomain.domain:80'                                       => ['protocol' => 'http',                'domain' => 'subdomain.domain:80', 'path' => '/',                    'query' => '',              'anchor' => ''      ],
                   'subdomain.domain:80/?key=value'                            => ['protocol' => 'http',                'domain' => 'subdomain.domain:80', 'path' => '/',                    'query' => 'key=value',     'anchor' => ''      ],
                   'subdomain.domain:80/#anchor'                               => ['protocol' => 'http',                'domain' => 'subdomain.domain:80', 'path' => '/',                    'query' => '',              'anchor' => 'anchor'],
                   'subdomain.domain:80/?key=value#anchor'                     => ['protocol' => 'http',                'domain' => 'subdomain.domain:80', 'path' => '/',                    'query' => 'key=value',     'anchor' => 'anchor'],
                   'subdomain.domain:80/dir/subdir/page'                       => ['protocol' => 'http',                'domain' => 'subdomain.domain:80', 'path' => '/dir/subdir/page',     'query' => '',              'anchor' => ''      ],
                   'subdomain.domain:80/dir/subdir/page?key=value'             => ['protocol' => 'http',                'domain' => 'subdomain.domain:80', 'path' => '/dir/subdir/page',     'query' => 'key=value',     'anchor' => ''      ],
                   'subdomain.domain:80/dir/subdir/page#anchor'                => ['protocol' => 'http',                'domain' => 'subdomain.domain:80', 'path' => '/dir/subdir/page',     'query' => '',              'anchor' => 'anchor'],
                   'subdomain.domain:80/dir/subdir/page?key=value#anchor'      => ['protocol' => 'http',                'domain' => 'subdomain.domain:80', 'path' => '/dir/subdir/page',     'query' => 'key=value',     'anchor' => 'anchor'],
            'http://subdomain.domain'                                          => ['protocol' => 'http',                'domain' => 'subdomain.domain',    'path' => '/',                    'query' => '',              'anchor' => ''      ],
            'http://subdomain.domain/?key=value'                               => ['protocol' => 'http',                'domain' => 'subdomain.domain',    'path' => '/',                    'query' => 'key=value',     'anchor' => ''      ],
            'http://subdomain.domain/#anchor'                                  => ['protocol' => 'http',                'domain' => 'subdomain.domain',    'path' => '/',                    'query' => '',              'anchor' => 'anchor'],
            'http://subdomain.domain/?key=value#anchor'                        => ['protocol' => 'http',                'domain' => 'subdomain.domain',    'path' => '/',                    'query' => 'key=value',     'anchor' => 'anchor'],
            'http://subdomain.domain/dir/subdir/page'                          => ['protocol' => 'http',                'domain' => 'subdomain.domain',    'path' => '/dir/subdir/page',     'query' => '',              'anchor' => ''      ],
            'http://subdomain.domain/dir/subdir/page?key=value'                => ['protocol' => 'http',                'domain' => 'subdomain.domain',    'path' => '/dir/subdir/page',     'query' => 'key=value',     'anchor' => ''      ],
            'http://subdomain.domain/dir/subdir/page#anchor'                   => ['protocol' => 'http',                'domain' => 'subdomain.domain',    'path' => '/dir/subdir/page',     'query' => '',              'anchor' => 'anchor'],
            'http://subdomain.domain/dir/subdir/page?key=value#anchor'         => ['protocol' => 'http',                'domain' => 'subdomain.domain',    'path' => '/dir/subdir/page',     'query' => 'key=value',     'anchor' => 'anchor'],
            'http://subdomain.domain:80'                                       => ['protocol' => 'http',                'domain' => 'subdomain.domain:80', 'path' => '/',                    'query' => '',              'anchor' => ''      ],
            'http://subdomain.domain:80/?key=value'                            => ['protocol' => 'http',                'domain' => 'subdomain.domain:80', 'path' => '/',                    'query' => 'key=value',     'anchor' => ''      ],
            'http://subdomain.domain:80/#anchor'                               => ['protocol' => 'http',                'domain' => 'subdomain.domain:80', 'path' => '/',                    'query' => '',              'anchor' => 'anchor'],
            'http://subdomain.domain:80/?key=value#anchor'                     => ['protocol' => 'http',                'domain' => 'subdomain.domain:80', 'path' => '/',                    'query' => 'key=value',     'anchor' => 'anchor'],
            'http://subdomain.domain:80/dir/subdir/page'                       => ['protocol' => 'http',                'domain' => 'subdomain.domain:80', 'path' => '/dir/subdir/page',     'query' => '',              'anchor' => ''      ],
            'http://subdomain.domain:80/dir/subdir/page?key=value'             => ['protocol' => 'http',                'domain' => 'subdomain.domain:80', 'path' => '/dir/subdir/page',     'query' => 'key=value',     'anchor' => ''      ],
            'http://subdomain.domain:80/dir/subdir/page#anchor'                => ['protocol' => 'http',                'domain' => 'subdomain.domain:80', 'path' => '/dir/subdir/page',     'query' => '',              'anchor' => 'anchor'],
            'http://subdomain.domain:80/dir/subdir/page?key=value#anchor'      => ['protocol' => 'http',                'domain' => 'subdomain.domain:80', 'path' => '/dir/subdir/page',     'query' => 'key=value',     'anchor' => 'anchor'],
                                 '/?ключ=значение'                             => ['protocol' => Request::scheme_get(), 'domain' => Request::host_get(),   'path' => '/',                    'query' => 'ключ=значение', 'anchor' => ''      ],
                                 '/#якорь'                                     => ['protocol' => Request::scheme_get(), 'domain' => Request::host_get(),   'path' => '/',                    'query' => '',              'anchor' => 'якорь' ],
                                 '/?ключ=значение#якорь'                       => ['protocol' => Request::scheme_get(), 'domain' => Request::host_get(),   'path' => '/',                    'query' => 'ключ=значение', 'anchor' => 'якорь' ],
                                 '/дир/субдир/страница'                        => ['protocol' => Request::scheme_get(), 'domain' => Request::host_get(),   'path' => '/дир/субдир/страница', 'query' => '',              'anchor' => ''      ],
                                 '/дир/субдир/страница?ключ=значение'          => ['protocol' => Request::scheme_get(), 'domain' => Request::host_get(),   'path' => '/дир/субдир/страница', 'query' => 'ключ=значение', 'anchor' => ''      ],
                                 '/дир/субдир/страница#якорь'                  => ['protocol' => Request::scheme_get(), 'domain' => Request::host_get(),   'path' => '/дир/субдир/страница', 'query' => '',              'anchor' => 'якорь' ],
                                 '/дир/субдир/страница?ключ=значение#якорь'    => ['protocol' => Request::scheme_get(), 'domain' => Request::host_get(),   'path' => '/дир/субдир/страница', 'query' => 'ключ=значение', 'anchor' => 'якорь' ],
                   'субдомен.домен/?ключ=значение'                             => ['protocol' => 'http',                'domain' => 'субдомен.домен',      'path' => '/',                    'query' => 'ключ=значение', 'anchor' => ''      ],
                   'субдомен.домен/#якорь'                                     => ['protocol' => 'http',                'domain' => 'субдомен.домен',      'path' => '/',                    'query' => '',              'anchor' => 'якорь' ],
                   'субдомен.домен/?ключ=значение#якорь'                       => ['protocol' => 'http',                'domain' => 'субдомен.домен',      'path' => '/',                    'query' => 'ключ=значение', 'anchor' => 'якорь' ],
                   'субдомен.домен/дир/субдир/страница'                        => ['protocol' => 'http',                'domain' => 'субдомен.домен',      'path' => '/дир/субдир/страница', 'query' => '',              'anchor' => ''      ],
                   'субдомен.домен/дир/субдир/страница?ключ=значение'          => ['protocol' => 'http',                'domain' => 'субдомен.домен',      'path' => '/дир/субдир/страница', 'query' => 'ключ=значение', 'anchor' => ''      ],
                   'субдомен.домен/дир/субдир/страница#якорь'                  => ['protocol' => 'http',                'domain' => 'субдомен.домен',      'path' => '/дир/субдир/страница', 'query' => '',              'anchor' => 'якорь' ],
                   'субдомен.домен/дир/субдир/страница?ключ=значение#якорь'    => ['protocol' => 'http',                'domain' => 'субдомен.домен',      'path' => '/дир/субдир/страница', 'query' => 'ключ=значение', 'anchor' => 'якорь' ],
                   'субдомен.домен:80/?ключ=значение'                          => ['protocol' => 'http',                'domain' => 'субдомен.домен:80',   'path' => '/',                    'query' => 'ключ=значение', 'anchor' => ''      ],
                   'субдомен.домен:80/#якорь'                                  => ['protocol' => 'http',                'domain' => 'субдомен.домен:80',   'path' => '/',                    'query' => '',              'anchor' => 'якорь' ],
                   'субдомен.домен:80/?ключ=значение#якорь'                    => ['protocol' => 'http',                'domain' => 'субдомен.домен:80',   'path' => '/',                    'query' => 'ключ=значение', 'anchor' => 'якорь' ],
                   'субдомен.домен:80/дир/субдир/страница'                     => ['protocol' => 'http',                'domain' => 'субдомен.домен:80',   'path' => '/дир/субдир/страница', 'query' => '',              'anchor' => ''      ],
                   'субдомен.домен:80/дир/субдир/страница?ключ=значение'       => ['protocol' => 'http',                'domain' => 'субдомен.домен:80',   'path' => '/дир/субдир/страница', 'query' => 'ключ=значение', 'anchor' => ''      ],
                   'субдомен.домен:80/дир/субдир/страница#якорь'               => ['protocol' => 'http',                'domain' => 'субдомен.домен:80',   'path' => '/дир/субдир/страница', 'query' => '',              'anchor' => 'якорь' ],
                   'субдомен.домен:80/дир/субдир/страница?ключ=значение#якорь' => ['protocol' => 'http',                'domain' => 'субдомен.домен:80',   'path' => '/дир/субдир/страница', 'query' => 'ключ=значение', 'anchor' => 'якорь' ],
            'http://субдомен.домен/?ключ=значение'                             => ['protocol' => 'http',                'domain' => 'субдомен.домен',      'path' => '/',                    'query' => 'ключ=значение', 'anchor' => ''      ],
            'http://субдомен.домен/#якорь'                                     => ['protocol' => 'http',                'domain' => 'субдомен.домен',      'path' => '/',                    'query' => '',              'anchor' => 'якорь' ],
            'http://субдомен.домен/?ключ=значение#якорь'                       => ['protocol' => 'http',                'domain' => 'субдомен.домен',      'path' => '/',                    'query' => 'ключ=значение', 'anchor' => 'якорь' ],
            'http://субдомен.домен/дир/субдир/страница'                        => ['protocol' => 'http',                'domain' => 'субдомен.домен',      'path' => '/дир/субдир/страница', 'query' => '',              'anchor' => ''      ],
            'http://субдомен.домен/дир/субдир/страница?ключ=значение'          => ['protocol' => 'http',                'domain' => 'субдомен.домен',      'path' => '/дир/субдир/страница', 'query' => 'ключ=значение', 'anchor' => ''      ],
            'http://субдомен.домен/дир/субдир/страница#якорь'                  => ['protocol' => 'http',                'domain' => 'субдомен.домен',      'path' => '/дир/субдир/страница', 'query' => '',              'anchor' => 'якорь' ],
            'http://субдомен.домен/дир/субдир/страница?ключ=значение#якорь'    => ['protocol' => 'http',                'domain' => 'субдомен.домен',      'path' => '/дир/субдир/страница', 'query' => 'ключ=значение', 'anchor' => 'якорь' ],
            'http://субдомен.домен:80/?ключ=значение'                          => ['protocol' => 'http',                'domain' => 'субдомен.домен:80',   'path' => '/',                    'query' => 'ключ=значение', 'anchor' => ''      ],
            'http://субдомен.домен:80/#якорь'                                  => ['protocol' => 'http',                'domain' => 'субдомен.домен:80',   'path' => '/',                    'query' => '',              'anchor' => 'якорь' ],
            'http://субдомен.домен:80/?ключ=значение#якорь'                    => ['protocol' => 'http',                'domain' => 'субдомен.домен:80',   'path' => '/',                    'query' => 'ключ=значение', 'anchor' => 'якорь' ],
            'http://субдомен.домен:80/дир/субдир/страница'                     => ['protocol' => 'http',                'domain' => 'субдомен.домен:80',   'path' => '/дир/субдир/страница', 'query' => '',              'anchor' => ''      ],
            'http://субдомен.домен:80/дир/субдир/страница?ключ=значение'       => ['protocol' => 'http',                'domain' => 'субдомен.домен:80',   'path' => '/дир/субдир/страница', 'query' => 'ключ=значение', 'anchor' => ''      ],
            'http://субдомен.домен:80/дир/субдир/страница#якорь'               => ['protocol' => 'http',                'domain' => 'субдомен.домен:80',   'path' => '/дир/субдир/страница', 'query' => '',              'anchor' => 'якорь' ],
            'http://субдомен.домен:80/дир/субдир/страница?ключ=значение#якорь' => ['protocol' => 'http',                'domain' => 'субдомен.домен:80',   'path' => '/дир/субдир/страница', 'query' => 'ключ=значение', 'anchor' => 'якорь' ],
        ];

        foreach ($data as $c_value => $c_expected) {
            $c_url = new Url($c_value);
            $c_result = $c_url->protocol === $c_expected['protocol'] &&
                        $c_url->domain   === $c_expected['domain']   &&
                        $c_url->path     === $c_expected['path']     &&
                        $c_url->query    === $c_expected['query']    &&
                        $c_url->anchor   === $c_expected['anchor'];
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"', ['value' => Core::return_encoded(serialize($c_expected))]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded(serialize($c_url))]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__has_error(&$test, $dpath, &$c_results) {
        $data = [
            ':',
            ':/',
            'http:',
            'http:/',
            'http:///',
            'http:///path/',
            'http:/domain/path?key=value',
            'javascript://%0Aalert(document.cookie)'
        ];

        foreach ($data as $c_value) {
            $c_url = new Url($c_value);
            $c_expected = true;
            $c_result = $c_url->has_error === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"', ['value' => $c_expected ? 'true' : 'false']);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => $c_url->has_error ? 'true' : 'false']);
                $c_results['return'] = 0;
                return;
            }
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $expected = [
            '://:80?key=value' => true,
            '://:80?key=value#anchor' => true,
            '://:80' => true,
            '://:80/dir/subdir/page?key=value' => true,
            '://:80/dir/subdir/page?key=value#anchor' => true,
            '://:80/dir/subdir/page' => true,
            '://:80/dir/subdir/page#anchor' => true,
            '://:80#anchor' => true,
            '://?key=value' => true,
            '://?key=value#anchor' => true,
            '://' => true,
            ':///dir/subdir/page?key=value' => true,
            ':///dir/subdir/page?key=value#anchor' => true,
            ':///dir/subdir/page' => true,
            ':///dir/subdir/page#anchor' => true,
            '://#anchor' => true,
            '://subdomain.domain:80?key=value' => true,
            '://subdomain.domain:80?key=value#anchor' => true,
            '://subdomain.domain:80' => true,
            '://subdomain.domain:80/dir/subdir/page?key=value' => true,
            '://subdomain.domain:80/dir/subdir/page?key=value#anchor' => true,
            '://subdomain.domain:80/dir/subdir/page' => true,
            '://subdomain.domain:80/dir/subdir/page#anchor' => true,
            '://subdomain.domain:80#anchor' => true,
            '://subdomain.domain?key=value' => true,
            '://subdomain.domain?key=value#anchor' => true,
            '://subdomain.domain' => true,
            '://subdomain.domain/dir/subdir/page?key=value' => true,
            '://subdomain.domain/dir/subdir/page?key=value#anchor' => true,
            '://subdomain.domain/dir/subdir/page' => true,
            '://subdomain.domain/dir/subdir/page#anchor' => true,
            '://subdomain.domain#anchor' => true,
            ':80?key=value' => true,
            ':80?key=value#anchor' => true,
            ':80' => true,
            ':80/dir/subdir/page?key=value' => true,
            ':80/dir/subdir/page?key=value#anchor' => true,
            ':80/dir/subdir/page' => true,
            ':80/dir/subdir/page#anchor' => true,
            ':80#anchor' => true,
            '?key=value' => true,
            '?key=value#anchor' => true,
            '' => true,
            '#anchor' => true,
            'http://:80?key=value' => true,
            'http://:80?key=value#anchor' => true,
            'http://:80' => true,
            'http://:80/dir/subdir/page?key=value' => true,
            'http://:80/dir/subdir/page?key=value#anchor' => true,
            'http://:80/dir/subdir/page' => true,
            'http://:80/dir/subdir/page#anchor' => true,
            'http://:80#anchor' => true,
            'http://?key=value' => true,
            'http://?key=value#anchor' => true,
            'http://' => true,
            'http:///dir/subdir/page?key=value' => true,
            'http:///dir/subdir/page?key=value#anchor' => true,
            'http:///dir/subdir/page' => true,
            'http:///dir/subdir/page#anchor' => true,
            'http://#anchor' => true,
            'http://subdomain.domain:80?key=value' => true,
            'http://subdomain.domain:80?key=value#anchor' => true,
            'http://subdomain.domain:80#anchor' => true,
            'http://subdomain.domain?key=value' => true,
            'http://subdomain.domain?key=value#anchor' => true,
            'http://subdomain.domain#anchor' => true,
            'http:80?key=value' => true,
            'http:80?key=value#anchor' => true,
            'http:80#anchor' => true,
            'http?key=value' => true,
            'http?key=value#anchor' => true,
            'http#anchor' => true,
            'httpsubdomain.domain:80?key=value' => true,
            'httpsubdomain.domain:80?key=value#anchor' => true,
            'httpsubdomain.domain:80#anchor' => true,
            'httpsubdomain.domain?key=value' => true,
            'httpsubdomain.domain?key=value#anchor' => true,
            'httpsubdomain.domain#anchor' => true,
            'subdomain.domain:80?key=value' => true,
            'subdomain.domain:80?key=value#anchor' => true,
            'subdomain.domain:80#anchor' => true,
            'subdomain.domain?key=value' => true,
            'subdomain.domain?key=value#anchor' => true,
            'subdomain.domain#anchor' => true,
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            '/dir/subdir/page?key=value' => false,
            '/dir/subdir/page?key=value#anchor' => false,
            '/dir/subdir/page' => false,
            '/dir/subdir/page#anchor' => false,
            'http://subdomain.domain:80' => false,
            'http://subdomain.domain:80/dir/subdir/page?key=value' => false,
            'http://subdomain.domain:80/dir/subdir/page?key=value#anchor' => false,
            'http://subdomain.domain:80/dir/subdir/page' => false,
            'http://subdomain.domain:80/dir/subdir/page#anchor' => false,
            'http://subdomain.domain' => false,
            'http://subdomain.domain/dir/subdir/page?key=value' => false,
            'http://subdomain.domain/dir/subdir/page?key=value#anchor' => false,
            'http://subdomain.domain/dir/subdir/page' => false,
            'http://subdomain.domain/dir/subdir/page#anchor' => false,
            'http:80' => false,
            'http:80/dir/subdir/page?key=value' => false,
            'http:80/dir/subdir/page?key=value#anchor' => false,
            'http:80/dir/subdir/page' => false,
            'http:80/dir/subdir/page#anchor' => false,
            'http' => false,
            'http/dir/subdir/page?key=value' => false,
            'http/dir/subdir/page?key=value#anchor' => false,
            'http/dir/subdir/page' => false,
            'http/dir/subdir/page#anchor' => false,
            'httpsubdomain.domain:80' => false,
            'httpsubdomain.domain:80/dir/subdir/page?key=value' => false,
            'httpsubdomain.domain:80/dir/subdir/page?key=value#anchor' => false,
            'httpsubdomain.domain:80/dir/subdir/page' => false,
            'httpsubdomain.domain:80/dir/subdir/page#anchor' => false,
            'httpsubdomain.domain' => false,
            'httpsubdomain.domain/dir/subdir/page?key=value' => false,
            'httpsubdomain.domain/dir/subdir/page?key=value#anchor' => false,
            'httpsubdomain.domain/dir/subdir/page' => false,
            'httpsubdomain.domain/dir/subdir/page#anchor' => false,
            'subdomain.domain:80' => false,
            'subdomain.domain:80/dir/subdir/page?key=value' => false,
            'subdomain.domain:80/dir/subdir/page?key=value#anchor' => false,
            'subdomain.domain:80/dir/subdir/page' => false,
            'subdomain.domain:80/dir/subdir/page#anchor' => false,
            'subdomain.domain' => false,
            'subdomain.domain/dir/subdir/page?key=value' => false,
            'subdomain.domain/dir/subdir/page?key=value#anchor' => false,
            'subdomain.domain/dir/subdir/page' => false,
            'subdomain.domain/dir/subdir/page#anchor' => false
        ];

        $parts = [
            1 => 'http',
            2 => '://',
            3 => 'subdomain.domain',
            4 => ':80',
            5 => '/dir/subdir/page',
            6 => '?key=value',
            7 => '#anchor'
        ];

        for ($i = 0b0000000; $i <= 0b1111111; $i++) {
            $c_value = '';
            if ($i & 0b0000001) $c_value.= $parts[1];
            if ($i & 0b0000010) $c_value.= $parts[2];
            if ($i & 0b0000100) $c_value.= $parts[3];
            if ($i & 0b0001000) $c_value.= $parts[4];
            if ($i & 0b0010000) $c_value.= $parts[5];
            if ($i & 0b0100000) $c_value.= $parts[6];
            if ($i & 0b1000000) $c_value.= $parts[7];
            $c_url = new Url($c_value);
            $c_expected = $expected[$c_value];
            $c_result = $c_url->has_error === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"', ['value' => $c_expected ? 'true' : 'false']);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => $c_url->has_error ? 'true' : 'false']);
                $c_results['return'] = 0;
                return;
            }
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $expected = [
            '://:80?key=value' => true,
            '://:80?key=value#anchor' => true,
            '://:80' => true,
            '://:80/?key=value' => true,
            '://:80/?key=value#anchor' => true,
            '://:80/' => true,
            '://:80/#anchor' => true,
            '://:80#anchor' => true,
            '://?key=value' => true,
            '://?key=value#anchor' => true,
            '://' => true,
            ':///?key=value' => true,
            ':///?key=value#anchor' => true,
            ':///' => true,
            ':///#anchor' => true,
            '://#anchor' => true,
            '://subdomain.domain:80?key=value' => true,
            '://subdomain.domain:80?key=value#anchor' => true,
            '://subdomain.domain:80' => true,
            '://subdomain.domain:80/?key=value' => true,
            '://subdomain.domain:80/?key=value#anchor' => true,
            '://subdomain.domain:80/' => true,
            '://subdomain.domain:80/#anchor' => true,
            '://subdomain.domain:80#anchor' => true,
            '://subdomain.domain?key=value' => true,
            '://subdomain.domain?key=value#anchor' => true,
            '://subdomain.domain' => true,
            '://subdomain.domain/?key=value' => true,
            '://subdomain.domain/?key=value#anchor' => true,
            '://subdomain.domain/' => true,
            '://subdomain.domain/#anchor' => true,
            '://subdomain.domain#anchor' => true,
            ':80?key=value' => true,
            ':80?key=value#anchor' => true,
            ':80' => true,
            ':80/?key=value' => true,
            ':80/?key=value#anchor' => true,
            ':80/' => true,
            ':80/#anchor' => true,
            ':80#anchor' => true,
            '?key=value' => true,
            '?key=value#anchor' => true,
            '' => true,
            '#anchor' => true,
            'http://:80?key=value' => true,
            'http://:80?key=value#anchor' => true,
            'http://:80' => true,
            'http://:80/?key=value' => true,
            'http://:80/?key=value#anchor' => true,
            'http://:80/' => true,
            'http://:80/#anchor' => true,
            'http://:80#anchor' => true,
            'http://?key=value' => true,
            'http://?key=value#anchor' => true,
            'http://' => true,
            'http:///?key=value' => true,
            'http:///?key=value#anchor' => true,
            'http:///' => true,
            'http:///#anchor' => true,
            'http://#anchor' => true,
            'http://subdomain.domain:80?key=value' => true,
            'http://subdomain.domain:80?key=value#anchor' => true,
            'http://subdomain.domain:80#anchor' => true,
            'http://subdomain.domain?key=value' => true,
            'http://subdomain.domain?key=value#anchor' => true,
            'http://subdomain.domain#anchor' => true,
            'http:80?key=value' => true,
            'http:80?key=value#anchor' => true,
            'http:80#anchor' => true,
            'http?key=value' => true,
            'http?key=value#anchor' => true,
            'http#anchor' => true,
            'httpsubdomain.domain:80?key=value' => true,
            'httpsubdomain.domain:80?key=value#anchor' => true,
            'httpsubdomain.domain:80#anchor' => true,
            'httpsubdomain.domain?key=value' => true,
            'httpsubdomain.domain?key=value#anchor' => true,
            'httpsubdomain.domain#anchor' => true,
            'subdomain.domain:80?key=value' => true,
            'subdomain.domain:80?key=value#anchor' => true,
            'subdomain.domain:80#anchor' => true,
            'subdomain.domain?key=value' => true,
            'subdomain.domain?key=value#anchor' => true,
            'subdomain.domain#anchor' => true,
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            '/?key=value' => false,
            '/?key=value#anchor' => false,
            '/' => false,
            '/#anchor' => false,
            'http://subdomain.domain:80' => false,
            'http://subdomain.domain:80/?key=value' => false,
            'http://subdomain.domain:80/?key=value#anchor' => false,
            'http://subdomain.domain:80/' => false,
            'http://subdomain.domain:80/#anchor' => false,
            'http://subdomain.domain' => false,
            'http://subdomain.domain/?key=value' => false,
            'http://subdomain.domain/?key=value#anchor' => false,
            'http://subdomain.domain/' => false,
            'http://subdomain.domain/#anchor' => false,
            'http:80' => false,
            'http:80/?key=value' => false,
            'http:80/?key=value#anchor' => false,
            'http:80/' => false,
            'http:80/#anchor' => false,
            'http' => false,
            'http/?key=value' => false,
            'http/?key=value#anchor' => false,
            'http/' => false,
            'http/#anchor' => false,
            'httpsubdomain.domain:80' => false,
            'httpsubdomain.domain:80/?key=value' => false,
            'httpsubdomain.domain:80/?key=value#anchor' => false,
            'httpsubdomain.domain:80/' => false,
            'httpsubdomain.domain:80/#anchor' => false,
            'httpsubdomain.domain' => false,
            'httpsubdomain.domain/?key=value' => false,
            'httpsubdomain.domain/?key=value#anchor' => false,
            'httpsubdomain.domain/' => false,
            'httpsubdomain.domain/#anchor' => false,
            'subdomain.domain:80' => false,
            'subdomain.domain:80/?key=value' => false,
            'subdomain.domain:80/?key=value#anchor' => false,
            'subdomain.domain:80/' => false,
            'subdomain.domain:80/#anchor' => false,
            'subdomain.domain' => false,
            'subdomain.domain/?key=value' => false,
            'subdomain.domain/?key=value#anchor' => false,
            'subdomain.domain/' => false,
            'subdomain.domain/#anchor' => false
        ];

        $parts = [
            1 => 'http',
            2 => '://',
            3 => 'subdomain.domain',
            4 => ':80',
            5 => '/',
            6 => '?key=value',
            7 => '#anchor'
        ];

        for ($i = 0b0000000; $i <= 0b1111111; $i++) {
            $c_value = '';
            if ($i & 0b0000001) $c_value.= $parts[1];
            if ($i & 0b0000010) $c_value.= $parts[2];
            if ($i & 0b0000100) $c_value.= $parts[3];
            if ($i & 0b0001000) $c_value.= $parts[4];
            if ($i & 0b0010000) $c_value.= $parts[5];
            if ($i & 0b0100000) $c_value.= $parts[6];
            if ($i & 0b1000000) $c_value.= $parts[7];
            $c_url = new Url($c_value);
            $c_expected = $expected[$c_value];
            $c_result = $c_url->has_error === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"', ['value' => $c_expected ? 'true' : 'false']);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => $c_url->has_error ? 'true' : 'false']);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__full_get(&$test, $dpath, &$c_results) {
        $data = [
                                                            '/'                                 => Request::scheme_get().'://'.Request::host_get(),
                                                            '/?key=value'                       => Request::scheme_get().'://'.Request::host_get().'/?key=value',
                                                            '/#anchor'                          => Request::scheme_get().'://'.Request::host_get().'/#anchor',
                                                            '/?key=value#anchor'                => Request::scheme_get().'://'.Request::host_get().'/?key=value#anchor',
                                                            '/dir/subdir/page'                  => Request::scheme_get().'://'.Request::host_get().'/dir/subdir/page',
                                                            '/dir/subdir/page?key=value'        => Request::scheme_get().'://'.Request::host_get().'/dir/subdir/page?key=value',
                                                            '/dir/subdir/page#anchor'           => Request::scheme_get().'://'.Request::host_get().'/dir/subdir/page#anchor',
                                                            '/dir/subdir/page?key=value#anchor' => Request::scheme_get().'://'.Request::host_get().'/dir/subdir/page?key=value#anchor',
                                        Request::host_get()                                     => Request::scheme_get().'://'.Request::host_get(),
                                        Request::host_get().'/?key=value'                       => Request::scheme_get().'://'.Request::host_get().'/?key=value',
                                        Request::host_get().'/#anchor'                          => Request::scheme_get().'://'.Request::host_get().'/#anchor',
                                        Request::host_get().'/?key=value#anchor'                => Request::scheme_get().'://'.Request::host_get().'/?key=value#anchor',
                                        Request::host_get().'/dir/subdir/page'                  => Request::scheme_get().'://'.Request::host_get().'/dir/subdir/page',
                                        Request::host_get().'/dir/subdir/page?key=value'        => Request::scheme_get().'://'.Request::host_get().'/dir/subdir/page?key=value',
                                        Request::host_get().'/dir/subdir/page#anchor'           => Request::scheme_get().'://'.Request::host_get().'/dir/subdir/page#anchor',
                                        Request::host_get().'/dir/subdir/page?key=value#anchor' => Request::scheme_get().'://'.Request::host_get().'/dir/subdir/page?key=value#anchor',
            Request::scheme_get().'://'.Request::host_get()                                     => Request::scheme_get().'://'.Request::host_get(),
            Request::scheme_get().'://'.Request::host_get().'/?key=value'                       => Request::scheme_get().'://'.Request::host_get().'/?key=value',
            Request::scheme_get().'://'.Request::host_get().'/#anchor'                          => Request::scheme_get().'://'.Request::host_get().'/#anchor',
            Request::scheme_get().'://'.Request::host_get().'/?key=value#anchor'                => Request::scheme_get().'://'.Request::host_get().'/?key=value#anchor',
            Request::scheme_get().'://'.Request::host_get().'/dir/subdir/page'                  => Request::scheme_get().'://'.Request::host_get().'/dir/subdir/page',
            Request::scheme_get().'://'.Request::host_get().'/dir/subdir/page?key=value'        => Request::scheme_get().'://'.Request::host_get().'/dir/subdir/page?key=value',
            Request::scheme_get().'://'.Request::host_get().'/dir/subdir/page#anchor'           => Request::scheme_get().'://'.Request::host_get().'/dir/subdir/page#anchor',
            Request::scheme_get().'://'.Request::host_get().'/dir/subdir/page?key=value#anchor' => Request::scheme_get().'://'.Request::host_get().'/dir/subdir/page?key=value#anchor'
        ];

        foreach ($data as $c_value => $c_expected) {
            $c_url = new Url($c_value);
            $c_gotten = $c_url->full_get();
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"', ['value' => $c_expected]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => $c_gotten]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__tiny_get(&$test, $dpath, &$c_results) {
        $data = [
                                                            '/'                                 => '/',
                                                            '/?key=value'                       => '/?key=value',
                                                            '/#anchor'                          => '/#anchor',
                                                            '/?key=value#anchor'                => '/?key=value#anchor',
                                                            '/dir/subdir/page'                  => '/dir/subdir/page',
                                                            '/dir/subdir/page?key=value'        => '/dir/subdir/page?key=value',
                                                            '/dir/subdir/page#anchor'           => '/dir/subdir/page#anchor',
                                                            '/dir/subdir/page?key=value#anchor' => '/dir/subdir/page?key=value#anchor',
                                        Request::host_get()                                     => '/',
                                        Request::host_get().'/?key=value'                       => '/?key=value',
                                        Request::host_get().'/#anchor'                          => '/#anchor',
                                        Request::host_get().'/?key=value#anchor'                => '/?key=value#anchor',
                                        Request::host_get().'/dir/subdir/page'                  => '/dir/subdir/page',
                                        Request::host_get().'/dir/subdir/page?key=value'        => '/dir/subdir/page?key=value',
                                        Request::host_get().'/dir/subdir/page#anchor'           => '/dir/subdir/page#anchor',
                                        Request::host_get().'/dir/subdir/page?key=value#anchor' => '/dir/subdir/page?key=value#anchor',
            Request::scheme_get().'://'.Request::host_get()                                     => '/',
            Request::scheme_get().'://'.Request::host_get().'/?key=value'                       => '/?key=value',
            Request::scheme_get().'://'.Request::host_get().'/#anchor'                          => '/#anchor',
            Request::scheme_get().'://'.Request::host_get().'/?key=value#anchor'                => '/?key=value#anchor',
            Request::scheme_get().'://'.Request::host_get().'/dir/subdir/page'                  => '/dir/subdir/page',
            Request::scheme_get().'://'.Request::host_get().'/dir/subdir/page?key=value'        => '/dir/subdir/page?key=value',
            Request::scheme_get().'://'.Request::host_get().'/dir/subdir/page#anchor'           => '/dir/subdir/page#anchor',
            Request::scheme_get().'://'.Request::host_get().'/dir/subdir/page?key=value#anchor' => '/dir/subdir/page?key=value#anchor',
        ];

        foreach ($data as $c_value => $c_expected) {
            $c_url = new Url($c_value);
            $c_gotten = $c_url->tiny_get();
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"', ['value' => $c_expected]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => $c_gotten]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__query_arg_(&$test, $dpath, &$c_results) {
        $url = new Url('http://example.com/test_utf?p1='.urlencode('знач.1'));

        $gotten = $url->query_arg_select('p1');
        $expected = 'знач.1';
        $result = $gotten === $expected;
        if ($result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $expected, 'result' => (new Text('success'))->render()]);
        if ($result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $expected, 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"', ['value' => $expected]);
            $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => $gotten]);
            $c_results['return'] = 0;
            return;
        }

        $gotten = $url->full_get();
        $expected = 'http://example.com/test_utf?p1=%D0%B7%D0%BD%D0%B0%D1%87.1';
        $result = $gotten === $expected;
        if ($result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $expected, 'result' => (new Text('success'))->render()]);
        if ($result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $expected, 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"', ['value' => $expected]);
            $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => $gotten]);
            $c_results['return'] = 0;
            return;
        }

        $url->query_arg_insert('p2', 'знач.2');
        $gotten = $url->query_arg_select('p2');
        $expected = 'знач.2';
        $result = $gotten === $expected;
        if ($result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $expected, 'result' => (new Text('success'))->render()]);
        if ($result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $expected, 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"', ['value' => $expected]);
            $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => $gotten]);
            $c_results['return'] = 0;
            return;
        }

        $gotten = $url->full_get();
        $expected = 'http://example.com/test_utf?p1=%D0%B7%D0%BD%D0%B0%D1%87.1&p2=%D0%B7%D0%BD%D0%B0%D1%87.2';
        $result = $gotten === $expected;
        if ($result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $expected, 'result' => (new Text('success'))->render()]);
        if ($result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $expected, 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"', ['value' => $expected]);
            $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => $gotten]);
            $c_results['return'] = 0;
            return;
        }
    }

    static function test_step_code__http_build_query(&$test, $dpath, &$c_results) {
        $expected = '%D0%BA%D0%BB%D1%8E%D1%87=%D0%BC%D0%BE%D1%91%20%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D0%B5';
        $gotten = http_build_query(['ключ' => 'моё значение'], '', '&', PHP_QUERY_RFC3986);
        $result = $gotten === $expected;
        if ($result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'ключ=моё значение', 'result' => (new Text('success'))->render()]);
        if ($result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'ключ=моё значение', 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"', ['value' => $expected]);
            $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => $gotten]);
            $c_results['return'] = 0;
            return;
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $expected = '%D0%BA%D0%BB%D1%8E%D1%87=%D0%BC%D0%BE%D1%91%2B%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D0%B5';
        $gotten = http_build_query(['ключ' => 'моё+значение'], '', '&', PHP_QUERY_RFC3986);
        $result = $gotten === $expected;
        if ($result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'ключ=моё+значение', 'result' => (new Text('success'))->render()]);
        if ($result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'ключ=моё+значение', 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"', ['value' => $expected]);
            $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => $gotten]);
            $c_results['return'] = 0;
            return;
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $data = [
            ''                                                                                               => [],
            'key_1='                                                                                         => ['key_1' => ''],
            'key_1=value_1'                                                                                  => ['key_1' => 'value_1'],
            'key_1=value_1%3Dstill_value_1'                                                                  => ['key_1' => 'value_1=still_value_1'],
            'key_1=&key_2='                                                                                  => ['key_1' => '', 'key_2' => ''],
            'key_1=value_1&key_arr%5B0%5D=value_2&key_arr%5B1%5D=value_3'                                    => ['key_1' => 'value_1', 'key_arr' => [0 => 'value_2', 1 => 'value_3']],
            'key_1=value_1&key_arr%5B2%5D=value_2&key_arr%5B3%5D=value_3'                                    => ['key_1' => 'value_1', 'key_arr' => [2 => 'value_2', 3 => 'value_3']],
            'key_1=value_1&key_arr%5Bk-1%5D=value_2&key_arr%5Bk-2%5D=value_3'                                => ['key_1' => 'value_1', 'key_arr' => ['k-1' => 'value_2', 'k-2' => 'value_3']],
            '%D0%BA%D0%BB%D1%8E%D1%87=%D0%BC%D0%BE%D1%91%20%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D0%B5' => ['ключ' => 'моё значение'],
            '%D0%BA%D0%BB%D1%8E%D1%87=%D0%BC%D0%BE%D1%91%2B%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D0%B5' => ['ключ' => 'моё+значение']
        ];

        foreach ($data as $c_expected => $c_value) {
            $c_gotten = http_build_query($c_value, '', '&', PHP_QUERY_RFC3986);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_expected, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_expected, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"', ['value' => $c_expected]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => $c_gotten]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__parse_str(&$test, $dpath, &$c_results) {
        $data = [
            ''                                                        => [],
            '&'                                                       => [],
            '='                                                       => [],
            '=value_1'                                                => [],
            'key_1'                                                   => ['key_1' => ''],
            'key_1='                                                  => ['key_1' => ''],
            'key_1=value_1'                                           => ['key_1' => 'value_1'],
            'key_1=value_1=still_value_1'                             => ['key_1' => 'value_1=still_value_1'],
            'key_1=value_1%3Dstill_value_1'                           => ['key_1' => 'value_1=still_value_1'],
            '&&&key_2'                                                => ['key_2' => ''],
            '&key_2'                                                  => ['key_2' => ''],
            'key_1&'                                                  => ['key_1' => ''],
            'key_1&key_2'                                             => ['key_1' => '', 'key_2' => ''],
            'key_1=value_1&key_arr[]=value_2&key_arr[]=value_3'       => ['key_1' => 'value_1', 'key_arr' => [   0  => 'value_2',    1  => 'value_3']],
            'key_1=value_1&key_arr[0]=value_2&key_arr[1]=value_3'     => ['key_1' => 'value_1', 'key_arr' => [   0  => 'value_2',    1  => 'value_3']],
            'key_1=value_1&key_arr[2]=value_2&key_arr[3]=value_3'     => ['key_1' => 'value_1', 'key_arr' => [   2  => 'value_2',    3  => 'value_3']],
            'key_1=value_1&key_arr[k-1]=value_2&key_arr[k-2]=value_3' => ['key_1' => 'value_1', 'key_arr' => ['k-1' => 'value_2', 'k-2' => 'value_3']],
            'key_1=value_1&key_arr[]=value_2&key_arr[k-2]=value_3'    => ['key_1' => 'value_1', 'key_arr' => [   0  => 'value_2', 'k-2' => 'value_3']],
            'key_1=value_1&key_arr[k-1]=value_2&key_arr[]=value_3'    => ['key_1' => 'value_1', 'key_arr' => ['k-1' => 'value_2',    0  => 'value_3']],
            'key_arr[][]=value_1'                                     => ['key_arr'  => [0 =>             [0 => 'value_1']]],
            'key_arr[][][]=value_1'                                   => ['key_arr'  => [0 => [0 =>       [0 => 'value_1']]]],
            'key_arr[][][][]=value_1'                                 => ['key_arr'  => [0 => [0 => [0 => [0 => 'value_1']]]]],
            'key_arr=value_1&key_arr[]=value_2'                       => ['key_arr'  => [0 =>                   'value_2']],
            'key_arr[]=value_1&key_arr=value_2'                       => ['key_arr'  =>                         'value_2'],
            'key_arr[=value_1'                                        => ['key_arr_' =>                         'value_1'],
            'key_arr]=value_1'                                        => ['key_arr]' =>                         'value_1'],
            '%D0%BA%D0%BB%D1%8E%D1%87=%D0%BC%D0%BE%D1%91%20%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D0%B5' => ['ключ' => 'моё значение'],
            '%D0%BA%D0%BB%D1%8E%D1%87=%D0%BC%D0%BE%D1%91%2B%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D0%B5' => ['ключ' => 'моё+значение']
        ];

        foreach ($data as $c_value => $c_expected) {
            $c_parse_result = null;
            $c_gotten = parse_str($c_value, $c_parse_result);
            $c_result = serialize($c_parse_result) === serialize($c_expected);
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"', ['value' => Core::return_encoded(serialize($c_expected))]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded(serialize($c_gotten))]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

}
