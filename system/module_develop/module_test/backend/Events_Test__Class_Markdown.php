<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use const effcore\NL;
use effcore\Markdown;
use effcore\Test;
use effcore\Text;

abstract class Events_Test__Class_Markdown {

    static function test_step_code__markdown_to_markup__simple(&$test, $dpath, &$c_results) {

        ###############
        ### headers ###
        ###############

        $data['simple']['header_setext_1']['data'] = 'Title H1 (Setext-style)'.NL;
        $data['simple']['header_setext_1']['data'].= '=';
        $data['simple']['header_setext_1']['expected'] = '<h1>Title H1 (Setext-style)</h1>';

        $data['simple']['header_setext_2']['data'] = '   Title H1 (Setext-style)   '.NL;
        $data['simple']['header_setext_2']['data'].= '=   ';
        $data['simple']['header_setext_2']['expected'] = '<h1>Title H1 (Setext-style)</h1>';

        $data['simple']['header_setext_3']['data'] = 'Title H1 (Setext-style)'.NL;
        $data['simple']['header_setext_3']['data'].= '=x';
        $data['simple']['header_setext_3']['expected'] = '<p>';
        $data['simple']['header_setext_3']['expected'].=   'Title H1 (Setext-style)'.NL;
        $data['simple']['header_setext_3']['expected'].=   '=x';
        $data['simple']['header_setext_3']['expected'].= '</p>';

        $data['simple']['header_setext_4']['data'] = 'Title H2 (Setext-style)'.NL;
        $data['simple']['header_setext_4']['data'].= '-';
        $data['simple']['header_setext_4']['expected'] = '<h2>Title H2 (Setext-style)</h2>';

        $data['simple']['header_setext_5']['data'] = '   Title H2 (Setext-style)   '.NL;
        $data['simple']['header_setext_5']['data'].= '-   ';
        $data['simple']['header_setext_5']['expected'] = '<h2>Title H2 (Setext-style)</h2>';

        $data['simple']['header_setext_6']['data'] = 'Title H2 (Setext-style)'.NL;
        $data['simple']['header_setext_6']['data'].= '-x';
        $data['simple']['header_setext_6']['expected'] = '<p>';
        $data['simple']['header_setext_6']['expected'].=   'Title H2 (Setext-style)'.NL;
        $data['simple']['header_setext_6']['expected'].=   '-x';
        $data['simple']['header_setext_6']['expected'].= '</p>';

        $data['simple']['header_atx_1']['data'] =       '# Title H1 (atx-style)';
        $data['simple']['header_atx_2']['data'] =      '## Title H2 (atx-style)';
        $data['simple']['header_atx_3']['data'] =     '### Title H3 (atx-style)';
        $data['simple']['header_atx_4']['data'] =    '#### Title H4 (atx-style)';
        $data['simple']['header_atx_5']['data'] =   '##### Title H5 (atx-style)';
        $data['simple']['header_atx_6']['data'] =  '###### Title H6 (atx-style)';
        $data['simple']['header_atx_1']['expected'] = '<h1>Title H1 (atx-style)</h1>';
        $data['simple']['header_atx_2']['expected'] = '<h2>Title H2 (atx-style)</h2>';
        $data['simple']['header_atx_3']['expected'] = '<h3>Title H3 (atx-style)</h3>';
        $data['simple']['header_atx_4']['expected'] = '<h4>Title H4 (atx-style)</h4>';
        $data['simple']['header_atx_5']['expected'] = '<h5>Title H5 (atx-style)</h5>';
        $data['simple']['header_atx_6']['expected'] = '<h6>Title H6 (atx-style)</h6>';

        $data['simple']['header_atx_7']['data'] = '# Title H1 (atx-style)';
        $data['simple']['header_atx_7']['expected'] = '<h1>Title H1 (atx-style)</h1>';

        $data['simple']['header_atx_8']['data'] = '   #      Title H1 (atx-style)      ######';
        $data['simple']['header_atx_8']['expected'] = '<h1>Title H1 (atx-style)</h1>';

        ###############################
        ### list: bulleted and flat ###
        ###############################

        $data['simple']['list_10']['data'] = '- flat list item 1'.NL;
        $data['simple']['list_10']['data'].= '- flat list item 2'.NL;
        $data['simple']['list_10']['data'].= '- flat list item 3'.NL;
        $data['simple']['list_10']['data'].= '+ flat list item 4'.NL;
        $data['simple']['list_10']['data'].= '+ flat list item 5'.NL;
        $data['simple']['list_10']['data'].= '+ flat list item 6'.NL;
        $data['simple']['list_10']['data'].= '* flat list item 7'.NL;
        $data['simple']['list_10']['data'].= '* flat list item 8'.NL;
        $data['simple']['list_10']['data'].= '* flat list item 9';
        $data['simple']['list_10']['expected'] = '<ul>';
        $data['simple']['list_10']['expected'].=   '<li>flat list item 1</li>';
        $data['simple']['list_10']['expected'].=   '<li>flat list item 2</li>';
        $data['simple']['list_10']['expected'].=   '<li>flat list item 3</li>';
        $data['simple']['list_10']['expected'].=   '<li>flat list item 4</li>';
        $data['simple']['list_10']['expected'].=   '<li>flat list item 5</li>';
        $data['simple']['list_10']['expected'].=   '<li>flat list item 6</li>';
        $data['simple']['list_10']['expected'].=   '<li>flat list item 7</li>';
        $data['simple']['list_10']['expected'].=   '<li>flat list item 8</li>';
        $data['simple']['list_10']['expected'].=   '<li>flat list item 9</li>';
        $data['simple']['list_10']['expected'].= '</ul>';

        $data['simple']['list_11']['data'] = '   -   flat list item 1'.NL;
        $data['simple']['list_11']['data'].= '   -   flat list item 2'.NL;
        $data['simple']['list_11']['data'].= '   -   flat list item 3'.NL;
        $data['simple']['list_11']['data'].= '   +   flat list item 4'.NL;
        $data['simple']['list_11']['data'].= '   +   flat list item 5'.NL;
        $data['simple']['list_11']['data'].= '   +   flat list item 6'.NL;
        $data['simple']['list_11']['data'].= '   *   flat list item 7'.NL;
        $data['simple']['list_11']['data'].= '   *   flat list item 8'.NL;
        $data['simple']['list_11']['data'].= '   *   flat list item 9';
        $data['simple']['list_11']['expected'] = '<ul>';
        $data['simple']['list_11']['expected'].=   '<li>flat list item 1</li>';
        $data['simple']['list_11']['expected'].=   '<li>flat list item 2</li>';
        $data['simple']['list_11']['expected'].=   '<li>flat list item 3</li>';
        $data['simple']['list_11']['expected'].=   '<li>flat list item 4</li>';
        $data['simple']['list_11']['expected'].=   '<li>flat list item 5</li>';
        $data['simple']['list_11']['expected'].=   '<li>flat list item 6</li>';
        $data['simple']['list_11']['expected'].=   '<li>flat list item 7</li>';
        $data['simple']['list_11']['expected'].=   '<li>flat list item 8</li>';
        $data['simple']['list_11']['expected'].=   '<li>flat list item 9</li>';
        $data['simple']['list_11']['expected'].= '</ul>';

        ######################################################################
        ### list: bulleted and hierarchical (ascent and descent algorithm) ###
        ######################################################################

        $data['simple']['list_20']['data'] = '- list item 1'.NL;
        $data['simple']['list_20']['data'].= '- list item 2'.NL;
        $data['simple']['list_20']['data'].= '  - list item 2.1'.NL;
        $data['simple']['list_20']['data'].= '  - list item 2.2'.NL;
        $data['simple']['list_20']['data'].= '    - list item 2.2.1'.NL;
        $data['simple']['list_20']['data'].= '    - list item 2.2.2'.NL;
        $data['simple']['list_20']['data'].= '  - list item 2.3'.NL;
        $data['simple']['list_20']['data'].= '  - list item 2.4'.NL;
        $data['simple']['list_20']['data'].= '- list item 3'.NL;
        $data['simple']['list_20']['data'].= '- list item 4'.NL;
        $data['simple']['list_20']['data'].= '  - list item 4.1'.NL;
        $data['simple']['list_20']['data'].= '  - list item 4.2'.NL;
        $data['simple']['list_20']['data'].= '    - list item 4.2.1'.NL;
        $data['simple']['list_20']['data'].= '    - list item 4.2.2'.NL;
        $data['simple']['list_20']['data'].= '  - list item 4.3'.NL;
        $data['simple']['list_20']['data'].= '  - list item 4.4'.NL;
        $data['simple']['list_20']['data'].= '- list item 5'.NL;
        $data['simple']['list_20']['data'].= '- list item 6';
        $data['simple']['list_20']['expected'] = '<ul>';
        $data['simple']['list_20']['expected'].=   '<li>list item 1</li>';
        $data['simple']['list_20']['expected'].=   '<li>list item 2';
        $data['simple']['list_20']['expected'].=     '<ul>';
        $data['simple']['list_20']['expected'].=       '<li>list item 2.1</li>';
        $data['simple']['list_20']['expected'].=       '<li>list item 2.2';
        $data['simple']['list_20']['expected'].=         '<ul>';
        $data['simple']['list_20']['expected'].=           '<li>list item 2.2.1</li>';
        $data['simple']['list_20']['expected'].=           '<li>list item 2.2.2</li>';
        $data['simple']['list_20']['expected'].=         '</ul>';
        $data['simple']['list_20']['expected'].=       '</li>';
        $data['simple']['list_20']['expected'].=       '<li>list item 2.3</li>';
        $data['simple']['list_20']['expected'].=       '<li>list item 2.4</li>';
        $data['simple']['list_20']['expected'].=     '</ul>';
        $data['simple']['list_20']['expected'].=   '</li>';
        $data['simple']['list_20']['expected'].=   '<li>list item 3</li>';
        $data['simple']['list_20']['expected'].=   '<li>list item 4';
        $data['simple']['list_20']['expected'].=     '<ul>';
        $data['simple']['list_20']['expected'].=       '<li>list item 4.1</li>';
        $data['simple']['list_20']['expected'].=       '<li>list item 4.2';
        $data['simple']['list_20']['expected'].=         '<ul>';
        $data['simple']['list_20']['expected'].=           '<li>list item 4.2.1</li>';
        $data['simple']['list_20']['expected'].=           '<li>list item 4.2.2</li>';
        $data['simple']['list_20']['expected'].=         '</ul>';
        $data['simple']['list_20']['expected'].=       '</li>';
        $data['simple']['list_20']['expected'].=       '<li>list item 4.3</li>';
        $data['simple']['list_20']['expected'].=       '<li>list item 4.4</li>';
        $data['simple']['list_20']['expected'].=     '</ul>';
        $data['simple']['list_20']['expected'].=   '</li>';
        $data['simple']['list_20']['expected'].=   '<li>list item 5</li>';
        $data['simple']['list_20']['expected'].=   '<li>list item 6</li>';
        $data['simple']['list_20']['expected'].= '</ul>';

        $data['simple']['list_21']['data'] = '   -   list item 1'.NL;
        $data['simple']['list_21']['data'].= '   -   list item 2'.NL;
        $data['simple']['list_21']['data'].= '     -   list item 2.1'.NL;
        $data['simple']['list_21']['data'].= '     -   list item 2.2'.NL;
        $data['simple']['list_21']['data'].= '       -   list item 2.2.1'.NL;
        $data['simple']['list_21']['data'].= '       -   list item 2.2.2'.NL;
        $data['simple']['list_21']['data'].= '     -   list item 2.3'.NL;
        $data['simple']['list_21']['data'].= '     -   list item 2.4'.NL;
        $data['simple']['list_21']['data'].= '   -   list item 3'.NL;
        $data['simple']['list_21']['data'].= '   -   list item 4'.NL;
        $data['simple']['list_21']['data'].= '     -   list item 4.1'.NL;
        $data['simple']['list_21']['data'].= '     -   list item 4.2'.NL;
        $data['simple']['list_21']['data'].= '       -   list item 4.2.1'.NL;
        $data['simple']['list_21']['data'].= '       -   list item 4.2.2'.NL;
        $data['simple']['list_21']['data'].= '     -   list item 4.3'.NL;
        $data['simple']['list_21']['data'].= '     -   list item 4.4'.NL;
        $data['simple']['list_21']['data'].= '   -   list item 5'.NL;
        $data['simple']['list_21']['data'].= '   -   list item 6';
        $data['simple']['list_21']['expected'] = '<ul>';
        $data['simple']['list_21']['expected'].=   '<li>list item 1</li>';
        $data['simple']['list_21']['expected'].=   '<li>list item 2';
        $data['simple']['list_21']['expected'].=     '<ul>';
        $data['simple']['list_21']['expected'].=       '<li>list item 2.1</li>';
        $data['simple']['list_21']['expected'].=       '<li>list item 2.2';
        $data['simple']['list_21']['expected'].=         '<ul>';
        $data['simple']['list_21']['expected'].=           '<li>list item 2.2.1</li>';
        $data['simple']['list_21']['expected'].=           '<li>list item 2.2.2</li>';
        $data['simple']['list_21']['expected'].=         '</ul>';
        $data['simple']['list_21']['expected'].=       '</li>';
        $data['simple']['list_21']['expected'].=       '<li>list item 2.3</li>';
        $data['simple']['list_21']['expected'].=       '<li>list item 2.4</li>';
        $data['simple']['list_21']['expected'].=     '</ul>';
        $data['simple']['list_21']['expected'].=   '</li>';
        $data['simple']['list_21']['expected'].=   '<li>list item 3</li>';
        $data['simple']['list_21']['expected'].=   '<li>list item 4';
        $data['simple']['list_21']['expected'].=     '<ul>';
        $data['simple']['list_21']['expected'].=       '<li>list item 4.1</li>';
        $data['simple']['list_21']['expected'].=       '<li>list item 4.2';
        $data['simple']['list_21']['expected'].=         '<ul>';
        $data['simple']['list_21']['expected'].=           '<li>list item 4.2.1</li>';
        $data['simple']['list_21']['expected'].=           '<li>list item 4.2.2</li>';
        $data['simple']['list_21']['expected'].=         '</ul>';
        $data['simple']['list_21']['expected'].=       '</li>';
        $data['simple']['list_21']['expected'].=       '<li>list item 4.3</li>';
        $data['simple']['list_21']['expected'].=       '<li>list item 4.4</li>';
        $data['simple']['list_21']['expected'].=     '</ul>';
        $data['simple']['list_21']['expected'].=   '</li>';
        $data['simple']['list_21']['expected'].=   '<li>list item 5</li>';
        $data['simple']['list_21']['expected'].=   '<li>list item 6</li>';
        $data['simple']['list_21']['expected'].= '</ul>';

        ######################################################################
        ### list: numbered and hierarchical (ascent and descent algorithm) ###
        ######################################################################

        $data['simple']['list_30']['data'] = '1. numbered list item 1'.NL;
        $data['simple']['list_30']['data'].= '2. numbered list item 2'.NL;
        $data['simple']['list_30']['data'].= '  21. numbered list item 2.1'.NL;
        $data['simple']['list_30']['data'].= '  22. numbered list item 2.2'.NL;
        $data['simple']['list_30']['data'].= '    221. numbered list item 2.2.1'.NL;
        $data['simple']['list_30']['data'].= '    222. numbered list item 2.2.2'.NL;
        $data['simple']['list_30']['data'].= '  23. numbered list item 2.3'.NL;
        $data['simple']['list_30']['data'].= '  24. numbered list item 2.4'.NL;
        $data['simple']['list_30']['data'].= '3. numbered list item 3'.NL;
        $data['simple']['list_30']['data'].= '4. numbered list item 4'.NL;
        $data['simple']['list_30']['data'].= '  41. numbered list item 4.1'.NL;
        $data['simple']['list_30']['data'].= '  42. numbered list item 4.2'.NL;
        $data['simple']['list_30']['data'].= '    421. numbered list item 4.2.1'.NL;
        $data['simple']['list_30']['data'].= '    422. numbered list item 4.2.2'.NL;
        $data['simple']['list_30']['data'].= '  43. numbered list item 4.3'.NL;
        $data['simple']['list_30']['data'].= '  44. numbered list item 4.4'.NL;
        $data['simple']['list_30']['data'].= '5. numbered list item 5'.NL;
        $data['simple']['list_30']['data'].= '6. numbered list item 6';
        $data['simple']['list_30']['expected'] = '<ol>';
        $data['simple']['list_30']['expected'].= '<li>numbered list item 1</li>';
        $data['simple']['list_30']['expected'].=   '<li>numbered list item 2';
        $data['simple']['list_30']['expected'].=     '<ol>';
        $data['simple']['list_30']['expected'].=       '<li>numbered list item 2.1</li>';
        $data['simple']['list_30']['expected'].=       '<li>numbered list item 2.2';
        $data['simple']['list_30']['expected'].=         '<ol>';
        $data['simple']['list_30']['expected'].=           '<li>numbered list item 2.2.1</li>';
        $data['simple']['list_30']['expected'].=           '<li>numbered list item 2.2.2</li>';
        $data['simple']['list_30']['expected'].=         '</ol>';
        $data['simple']['list_30']['expected'].=       '</li>';
        $data['simple']['list_30']['expected'].=       '<li>numbered list item 2.3</li>';
        $data['simple']['list_30']['expected'].=       '<li>numbered list item 2.4</li>';
        $data['simple']['list_30']['expected'].=     '</ol>';
        $data['simple']['list_30']['expected'].=   '</li>';
        $data['simple']['list_30']['expected'].=   '<li>numbered list item 3</li>';
        $data['simple']['list_30']['expected'].=   '<li>numbered list item 4';
        $data['simple']['list_30']['expected'].=     '<ol>';
        $data['simple']['list_30']['expected'].=       '<li>numbered list item 4.1</li>';
        $data['simple']['list_30']['expected'].=       '<li>numbered list item 4.2';
        $data['simple']['list_30']['expected'].=         '<ol>';
        $data['simple']['list_30']['expected'].=           '<li>numbered list item 4.2.1</li>';
        $data['simple']['list_30']['expected'].=           '<li>numbered list item 4.2.2</li>';
        $data['simple']['list_30']['expected'].=         '</ol>';
        $data['simple']['list_30']['expected'].=       '</li>';
        $data['simple']['list_30']['expected'].=       '<li>numbered list item 4.3</li>';
        $data['simple']['list_30']['expected'].=       '<li>numbered list item 4.4</li>';
        $data['simple']['list_30']['expected'].=     '</ol>';
        $data['simple']['list_30']['expected'].=   '</li>';
        $data['simple']['list_30']['expected'].=   '<li>numbered list item 5</li>';
        $data['simple']['list_30']['expected'].=   '<li>numbered list item 6</li>';
        $data['simple']['list_30']['expected'].= '</ol>';

        $data['simple']['list_31']['data'] = '   1.   numbered list item 1'.NL;
        $data['simple']['list_31']['data'].= '   2.   numbered list item 2'.NL;
        $data['simple']['list_31']['data'].= '     21.   numbered list item 2.1'.NL;
        $data['simple']['list_31']['data'].= '     22.   numbered list item 2.2'.NL;
        $data['simple']['list_31']['data'].= '       221.   numbered list item 2.2.1'.NL;
        $data['simple']['list_31']['data'].= '       222.   numbered list item 2.2.2'.NL;
        $data['simple']['list_31']['data'].= '     23.   numbered list item 2.3'.NL;
        $data['simple']['list_31']['data'].= '     24.   numbered list item 2.4'.NL;
        $data['simple']['list_31']['data'].= '   3.   numbered list item 3'.NL;
        $data['simple']['list_31']['data'].= '   4.   numbered list item 4'.NL;
        $data['simple']['list_31']['data'].= '     41.   numbered list item 4.1'.NL;
        $data['simple']['list_31']['data'].= '     42.   numbered list item 4.2'.NL;
        $data['simple']['list_31']['data'].= '       421.   numbered list item 4.2.1'.NL;
        $data['simple']['list_31']['data'].= '       422.   numbered list item 4.2.2'.NL;
        $data['simple']['list_31']['data'].= '     43.   numbered list item 4.3'.NL;
        $data['simple']['list_31']['data'].= '     44.   numbered list item 4.4'.NL;
        $data['simple']['list_31']['data'].= '   5.   numbered list item 5'.NL;
        $data['simple']['list_31']['data'].= '   6.   numbered list item 6';
        $data['simple']['list_31']['expected'] = '<ol>';
        $data['simple']['list_31']['expected'].=   '<li>numbered list item 1</li>';
        $data['simple']['list_31']['expected'].=   '<li>numbered list item 2';
        $data['simple']['list_31']['expected'].=     '<ol>';
        $data['simple']['list_31']['expected'].=       '<li>numbered list item 2.1</li>';
        $data['simple']['list_31']['expected'].=       '<li>numbered list item 2.2';
        $data['simple']['list_31']['expected'].=         '<ol>';
        $data['simple']['list_31']['expected'].=           '<li>numbered list item 2.2.1</li>';
        $data['simple']['list_31']['expected'].=           '<li>numbered list item 2.2.2</li>';
        $data['simple']['list_31']['expected'].=         '</ol>';
        $data['simple']['list_31']['expected'].=       '</li>';
        $data['simple']['list_31']['expected'].=       '<li>numbered list item 2.3</li>';
        $data['simple']['list_31']['expected'].=       '<li>numbered list item 2.4</li>';
        $data['simple']['list_31']['expected'].=     '</ol>';
        $data['simple']['list_31']['expected'].=   '</li>';
        $data['simple']['list_31']['expected'].=   '<li>numbered list item 3</li>';
        $data['simple']['list_31']['expected'].=   '<li>numbered list item 4';
        $data['simple']['list_31']['expected'].=     '<ol>';
        $data['simple']['list_31']['expected'].=       '<li>numbered list item 4.1</li>';
        $data['simple']['list_31']['expected'].=       '<li>numbered list item 4.2';
        $data['simple']['list_31']['expected'].=         '<ol>';
        $data['simple']['list_31']['expected'].=           '<li>numbered list item 4.2.1</li>';
        $data['simple']['list_31']['expected'].=           '<li>numbered list item 4.2.2</li>';
        $data['simple']['list_31']['expected'].=         '</ol>';
        $data['simple']['list_31']['expected'].=       '</li>';
        $data['simple']['list_31']['expected'].=       '<li>numbered list item 4.3</li>';
        $data['simple']['list_31']['expected'].=       '<li>numbered list item 4.4</li>';
        $data['simple']['list_31']['expected'].=     '</ol>';
        $data['simple']['list_31']['expected'].=   '</li>';
        $data['simple']['list_31']['expected'].=   '<li>numbered list item 5</li>';
        $data['simple']['list_31']['expected'].=   '<li>numbered list item 6</li>';
        $data['simple']['list_31']['expected'].= '</ol>';

        ######################################################################################
        ### list: hierarchical mix of numbered and bulleted (ascent and descent algorithm) ###
        ######################################################################################

        $data['simple']['list_40']['data'] = '1. numbered list item 1'.NL;
        $data['simple']['list_40']['data'].= '2. numbered list item 2'.NL;
        $data['simple']['list_40']['data'].= '  - list item 2.1'.NL;
        $data['simple']['list_40']['data'].= '  - list item 2.2'.NL;
        $data['simple']['list_40']['data'].= '    221. numbered list item 2.2.1'.NL;
        $data['simple']['list_40']['data'].= '    222. numbered list item 2.2.2'.NL;
        $data['simple']['list_40']['data'].= '  - list item 2.3'.NL;
        $data['simple']['list_40']['data'].= '  - list item 2.4'.NL;
        $data['simple']['list_40']['data'].= '3. numbered list item 3'.NL;
        $data['simple']['list_40']['data'].= '4. numbered list item 4'.NL;
        $data['simple']['list_40']['data'].= '  - list item 4.1'.NL;
        $data['simple']['list_40']['data'].= '  - list item 4.2'.NL;
        $data['simple']['list_40']['data'].= '    421. numbered list item 4.2.1'.NL;
        $data['simple']['list_40']['data'].= '    422. numbered list item 4.2.2'.NL;
        $data['simple']['list_40']['data'].= '  - list item 4.3'.NL;
        $data['simple']['list_40']['data'].= '  - list item 4.4'.NL;
        $data['simple']['list_40']['data'].= '5. numbered list item 5'.NL;
        $data['simple']['list_40']['data'].= '6. numbered list item 6';
        $data['simple']['list_40']['expected'] = '<ol>';
        $data['simple']['list_40']['expected'].=   '<li>numbered list item 1</li>';
        $data['simple']['list_40']['expected'].=   '<li>numbered list item 2';
        $data['simple']['list_40']['expected'].=     '<ul>';
        $data['simple']['list_40']['expected'].=       '<li>list item 2.1</li>';
        $data['simple']['list_40']['expected'].=       '<li>list item 2.2';
        $data['simple']['list_40']['expected'].=         '<ol>';
        $data['simple']['list_40']['expected'].=           '<li>numbered list item 2.2.1</li>';
        $data['simple']['list_40']['expected'].=           '<li>numbered list item 2.2.2</li>';
        $data['simple']['list_40']['expected'].=         '</ol>';
        $data['simple']['list_40']['expected'].=       '</li>';
        $data['simple']['list_40']['expected'].=       '<li>list item 2.3</li>';
        $data['simple']['list_40']['expected'].=       '<li>list item 2.4</li>';
        $data['simple']['list_40']['expected'].=     '</ul>';
        $data['simple']['list_40']['expected'].=   '</li>';
        $data['simple']['list_40']['expected'].=   '<li>numbered list item 3</li>';
        $data['simple']['list_40']['expected'].=   '<li>numbered list item 4';
        $data['simple']['list_40']['expected'].=     '<ul>';
        $data['simple']['list_40']['expected'].=       '<li>list item 4.1</li>';
        $data['simple']['list_40']['expected'].=       '<li>list item 4.2';
        $data['simple']['list_40']['expected'].=         '<ol>';
        $data['simple']['list_40']['expected'].=           '<li>numbered list item 4.2.1</li>';
        $data['simple']['list_40']['expected'].=           '<li>numbered list item 4.2.2</li>';
        $data['simple']['list_40']['expected'].=         '</ol>';
        $data['simple']['list_40']['expected'].=       '</li>';
        $data['simple']['list_40']['expected'].=       '<li>list item 4.3</li>';
        $data['simple']['list_40']['expected'].=       '<li>list item 4.4</li>';
        $data['simple']['list_40']['expected'].=     '</ul>';
        $data['simple']['list_40']['expected'].=   '</li>';
        $data['simple']['list_40']['expected'].=   '<li>numbered list item 5</li>';
        $data['simple']['list_40']['expected'].=   '<li>numbered list item 6</li>';
        $data['simple']['list_40']['expected'].= '</ol>';

        $data['simple']['list_41']['data'] = '   1.   numbered list item 1'.NL;
        $data['simple']['list_41']['data'].= '   2.   numbered list item 2'.NL;
        $data['simple']['list_41']['data'].= '     -   list item 2.1'.NL;
        $data['simple']['list_41']['data'].= '     -   list item 2.2'.NL;
        $data['simple']['list_41']['data'].= '       221.   numbered list item 2.2.1'.NL;
        $data['simple']['list_41']['data'].= '       222.   numbered list item 2.2.2'.NL;
        $data['simple']['list_41']['data'].= '     -   list item 2.3'.NL;
        $data['simple']['list_41']['data'].= '     -   list item 2.4'.NL;
        $data['simple']['list_41']['data'].= '   3.   numbered list item 3'.NL;
        $data['simple']['list_41']['data'].= '   4.   numbered list item 4'.NL;
        $data['simple']['list_41']['data'].= '     -   list item 4.1'.NL;
        $data['simple']['list_41']['data'].= '     -   list item 4.2'.NL;
        $data['simple']['list_41']['data'].= '       421.   numbered list item 4.2.1'.NL;
        $data['simple']['list_41']['data'].= '       422.   numbered list item 4.2.2'.NL;
        $data['simple']['list_41']['data'].= '     -   list item 4.3'.NL;
        $data['simple']['list_41']['data'].= '     -   list item 4.4'.NL;
        $data['simple']['list_41']['data'].= '   5.   numbered list item 5'.NL;
        $data['simple']['list_41']['data'].= '   6.   numbered list item 6';
        $data['simple']['list_41']['expected'] = '<ol>';
        $data['simple']['list_41']['expected'].=   '<li>numbered list item 1</li>';
        $data['simple']['list_41']['expected'].=   '<li>numbered list item 2';
        $data['simple']['list_41']['expected'].=     '<ul>';
        $data['simple']['list_41']['expected'].=       '<li>list item 2.1</li>';
        $data['simple']['list_41']['expected'].=       '<li>list item 2.2';
        $data['simple']['list_41']['expected'].=         '<ol>';
        $data['simple']['list_41']['expected'].=           '<li>numbered list item 2.2.1</li>';
        $data['simple']['list_41']['expected'].=           '<li>numbered list item 2.2.2</li>';
        $data['simple']['list_41']['expected'].=         '</ol>';
        $data['simple']['list_41']['expected'].=       '</li>';
        $data['simple']['list_41']['expected'].=       '<li>list item 2.3</li>';
        $data['simple']['list_41']['expected'].=       '<li>list item 2.4</li>';
        $data['simple']['list_41']['expected'].=     '</ul>';
        $data['simple']['list_41']['expected'].=   '</li>';
        $data['simple']['list_41']['expected'].=   '<li>numbered list item 3</li>';
        $data['simple']['list_41']['expected'].=   '<li>numbered list item 4';
        $data['simple']['list_41']['expected'].=     '<ul>';
        $data['simple']['list_41']['expected'].=       '<li>list item 4.1</li>';
        $data['simple']['list_41']['expected'].=       '<li>list item 4.2';
        $data['simple']['list_41']['expected'].=         '<ol>';
        $data['simple']['list_41']['expected'].=           '<li>numbered list item 4.2.1</li>';
        $data['simple']['list_41']['expected'].=           '<li>numbered list item 4.2.2</li>';
        $data['simple']['list_41']['expected'].=         '</ol>';
        $data['simple']['list_41']['expected'].=       '</li>';
        $data['simple']['list_41']['expected'].=       '<li>list item 4.3</li>';
        $data['simple']['list_41']['expected'].=       '<li>list item 4.4</li>';
        $data['simple']['list_41']['expected'].=     '</ul>';
        $data['simple']['list_41']['expected'].=   '</li>';
        $data['simple']['list_41']['expected'].=   '<li>numbered list item 5</li>';
        $data['simple']['list_41']['expected'].=   '<li>numbered list item 6</li>';
        $data['simple']['list_41']['expected'].= '</ol>';

        #################
        ### paragraph ###
        #################

        $data['simple']['paragraph_1']['data'] = 'paragraph line 1'.NL;
        $data['simple']['paragraph_1']['data'].= 'paragraph line 2';
        $data['simple']['paragraph_1']['expected'] = '<p>';
        $data['simple']['paragraph_1']['expected'].=   'paragraph line 1'.NL;
        $data['simple']['paragraph_1']['expected'].=   'paragraph line 2';
        $data['simple']['paragraph_1']['expected'].= '</p>';

        $data['simple']['paragraph_2']['data'] = '   paragraph line 1   '.NL;
        $data['simple']['paragraph_2']['data'].= '      paragraph line 2   ';
        $data['simple']['paragraph_2']['expected'] = '<p>';
        $data['simple']['paragraph_2']['expected'].=   'paragraph line 1';
        $data['simple']['paragraph_2']['expected'].=   '<br>'.NL;
        $data['simple']['paragraph_2']['expected'].=   '      paragraph line 2   ';
        $data['simple']['paragraph_2']['expected'].= '</p>';

        ##################
        ### blockquote ###
        ##################

        $data['simple']['blockquote_1']['data'] = '>blockquote line   ';
        $data['simple']['blockquote_1']['expected'] = '<blockquote>';
        $data['simple']['blockquote_1']['expected'].=   '<p>';
        $data['simple']['blockquote_1']['expected'].=     'blockquote line';
        $data['simple']['blockquote_1']['expected'].=   '</p>';
        $data['simple']['blockquote_1']['expected'].= '</blockquote>';

        $data['simple']['blockquote_2']['data'] = '   >   blockquote line 1   '.NL;
        $data['simple']['blockquote_2']['data'].= '   >   blockquote line 2   ';
        $data['simple']['blockquote_2']['expected'] = '<blockquote>';
        $data['simple']['blockquote_2']['expected'].=   '<p>';
        $data['simple']['blockquote_2']['expected'].=     'blockquote line 1';
        $data['simple']['blockquote_2']['expected'].=     '<br>'.NL;
        $data['simple']['blockquote_2']['expected'].=     '  blockquote line 2';
        $data['simple']['blockquote_2']['expected'].=   '</p>';
        $data['simple']['blockquote_2']['expected'].= '</blockquote>';

        $data['simple']['blockquote_3']['data'] = '> blockquote line 1'.NL;
        $data['simple']['blockquote_3']['data'].= 'blockquote line 2';
        $data['simple']['blockquote_3']['expected'] = '<blockquote>';
        $data['simple']['blockquote_3']['expected'].=   '<p>';
        $data['simple']['blockquote_3']['expected'].=     'blockquote line 1'.NL;
        $data['simple']['blockquote_3']['expected'].=     'blockquote line 2';
        $data['simple']['blockquote_3']['expected'].=   '</p>';
        $data['simple']['blockquote_3']['expected'].= '</blockquote>';

        $data['simple']['blockquote_4']['data'] = '> blockquote line 1'.NL;
        $data['simple']['blockquote_4']['data'].= '          blockquote line 2';
        $data['simple']['blockquote_4']['expected'] = '<blockquote>';
        $data['simple']['blockquote_4']['expected'].=   '<p>';
        $data['simple']['blockquote_4']['expected'].=     'blockquote line 1'.NL;
        $data['simple']['blockquote_4']['expected'].=     '          blockquote line 2';
        $data['simple']['blockquote_4']['expected'].=   '</p>';
        $data['simple']['blockquote_4']['expected'].= '</blockquote>';

        $data['simple']['blockquote_5']['data'] = '>          blockquote line 1'.NL;
        $data['simple']['blockquote_5']['data'].= 'blockquote line 2';
        $data['simple']['blockquote_5']['expected'] = '<blockquote>';
        $data['simple']['blockquote_5']['expected'].=   '<pre>';
        $data['simple']['blockquote_5']['expected'].=     '<code>     ';
        $data['simple']['blockquote_5']['expected'].=       'blockquote line 1';
        $data['simple']['blockquote_5']['expected'].=     '</code>';
        $data['simple']['blockquote_5']['expected'].=   '</pre>';
        $data['simple']['blockquote_5']['expected'].=   '<p>';
        $data['simple']['blockquote_5']['expected'].=     'blockquote line 2';
        $data['simple']['blockquote_5']['expected'].=   '</p>';
        $data['simple']['blockquote_5']['expected'].= '</blockquote>';

        $data['simple']['blockquote_6']['data'] = '>          blockquote line 1'.NL;
        $data['simple']['blockquote_6']['data'].= '          blockquote line 2';
        $data['simple']['blockquote_6']['expected'] = '<blockquote>';
        $data['simple']['blockquote_6']['expected'].=   '<pre>';
        $data['simple']['blockquote_6']['expected'].=     '<code>';
        $data['simple']['blockquote_6']['expected'].=       '     blockquote line 1'.NL;
        $data['simple']['blockquote_6']['expected'].=       '      blockquote line 2';
        $data['simple']['blockquote_6']['expected'].=     '</code>';
        $data['simple']['blockquote_6']['expected'].=   '</pre>';
        $data['simple']['blockquote_6']['expected'].= '</blockquote>';

        $data['simple']['blockquote_7']['data'] = '> blockquote line 1'.NL;
        $data['simple']['blockquote_7']['data'].= '> blockquote line 2';
        $data['simple']['blockquote_7']['expected'] = '<blockquote>';
        $data['simple']['blockquote_7']['expected'].=   '<p>';
        $data['simple']['blockquote_7']['expected'].=     'blockquote line 1'.NL;
        $data['simple']['blockquote_7']['expected'].=     'blockquote line 2';
        $data['simple']['blockquote_7']['expected'].=   '</p>';
        $data['simple']['blockquote_7']['expected'].= '</blockquote>';

        $data['simple']['blockquote_8']['data'] = '> blockquote line 1'.NL;
        $data['simple']['blockquote_8']['data'].= '         > blockquote line 2';
        $data['simple']['blockquote_8']['expected'] = '<blockquote>';
        $data['simple']['blockquote_8']['expected'].=   '<p>';
        $data['simple']['blockquote_8']['expected'].=      'blockquote line 1';
        $data['simple']['blockquote_8']['expected'].=   '</p>';
        $data['simple']['blockquote_8']['expected'].= '</blockquote>';
        $data['simple']['blockquote_8']['expected'].= '<pre>';
        $data['simple']['blockquote_8']['expected'].=   '<code>';
        $data['simple']['blockquote_8']['expected'].=     '     &gt; blockquote line 2';
        $data['simple']['blockquote_8']['expected'].=   '</code>';
        $data['simple']['blockquote_8']['expected'].= '</pre>';

        $data['simple']['blockquote_9']['data'] = '>          blockquote line 1'.NL;
        $data['simple']['blockquote_9']['data'].= '> blockquote line 2';
        $data['simple']['blockquote_9']['expected'] = '<blockquote>';
        $data['simple']['blockquote_9']['expected'].=   '<pre>';
        $data['simple']['blockquote_9']['expected'].=     '<code>';
        $data['simple']['blockquote_9']['expected'].=       '     blockquote line 1';
        $data['simple']['blockquote_9']['expected'].=     '</code>';
        $data['simple']['blockquote_9']['expected'].=   '</pre>';
        $data['simple']['blockquote_9']['expected'].=   '<p>';
        $data['simple']['blockquote_9']['expected'].=     'blockquote line 2';
        $data['simple']['blockquote_9']['expected'].=   '</p>';
        $data['simple']['blockquote_9']['expected'].= '</blockquote>';

        $data['simple']['blockquote_10']['data'] = '>          blockquote line 1'.NL;
        $data['simple']['blockquote_10']['data'].= '         > blockquote line 2';
        $data['simple']['blockquote_10']['expected'] = '<blockquote>';
        $data['simple']['blockquote_10']['expected'].=   '<pre>';
        $data['simple']['blockquote_10']['expected'].=     '<code>';
        $data['simple']['blockquote_10']['expected'].=       '     blockquote line 1';
        $data['simple']['blockquote_10']['expected'].=     '</code>';
        $data['simple']['blockquote_10']['expected'].=   '</pre>';
        $data['simple']['blockquote_10']['expected'].= '</blockquote>';
        $data['simple']['blockquote_10']['expected'].= '<pre>';
        $data['simple']['blockquote_10']['expected'].=   '<code>';
        $data['simple']['blockquote_10']['expected'].=     '     &gt; blockquote line 2';
        $data['simple']['blockquote_10']['expected'].=   '</code>';
        $data['simple']['blockquote_10']['expected'].= '</pre>';

        ############
        ### code ###
        ############

        $data['simple']['code']['data'] = '    code line 1     '.NL;
        $data['simple']['code']['data'].= '         code line 2'.NL;
        $data['simple']['code']['data'].= '    code line 3';
        $data['simple']['code']['expected'] = '<pre>';
        $data['simple']['code']['expected'].=   '<code>';
        $data['simple']['code']['expected'].=     'code line 1     '.NL;
        $data['simple']['code']['expected'].=       '     code line 2'.NL;
        $data['simple']['code']['expected'].=     'code line 3';
        $data['simple']['code']['expected'].=   '</code>';
        $data['simple']['code']['expected'].= '</pre>';

        ##################################
        ### italic/bold/code in header ###
        ##################################

        $data['simple']['decoration_header_1']['data'] = '# Title with *italic text*';
        $data['simple']['decoration_header_1']['expected'] = '<h1>Title with <em>italic text</em></h1>';

        $data['simple']['decoration_header_2']['data'] = '# Title with _italic text_';
        $data['simple']['decoration_header_2']['expected'] = '<h1>Title with <em>italic text</em></h1>';

        $data['simple']['decoration_header_3']['data'] = '# Title with **bold text**';
        $data['simple']['decoration_header_3']['expected'] = '<h1>Title with <strong>bold text</strong></h1>';

        $data['simple']['decoration_header_4']['data'] = '# Title with __bold text__';
        $data['simple']['decoration_header_4']['expected'] = '<h1>Title with <strong>bold text</strong></h1>';

        $data['simple']['decoration_header_5']['data'] = '# Title with ***italic and bold text***';
        $data['simple']['decoration_header_5']['expected'] = '<h1>Title with <em><strong>italic and bold text</strong></em></h1>';

        $data['simple']['decoration_header_6']['data'] = '# Title with ___italic and bold text___';
        $data['simple']['decoration_header_6']['expected'] = '<h1>Title with <em><strong>italic and bold text</strong></em></h1>';

        $data['simple']['decoration_header_7']['data'] = '# Title with `code`';
        $data['simple']['decoration_header_7']['expected'] = '<h1>Title with <code>code</code></h1>';

        #####################################
        ### italic/bold/code in paragraph ###
        #####################################

        $data['simple']['decoration_paragraph_1']['data'] = 'paragraph line 1'.NL;
        $data['simple']['decoration_paragraph_1']['data'].= 'paragraph *italic line 2*'.NL;
        $data['simple']['decoration_paragraph_1']['data'].= 'paragraph line 3';
        $data['simple']['decoration_paragraph_1']['expected'] = '<p>';
        $data['simple']['decoration_paragraph_1']['expected'].=   'paragraph line 1'.NL;
        $data['simple']['decoration_paragraph_1']['expected'].=   'paragraph <em>italic line 2</em>'.NL;
        $data['simple']['decoration_paragraph_1']['expected'].=   'paragraph line 3';
        $data['simple']['decoration_paragraph_1']['expected'].= '</p>';

        $data['simple']['decoration_paragraph_2']['data'] = 'paragraph line 1'.NL;
        $data['simple']['decoration_paragraph_2']['data'].= 'paragraph _italic line 2_'.NL;
        $data['simple']['decoration_paragraph_2']['data'].= 'paragraph line 3';
        $data['simple']['decoration_paragraph_2']['expected'] = '<p>';
        $data['simple']['decoration_paragraph_2']['expected'].=   'paragraph line 1'.NL;
        $data['simple']['decoration_paragraph_2']['expected'].=   'paragraph <em>italic line 2</em>'.NL;
        $data['simple']['decoration_paragraph_2']['expected'].=   'paragraph line 3';
        $data['simple']['decoration_paragraph_2']['expected'].= '</p>';

        $data['simple']['decoration_paragraph_3']['data'] = 'paragraph line 1'.NL;
        $data['simple']['decoration_paragraph_3']['data'].= 'paragraph **bold line 2**'.NL;
        $data['simple']['decoration_paragraph_3']['data'].= 'paragraph line 3';
        $data['simple']['decoration_paragraph_3']['expected'] = '<p>';
        $data['simple']['decoration_paragraph_3']['expected'].=   'paragraph line 1'.NL;
        $data['simple']['decoration_paragraph_3']['expected'].=   'paragraph <strong>bold line 2</strong>'.NL;
        $data['simple']['decoration_paragraph_3']['expected'].=   'paragraph line 3';
        $data['simple']['decoration_paragraph_3']['expected'].= '</p>';

        $data['simple']['decoration_paragraph_4']['data'] = 'paragraph line 1'.NL;
        $data['simple']['decoration_paragraph_4']['data'].= 'paragraph __bold line 2__'.NL;
        $data['simple']['decoration_paragraph_4']['data'].= 'paragraph line 3';
        $data['simple']['decoration_paragraph_4']['expected'] = '<p>';
        $data['simple']['decoration_paragraph_4']['expected'].=   'paragraph line 1'.NL;
        $data['simple']['decoration_paragraph_4']['expected'].=   'paragraph <strong>bold line 2</strong>'.NL;
        $data['simple']['decoration_paragraph_4']['expected'].=   'paragraph line 3';
        $data['simple']['decoration_paragraph_4']['expected'].= '</p>';

        $data['simple']['decoration_paragraph_5']['data'] = 'paragraph line 1'.NL;
        $data['simple']['decoration_paragraph_5']['data'].= 'paragraph ***italic and bold line 2***'.NL;
        $data['simple']['decoration_paragraph_5']['data'].= 'paragraph line 3';
        $data['simple']['decoration_paragraph_5']['expected'] = '<p>';
        $data['simple']['decoration_paragraph_5']['expected'].=   'paragraph line 1'.NL;
        $data['simple']['decoration_paragraph_5']['expected'].=   'paragraph <em><strong>italic and bold line 2</strong></em>'.NL;
        $data['simple']['decoration_paragraph_5']['expected'].=   'paragraph line 3';
        $data['simple']['decoration_paragraph_5']['expected'].= '</p>';

        $data['simple']['decoration_paragraph_6']['data'] = 'paragraph line 1'.NL;
        $data['simple']['decoration_paragraph_6']['data'].= 'paragraph ___italic and bold line 2___'.NL;
        $data['simple']['decoration_paragraph_6']['data'].= 'paragraph line 3';
        $data['simple']['decoration_paragraph_6']['expected'] = '<p>';
        $data['simple']['decoration_paragraph_6']['expected'].=   'paragraph line 1'.NL;
        $data['simple']['decoration_paragraph_6']['expected'].=   'paragraph <em><strong>italic and bold line 2</strong></em>'.NL;
        $data['simple']['decoration_paragraph_6']['expected'].=   'paragraph line 3';
        $data['simple']['decoration_paragraph_6']['expected'].= '</p>';

        $data['simple']['decoration_paragraph_7']['data'] = 'paragraph line 1'.NL;
        $data['simple']['decoration_paragraph_7']['data'].= 'paragraph `code line 2`'.NL;
        $data['simple']['decoration_paragraph_7']['data'].= 'paragraph line 3';
        $data['simple']['decoration_paragraph_7']['expected'] = '<p>';
        $data['simple']['decoration_paragraph_7']['expected'].=   'paragraph line 1'.NL;
        $data['simple']['decoration_paragraph_7']['expected'].=   'paragraph <code>code line 2</code>'.NL;
        $data['simple']['decoration_paragraph_7']['expected'].=   'paragraph line 3';
        $data['simple']['decoration_paragraph_7']['expected'].= '</p>';

        ######################################
        ### italic/bold/code in blockquote ###
        ######################################

        $data['simple']['decoration_blockquote_1']['data'] = '> blockquote line 1'.NL;
        $data['simple']['decoration_blockquote_1']['data'].= '> blockquote *italic line 2*'.NL;
        $data['simple']['decoration_blockquote_1']['data'].= '> blockquote line 3';
        $data['simple']['decoration_blockquote_1']['expected'] = '<blockquote>';
        $data['simple']['decoration_blockquote_1']['expected'].=   '<p>';
        $data['simple']['decoration_blockquote_1']['expected'].=     'blockquote line 1'.NL;
        $data['simple']['decoration_blockquote_1']['expected'].=     'blockquote <em>italic line 2</em>'.NL;
        $data['simple']['decoration_blockquote_1']['expected'].=     'blockquote line 3';
        $data['simple']['decoration_blockquote_1']['expected'].=   '</p>';
        $data['simple']['decoration_blockquote_1']['expected'].= '</blockquote>';

        $data['simple']['decoration_blockquote_2']['data'] = '> blockquote line 1'.NL;
        $data['simple']['decoration_blockquote_2']['data'].= '> blockquote _italic line 2_'.NL;
        $data['simple']['decoration_blockquote_2']['data'].= '> blockquote line 3';
        $data['simple']['decoration_blockquote_2']['expected'] = '<blockquote>';
        $data['simple']['decoration_blockquote_2']['expected'].=   '<p>';
        $data['simple']['decoration_blockquote_2']['expected'].=     'blockquote line 1'.NL;
        $data['simple']['decoration_blockquote_2']['expected'].=     'blockquote <em>italic line 2</em>'.NL;
        $data['simple']['decoration_blockquote_2']['expected'].=     'blockquote line 3';
        $data['simple']['decoration_blockquote_2']['expected'].=   '</p>';
        $data['simple']['decoration_blockquote_2']['expected'].= '</blockquote>';

        $data['simple']['decoration_blockquote_3']['data'] = '> blockquote line 1'.NL;
        $data['simple']['decoration_blockquote_3']['data'].= '> blockquote **bold line 2**'.NL;
        $data['simple']['decoration_blockquote_3']['data'].= '> blockquote line 3';
        $data['simple']['decoration_blockquote_3']['expected'] = '<blockquote>';
        $data['simple']['decoration_blockquote_3']['expected'].=   '<p>';
        $data['simple']['decoration_blockquote_3']['expected'].=     'blockquote line 1'.NL;
        $data['simple']['decoration_blockquote_3']['expected'].=     'blockquote <strong>bold line 2</strong>'.NL;
        $data['simple']['decoration_blockquote_3']['expected'].=     'blockquote line 3';
        $data['simple']['decoration_blockquote_3']['expected'].=   '</p>';
        $data['simple']['decoration_blockquote_3']['expected'].= '</blockquote>';

        $data['simple']['decoration_blockquote_4']['data'] = '> blockquote line 1'.NL;
        $data['simple']['decoration_blockquote_4']['data'].= '> blockquote __bold line 2__'.NL;
        $data['simple']['decoration_blockquote_4']['data'].= '> blockquote line 3';
        $data['simple']['decoration_blockquote_4']['expected'] = '<blockquote>';
        $data['simple']['decoration_blockquote_4']['expected'].=   '<p>';
        $data['simple']['decoration_blockquote_4']['expected'].=     'blockquote line 1'.NL;
        $data['simple']['decoration_blockquote_4']['expected'].=     'blockquote <strong>bold line 2</strong>'.NL;
        $data['simple']['decoration_blockquote_4']['expected'].=     'blockquote line 3';
        $data['simple']['decoration_blockquote_4']['expected'].=   '</p>';
        $data['simple']['decoration_blockquote_4']['expected'].= '</blockquote>';

        $data['simple']['decoration_blockquote_5']['data'] = '> blockquote line 1'.NL;
        $data['simple']['decoration_blockquote_5']['data'].= '> blockquote ***italic and bold line 2***'.NL;
        $data['simple']['decoration_blockquote_5']['data'].= '> blockquote line 3';
        $data['simple']['decoration_blockquote_5']['expected'] = '<blockquote>';
        $data['simple']['decoration_blockquote_5']['expected'].=   '<p>';
        $data['simple']['decoration_blockquote_5']['expected'].=     'blockquote line 1'.NL;
        $data['simple']['decoration_blockquote_5']['expected'].=     'blockquote <em><strong>italic and bold line 2</strong></em>'.NL;
        $data['simple']['decoration_blockquote_5']['expected'].=     'blockquote line 3';
        $data['simple']['decoration_blockquote_5']['expected'].=   '</p>';
        $data['simple']['decoration_blockquote_5']['expected'].= '</blockquote>';

        $data['simple']['decoration_blockquote_6']['data'] = '> blockquote line 1'.NL;
        $data['simple']['decoration_blockquote_6']['data'].= '> blockquote ___italic and bold line 2___'.NL;
        $data['simple']['decoration_blockquote_6']['data'].= '> blockquote line 3';
        $data['simple']['decoration_blockquote_6']['expected'] = '<blockquote>';
        $data['simple']['decoration_blockquote_6']['expected'].=   '<p>';
        $data['simple']['decoration_blockquote_6']['expected'].=     'blockquote line 1'.NL;
        $data['simple']['decoration_blockquote_6']['expected'].=     'blockquote <em><strong>italic and bold line 2</strong></em>'.NL;
        $data['simple']['decoration_blockquote_6']['expected'].=     'blockquote line 3';
        $data['simple']['decoration_blockquote_6']['expected'].=   '</p>';
        $data['simple']['decoration_blockquote_6']['expected'].= '</blockquote>';

        $data['simple']['decoration_blockquote_7']['data'] = '> blockquote line 1'.NL;
        $data['simple']['decoration_blockquote_7']['data'].= '> blockquote `code line 2`'.NL;
        $data['simple']['decoration_blockquote_7']['data'].= '> blockquote line 3';
        $data['simple']['decoration_blockquote_7']['expected'] = '<blockquote>';
        $data['simple']['decoration_blockquote_7']['expected'].=   '<p>';
        $data['simple']['decoration_blockquote_7']['expected'].=     'blockquote line 1'.NL;
        $data['simple']['decoration_blockquote_7']['expected'].=     'blockquote <code>code line 2</code>'.NL;
        $data['simple']['decoration_blockquote_7']['expected'].=     'blockquote line 3';
        $data['simple']['decoration_blockquote_7']['expected'].=   '</p>';
        $data['simple']['decoration_blockquote_7']['expected'].= '</blockquote>';

        ################################
        ### italic/bold/code in list ###
        ################################

        $data['simple']['decoration_list_1']['data'] = '- list *italic item*';
        $data['simple']['decoration_list_1']['expected'] = '<ul>';
        $data['simple']['decoration_list_1']['expected'].=   '<li>list <em>italic item</em></li>';
        $data['simple']['decoration_list_1']['expected'].= '</ul>';

        $data['simple']['decoration_list_2']['data'] = '- list _italic item_';
        $data['simple']['decoration_list_2']['expected'] = '<ul>';
        $data['simple']['decoration_list_2']['expected'].=   '<li>list <em>italic item</em></li>';
        $data['simple']['decoration_list_2']['expected'].= '</ul>';

        $data['simple']['decoration_list_3']['data'] = '- list **bold item**';
        $data['simple']['decoration_list_3']['expected'] = '<ul>';
        $data['simple']['decoration_list_3']['expected'].=   '<li>list <strong>bold item</strong></li>';
        $data['simple']['decoration_list_3']['expected'].= '</ul>';

        $data['simple']['decoration_list_4']['data'] = '- list __bold item__';
        $data['simple']['decoration_list_4']['expected'] = '<ul>';
        $data['simple']['decoration_list_4']['expected'].=   '<li>list <strong>bold item</strong></li>';
        $data['simple']['decoration_list_4']['expected'].= '</ul>';

        $data['simple']['decoration_list_5']['data'] = '- list ***italic and bold item***';
        $data['simple']['decoration_list_5']['expected'] = '<ul>';
        $data['simple']['decoration_list_5']['expected'].=   '<li>list <em><strong>italic and bold item</strong></em></li>';
        $data['simple']['decoration_list_5']['expected'].= '</ul>';

        $data['simple']['decoration_list_6']['data'] = '- list ___italic and bold item___';
        $data['simple']['decoration_list_6']['expected'] = '<ul>';
        $data['simple']['decoration_list_6']['expected'].=   '<li>list <em><strong>italic and bold item</strong></em></li>';
        $data['simple']['decoration_list_6']['expected'].= '</ul>';

        $data['simple']['decoration_list_7']['data'] = '- list `code item`';
        $data['simple']['decoration_list_7']['expected'] = '<ul>';
        $data['simple']['decoration_list_7']['expected'].=   '<li>list <code>code item</code></li>';
        $data['simple']['decoration_list_7']['expected'].= '</ul>';

        #############
        ### links ###
        #############

        $data['simple']['links']['data'] = '[]()'.NL;
        $data['simple']['links']['data'].= '[](http://example.com)'.NL;
        $data['simple']['links']['data'].= '[link text]()'.NL;
        $data['simple']['links']['data'].= '[link text](http://example.com)'.NL;
        $data['simple']['links']['data'].= '[]("title")'.NL;
        $data['simple']['links']['data'].= '[](http://example.com "title")'.NL;
        $data['simple']['links']['data'].= '[link text]("title")'.NL;
        $data['simple']['links']['data'].= '[link text](http://example.com "title")';
        $data['simple']['links']['expected'] = '<p>';
        $data['simple']['links']['expected'].=   '<a title="" href=""></a>'.NL;
        $data['simple']['links']['expected'].=   '<a title="" href="http://example.com"></a>'.NL;
        $data['simple']['links']['expected'].=   '<a title="" href="">link text</a>'.NL;
        $data['simple']['links']['expected'].=   '<a title="" href="http://example.com">link text</a>'.NL;
        $data['simple']['links']['expected'].=   '<a title="title" href=""></a>'.NL;
        $data['simple']['links']['expected'].=   '<a title="title" href="http://example.com"></a>'.NL;
        $data['simple']['links']['expected'].=   '<a title="title" href="">link text</a>'.NL;
        $data['simple']['links']['expected'].=   '<a title="title" href="http://example.com">link text</a>';
        $data['simple']['links']['expected'].= '</p>';

        $data['simple']['links_references']['data'] = '[][id_1]'.NL;
        $data['simple']['links_references']['data'].= '[link text 1][id_1]'.NL;
        $data['simple']['links_references']['data'].= '[][id_2]'.NL;
        $data['simple']['links_references']['data'].= '[link text 2][id_2]'.NL;
        $data['simple']['links_references']['data'].= '[][id_3]'.NL;
        $data['simple']['links_references']['data'].= '[link text 3][id_3]'.NL;
        $data['simple']['links_references']['data'].= '[link text 4][]'.NL;
        $data['simple']['links_references']['data'].= '[link text 5][]'.NL;
        $data['simple']['links_references']['data'].= '[][id_unknown]'.NL;
        $data['simple']['links_references']['data'].= '[link text unknown][id_unknown]'.NL;
        $data['simple']['links_references']['data'].= '<!-- references -->'.NL;
        $data['simple']['links_references']['data'].= '[id_1]: http://example.com/'.NL;
        $data['simple']['links_references']['data'].= '[id_2]: "Title"'.NL;
        $data['simple']['links_references']['data'].= '[id_3]: http://example.com/ "Title"'.NL;
        $data['simple']['links_references']['data'].= '[link text 4]: http://example.com/'.NL;
        $data['simple']['links_references']['data'].= '[link text 5]: http://example.com/ "Title"';
        $data['simple']['links_references']['expected'] = '<p>';
        $data['simple']['links_references']['expected'].=   '<a title="" href="http://example.com/"></a>'.NL;
        $data['simple']['links_references']['expected'].=   '<a title="" href="http://example.com/">link text 1</a>'.NL;
        $data['simple']['links_references']['expected'].=   '<a title="Title" href=""></a>'.NL;
        $data['simple']['links_references']['expected'].=   '<a title="Title" href="">link text 2</a>'.NL;
        $data['simple']['links_references']['expected'].=   '<a title="Title" href="http://example.com/"></a>'.NL;
        $data['simple']['links_references']['expected'].=   '<a title="Title" href="http://example.com/">link text 3</a>'.NL;
        $data['simple']['links_references']['expected'].=   '<a title="" href="http://example.com/">link text 4</a>'.NL;
        $data['simple']['links_references']['expected'].=   '<a title="Title" href="http://example.com/">link text 5</a>'.NL;
        $data['simple']['links_references']['expected'].=   '[][id<em>unknown]'.NL;
        $data['simple']['links_references']['expected'].=   '[link text unknown][id</em>unknown]'.NL;
        $data['simple']['links_references']['expected'].=   '<!-- references -->'.NL.NL.NL.NL.NL;
        $data['simple']['links_references']['expected'].= '</p>';

        $data['simple']['link_and_email']['data'] = 'url: <http://example.com>'.NL;
        $data['simple']['link_and_email']['data'].= 'email: <user@example.com>';
        $data['simple']['link_and_email']['expected'] = '<p>';
        $data['simple']['link_and_email']['expected'].=   'url: <a href="http://example.com">http://example.com</a>'.NL;
        $data['simple']['link_and_email']['expected'].=   'email: <a href="mailto:user@example.com">user@example.com</a>';
        $data['simple']['link_and_email']['expected'].= '</p>';

        ##############
        ### images ###
        ##############

        $data['simple']['images']['data'] = '![]()'.NL;
        $data['simple']['images']['data'].= '![alt text]()'.NL;
        $data['simple']['images']['data'].= '![](path)'.NL;
        $data['simple']['images']['data'].= '![alt text](path)'.NL;
        $data['simple']['images']['data'].= '![]("Title")'.NL;
        $data['simple']['images']['data'].= '![alt text]("Title")'.NL;
        $data['simple']['images']['data'].= '![](path "Title")'.NL;
        $data['simple']['images']['data'].= '![alt text](path "Title")';
        $data['simple']['images']['expected'] = '<p>';
        $data['simple']['images']['expected'].=   '<img title="" src="" alt="">'.NL;
        $data['simple']['images']['expected'].=   '<img title="" src="" alt="alt text">'.NL;
        $data['simple']['images']['expected'].=   '<img title="" src="path" alt="">'.NL;
        $data['simple']['images']['expected'].=   '<img title="" src="path" alt="alt text">'.NL;
        $data['simple']['images']['expected'].=   '<img title="Title" src="" alt="">'.NL;
        $data['simple']['images']['expected'].=   '<img title="Title" src="" alt="alt text">'.NL;
        $data['simple']['images']['expected'].=   '<img title="Title" src="path" alt="">'.NL;
        $data['simple']['images']['expected'].=   '<img title="Title" src="path" alt="alt text">';
        $data['simple']['images']['expected'].= '</p>';

        $data['simple']['images_references']['data'] = '![][id_1]'.NL;
        $data['simple']['images_references']['data'].= '![alt text 1][id_1]'.NL;
        $data['simple']['images_references']['data'].= '![][id_2]'.NL;
        $data['simple']['images_references']['data'].= '![alt text 2][id_2]'.NL;
        $data['simple']['images_references']['data'].= '![][id_3]'.NL;
        $data['simple']['images_references']['data'].= '![alt text 3][id_3]'.NL;
        $data['simple']['images_references']['data'].= '![][id_4]'.NL;
        $data['simple']['images_references']['data'].= '![alt text 4][id_4]'.NL;
        $data['simple']['images_references']['data'].= '<!-- references -->'.NL;
        $data['simple']['images_references']['data'].= '[id_1]: path'.NL;
        $data['simple']['images_references']['data'].= '[id_2]: "Title"'.NL;
        $data['simple']['images_references']['data'].= '[id_3]: path "Title"';
        $data['simple']['images_references']['expected'] = '<p><img title="" src="path" alt="">'.NL;
        $data['simple']['images_references']['expected'].=   '<img title="" src="path" alt="alt text 1">'.NL;
        $data['simple']['images_references']['expected'].=   '<img title="Title" src="" alt="">'.NL;
        $data['simple']['images_references']['expected'].=   '<img title="Title" src="" alt="alt text 2">'.NL;
        $data['simple']['images_references']['expected'].=   '<img title="Title" src="path" alt="">'.NL;
        $data['simple']['images_references']['expected'].=   '<img title="Title" src="path" alt="alt text 3">'.NL;
        $data['simple']['images_references']['expected'].=   '![][id<em>4]'.NL;
        $data['simple']['images_references']['expected'].=   '![alt text 4][id</em>4]'.NL;
        $data['simple']['images_references']['expected'].=   '<!-- references -->'.NL.NL.NL;
        $data['simple']['images_references']['expected'].= '</p>';

        foreach ($data['simple'] as $c_row_id => $c_info) {
            $c_expected = $c_info['expected'];
            $c_value = $c_info['data'];
            $c_gotten = Markdown::markdown_to_markup($c_value)->render();
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__markdown_to_markup__separation(&$test, $dpath, &$c_results) {

        ##########################
        ### header near header ###
        ##########################

        $data['separation'][100]['data'] = '# Title H1 (atx-style)'.NL;
        $data['separation'][100]['data'].= '## Title H2 (atx-style)';
        $data['separation'][100]['expected'] = '<h1>Title H1 (atx-style)</h1>';
        $data['separation'][100]['expected'].= '<h2>Title H2 (atx-style)</h2>';

        $data['separation'][101]['data'] = 'Title H1 (Setext-style)'.NL;
        $data['separation'][101]['data'].= '======================='.NL;
        $data['separation'][101]['data'].= 'Title H2 (Setext-style)'.NL;
        $data['separation'][101]['data'].= '-----------------------';
        $data['separation'][101]['expected'] = '<h1>Title H1 (Setext-style)</h1>';
        $data['separation'][101]['expected'].= '<h2>Title H2 (Setext-style)</h2>';

        $data['separation'][102]['data'] = '# Title H1 (atx-style)'.NL;
        $data['separation'][102]['data'].= 'Title H2 (Setext-style)'.NL;
        $data['separation'][102]['data'].= '-----------------------';
        $data['separation'][102]['expected'] = '<h1>Title H1 (atx-style)</h1>';
        $data['separation'][102]['expected'].= '<h2>Title H2 (Setext-style)</h2>';

        $data['separation'][103]['data'] = 'Title H1 (Setext-style)'.NL;
        $data['separation'][103]['data'].= '======================='.NL;
        $data['separation'][103]['data'].= '## Title H2 (atx-style)';
        $data['separation'][103]['expected'] = '<h1>Title H1 (Setext-style)</h1>';
        $data['separation'][103]['expected'].= '<h2>Title H2 (atx-style)</h2>';

        $data['separation'][104]['data'] = '# Title H1 (atx-style)'.NL;
        $data['separation'][104]['data'].= NL;
        $data['separation'][104]['data'].= '## Title H2 (atx-style)';
        $data['separation'][104]['expected'] = '<h1>Title H1 (atx-style)</h1>';
        $data['separation'][104]['expected'].= '<h2>Title H2 (atx-style)</h2>';

        $data['separation'][105]['data'] = 'Title H1 (Setext-style)'.NL;
        $data['separation'][105]['data'].= '======================='.NL;
        $data['separation'][105]['data'].= NL;
        $data['separation'][105]['data'].= 'Title H2 (Setext-style)'.NL;
        $data['separation'][105]['data'].= '-----------------------';
        $data['separation'][105]['expected'] = '<h1>Title H1 (Setext-style)</h1>';
        $data['separation'][105]['expected'].= '<h2>Title H2 (Setext-style)</h2>';

        $data['separation'][106]['data'] = '# Title H1 (atx-style)'.NL;
        $data['separation'][106]['data'].= NL;
        $data['separation'][106]['data'].= 'Title H2 (Setext-style)'.NL;
        $data['separation'][106]['data'].= '-----------------------';
        $data['separation'][106]['expected'] = '<h1>Title H1 (atx-style)</h1>';
        $data['separation'][106]['expected'].= '<h2>Title H2 (Setext-style)</h2>';

        $data['separation'][107]['data'] = 'Title H1 (Setext-style)'.NL;
        $data['separation'][107]['data'].= '======================='.NL;
        $data['separation'][107]['data'].= NL;
        $data['separation'][107]['data'].= '## Title H2 (atx-style)';
        $data['separation'][107]['expected'] = '<h1>Title H1 (Setext-style)</h1>';
        $data['separation'][107]['expected'].= '<h2>Title H2 (atx-style)</h2>';

        #############################
        ### header near paragraph ###
        #############################

        $data['separation'][110]['data'] = '# Title H1 (atx-style)'.NL;
        $data['separation'][110]['data'].= 'paragraph line 1'.NL;
        $data['separation'][110]['data'].= 'paragraph line 2'.NL;
        $data['separation'][110]['data'].= 'paragraph line 3';
        $data['separation'][110]['expected'] = '<h1>Title H1 (atx-style)</h1>';
        $data['separation'][110]['expected'].= '<p>';
        $data['separation'][110]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][110]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][110]['expected'].=   'paragraph line 3';
        $data['separation'][110]['expected'].= '</p>';

        $data['separation'][111]['data'] = '# Title H1 (atx-style)'.NL;
        $data['separation'][111]['data'].= NL;
        $data['separation'][111]['data'].= 'paragraph line 1'.NL;
        $data['separation'][111]['data'].= 'paragraph line 2'.NL;
        $data['separation'][111]['data'].= 'paragraph line 3';
        $data['separation'][111]['expected'] = '<h1>Title H1 (atx-style)</h1>';
        $data['separation'][111]['expected'].= '<p>';
        $data['separation'][111]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][111]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][111]['expected'].=   'paragraph line 3';
        $data['separation'][111]['expected'].= '</p>';

        $data['separation'][112]['data'] = 'Title H1 (Setext-style)'.NL;
        $data['separation'][112]['data'].= '======================='.NL;
        $data['separation'][112]['data'].= 'paragraph line 1'.NL;
        $data['separation'][112]['data'].= 'paragraph line 2'.NL;
        $data['separation'][112]['data'].= 'paragraph line 3';
        $data['separation'][112]['expected'] = '<h1>Title H1 (Setext-style)</h1>';
        $data['separation'][112]['expected'].= '<p>';
        $data['separation'][112]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][112]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][112]['expected'].=   'paragraph line 3';
        $data['separation'][112]['expected'].= '</p>';

        $data['separation'][113]['data'] = 'Title H1 (Setext-style)'.NL;
        $data['separation'][113]['data'].= '======================='.NL;
        $data['separation'][113]['data'].= NL;
        $data['separation'][113]['data'].= 'paragraph line 1'.NL;
        $data['separation'][113]['data'].= 'paragraph line 2'.NL;
        $data['separation'][113]['data'].= 'paragraph line 3';
        $data['separation'][113]['expected'] = '<h1>Title H1 (Setext-style)</h1>';
        $data['separation'][113]['expected'].= '<p>';
        $data['separation'][113]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][113]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][113]['expected'].=   'paragraph line 3';
        $data['separation'][113]['expected'].= '</p>';

        ########################
        ### header near list ###
        ########################

        $data['separation'][120]['data'] = '# Title H1 (atx-style)'.NL;
        $data['separation'][120]['data'].= '* list item 1'.NL;
        $data['separation'][120]['data'].= '* list item 2'.NL;
        $data['separation'][120]['data'].= '* list item 3';
        $data['separation'][120]['expected'] = '<h1>Title H1 (atx-style)</h1>';
        $data['separation'][120]['expected'].= '<ul>';
        $data['separation'][120]['expected'].=   '<li>list item 1</li>';
        $data['separation'][120]['expected'].=   '<li>list item 2</li>';
        $data['separation'][120]['expected'].=   '<li>list item 3</li>';
        $data['separation'][120]['expected'].= '</ul>';

        $data['separation'][121]['data'] = '# Title H1 (atx-style)'.NL;
        $data['separation'][121]['data'].= NL;
        $data['separation'][121]['data'].= '* list item 1'.NL;
        $data['separation'][121]['data'].= '* list item 2'.NL;
        $data['separation'][121]['data'].= '* list item 3';
        $data['separation'][121]['expected'] = '<h1>Title H1 (atx-style)</h1>';
        $data['separation'][121]['expected'].= '<ul>';
        $data['separation'][121]['expected'].=   '<li>list item 1</li>';
        $data['separation'][121]['expected'].=   '<li>list item 2</li>';
        $data['separation'][121]['expected'].=   '<li>list item 3</li>';
        $data['separation'][121]['expected'].= '</ul>';

        $data['separation'][122]['data'] = 'Title H1 (Setext-style)'.NL;
        $data['separation'][122]['data'].= '======================='.NL;
        $data['separation'][122]['data'].= '* list item 1'.NL;
        $data['separation'][122]['data'].= '* list item 2'.NL;
        $data['separation'][122]['data'].= '* list item 3';
        $data['separation'][122]['expected'] = '<h1>Title H1 (Setext-style)</h1>';
        $data['separation'][122]['expected'].= '<ul>';
        $data['separation'][122]['expected'].=   '<li>list item 1</li>';
        $data['separation'][122]['expected'].=   '<li>list item 2</li>';
        $data['separation'][122]['expected'].=   '<li>list item 3</li>';
        $data['separation'][122]['expected'].= '</ul>';

        $data['separation'][123]['data'] = 'Title H1 (Setext-style)'.NL;
        $data['separation'][123]['data'].= '======================='.NL;
        $data['separation'][123]['data'].= NL;
        $data['separation'][123]['data'].= '* list item 1'.NL;
        $data['separation'][123]['data'].= '* list item 2'.NL;
        $data['separation'][123]['data'].= '* list item 3';
        $data['separation'][123]['expected'] = '<h1>Title H1 (Setext-style)</h1>';
        $data['separation'][123]['expected'].= '<ul>';
        $data['separation'][123]['expected'].=   '<li>list item 1</li>';
        $data['separation'][123]['expected'].=   '<li>list item 2</li>';
        $data['separation'][123]['expected'].=   '<li>list item 3</li>';
        $data['separation'][123]['expected'].= '</ul>';

        ########################
        ### header near code ###
        ########################

        $data['separation'][130]['data'] = '# Title H1 (atx-style)'.NL;
        $data['separation'][130]['data'].= '    code line 1'.NL;
        $data['separation'][130]['data'].= '         code line 2'.NL;
        $data['separation'][130]['data'].= '    code line 3';
        $data['separation'][130]['expected'] = '<h1>Title H1 (atx-style)</h1>';
        $data['separation'][130]['expected'].= '<pre>';
        $data['separation'][130]['expected'].=   '<code>';
        $data['separation'][130]['expected'].=     'code line 1'.NL;
        $data['separation'][130]['expected'].=       '     code line 2'.NL;
        $data['separation'][130]['expected'].=     'code line 3';
        $data['separation'][130]['expected'].=   '</code>';
        $data['separation'][130]['expected'].= '</pre>';

        $data['separation'][131]['data'] = '# Title H1 (atx-style)'.NL;
        $data['separation'][131]['data'].= NL;
        $data['separation'][131]['data'].= '    code line 1'.NL;
        $data['separation'][131]['data'].= '         code line 2'.NL;
        $data['separation'][131]['data'].= '    code line 3';
        $data['separation'][131]['expected'] = '<h1>Title H1 (atx-style)</h1>';
        $data['separation'][131]['expected'].= '<pre>';
        $data['separation'][131]['expected'].=   '<code>';
        $data['separation'][131]['expected'].=     'code line 1'.NL;
        $data['separation'][131]['expected'].=       '     code line 2'.NL;
        $data['separation'][131]['expected'].=     'code line 3';
        $data['separation'][131]['expected'].=   '</code>';
        $data['separation'][131]['expected'].= '</pre>';

        $data['separation'][132]['data'] = 'Title H1 (Setext-style)'.NL;
        $data['separation'][132]['data'].= '======================='.NL;
        $data['separation'][132]['data'].= '    code line 1'.NL;
        $data['separation'][132]['data'].= '         code line 2'.NL;
        $data['separation'][132]['data'].= '    code line 3';
        $data['separation'][132]['expected'] = '<h1>Title H1 (Setext-style)</h1>';
        $data['separation'][132]['expected'].= '<pre>';
        $data['separation'][132]['expected'].=   '<code>';
        $data['separation'][132]['expected'].=     'code line 1'.NL;
        $data['separation'][132]['expected'].=       '     code line 2'.NL;
        $data['separation'][132]['expected'].=     'code line 3';
        $data['separation'][132]['expected'].=   '</code>';
        $data['separation'][132]['expected'].= '</pre>';

        $data['separation'][133]['data'] = 'Title H1 (Setext-style)'.NL;
        $data['separation'][133]['data'].= '======================='.NL;
        $data['separation'][133]['data'].= NL;
        $data['separation'][133]['data'].= '    code line 1'.NL;
        $data['separation'][133]['data'].= '         code line 2'.NL;
        $data['separation'][133]['data'].= '    code line 3';
        $data['separation'][133]['expected'] = '<h1>Title H1 (Setext-style)</h1>';
        $data['separation'][133]['expected'].= '<pre>';
        $data['separation'][133]['expected'].=   '<code>';
        $data['separation'][133]['expected'].=     'code line 1'.NL;
        $data['separation'][133]['expected'].=       '     code line 2'.NL;
        $data['separation'][133]['expected'].=     'code line 3';
        $data['separation'][133]['expected'].=   '</code>';
        $data['separation'][133]['expected'].= '</pre>';

        ##############################
        ### header near blockquote ###
        ##############################

        $data['separation'][140]['data'] = '# Title H1 (atx-style)'.NL;
        $data['separation'][140]['data'].= '> blockquote line 1'.NL;
        $data['separation'][140]['data'].= '> blockquote line 2'.NL;
        $data['separation'][140]['data'].= '> blockquote line 3';
        $data['separation'][140]['expected'] = '<h1>Title H1 (atx-style)</h1>';
        $data['separation'][140]['expected'].= '<blockquote>';
        $data['separation'][140]['expected'].=   '<p>';
        $data['separation'][140]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][140]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][140]['expected'].=     'blockquote line 3';
        $data['separation'][140]['expected'].=   '</p>';
        $data['separation'][140]['expected'].= '</blockquote>';

        $data['separation'][141]['data'] = '# Title H1 (atx-style)'.NL;
        $data['separation'][141]['data'].= NL;
        $data['separation'][141]['data'].= '> blockquote line 1'.NL;
        $data['separation'][141]['data'].= '> blockquote line 2'.NL;
        $data['separation'][141]['data'].= '> blockquote line 3';
        $data['separation'][141]['expected'] = '<h1>Title H1 (atx-style)</h1>';
        $data['separation'][141]['expected'].= '<blockquote>';
        $data['separation'][141]['expected'].=   '<p>';
        $data['separation'][141]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][141]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][141]['expected'].=     'blockquote line 3';
        $data['separation'][141]['expected'].=   '</p>';
        $data['separation'][141]['expected'].= '</blockquote>';

        $data['separation'][142]['data'] = 'Title H1 (Setext-style)'.NL;
        $data['separation'][142]['data'].= '======================='.NL;
        $data['separation'][142]['data'].= '> blockquote line 1'.NL;
        $data['separation'][142]['data'].= '> blockquote line 2'.NL;
        $data['separation'][142]['data'].= '> blockquote line 3';
        $data['separation'][142]['expected'] = '<h1>Title H1 (Setext-style)</h1>';
        $data['separation'][142]['expected'].= '<blockquote>';
        $data['separation'][142]['expected'].=   '<p>';
        $data['separation'][142]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][142]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][142]['expected'].=     'blockquote line 3';
        $data['separation'][142]['expected'].=   '</p>';
        $data['separation'][142]['expected'].= '</blockquote>';

        $data['separation'][143]['data'] = 'Title H1 (Setext-style)'.NL;
        $data['separation'][143]['data'].= '======================='.NL;
        $data['separation'][143]['data'].= NL;
        $data['separation'][143]['data'].= '> blockquote line 1'.NL;
        $data['separation'][143]['data'].= '> blockquote line 2'.NL;
        $data['separation'][143]['data'].= '> blockquote line 3';
        $data['separation'][143]['expected'] = '<h1>Title H1 (Setext-style)</h1>';
        $data['separation'][143]['expected'].= '<blockquote>';
        $data['separation'][143]['expected'].=   '<p>';
        $data['separation'][143]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][143]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][143]['expected'].=     'blockquote line 3';
        $data['separation'][143]['expected'].=   '</p>';
        $data['separation'][143]['expected'].= '</blockquote>';

        ######################
        ### header near hr ###
        ######################

        $data['separation'][150]['data'] = '# Title H1 (atx-style)'.NL;
        $data['separation'][150]['data'].= '*  *  *';
        $data['separation'][150]['expected'] = '<h1>Title H1 (atx-style)</h1>';
        $data['separation'][150]['expected'].= '<hr>';

        $data['separation'][151]['data'] = '# Title H1 (atx-style)'.NL;
        $data['separation'][151]['data'].= '-  -  -';
        $data['separation'][151]['expected'] = '<h1>Title H1 (atx-style)</h1>';
        $data['separation'][151]['expected'].= '<hr>';

        $data['separation'][152]['data'] = '# Title H1 (atx-style)'.NL;
        $data['separation'][152]['data'].= '-------';
        $data['separation'][152]['expected'] = '<h1>Title H1 (atx-style)</h1>';
        $data['separation'][152]['expected'].= '<hr>';

        $data['separation'][153]['data'] = '# Title H1 (atx-style)'.NL;
        $data['separation'][153]['data'].= NL;
        $data['separation'][153]['data'].= '*  *  *';
        $data['separation'][153]['expected'] = '<h1>Title H1 (atx-style)</h1>';
        $data['separation'][153]['expected'].= '<hr>';

        $data['separation'][154]['data'] = '# Title H1 (atx-style)'.NL;
        $data['separation'][154]['data'].= NL;
        $data['separation'][154]['data'].= '-  -  -';
        $data['separation'][154]['expected'] = '<h1>Title H1 (atx-style)</h1>';
        $data['separation'][154]['expected'].= '<hr>';

        $data['separation'][155]['data'] = '# Title H1 (atx-style)'.NL;
        $data['separation'][155]['data'].= NL;
        $data['separation'][155]['data'].= '-------';
        $data['separation'][155]['expected'] = '<h1>Title H1 (atx-style)</h1>';
        $data['separation'][155]['expected'].= '<hr>';

        $data['separation'][156]['data'] = 'Title H1 (Setext-style)'.NL;
        $data['separation'][156]['data'].= '======================='.NL;
        $data['separation'][156]['data'].= '*  *  *';
        $data['separation'][156]['expected'] = '<h1>Title H1 (Setext-style)</h1>';
        $data['separation'][156]['expected'].= '<hr>';

        $data['separation'][157]['data'] = 'Title H1 (Setext-style)'.NL;
        $data['separation'][157]['data'].= '======================='.NL;
        $data['separation'][157]['data'].= '-  -  -';
        $data['separation'][157]['expected'] = '<h1>Title H1 (Setext-style)</h1>';
        $data['separation'][157]['expected'].= '<hr>';

        $data['separation'][158]['data'] = 'Title H1 (Setext-style)'.NL;
        $data['separation'][158]['data'].= '======================='.NL;
        $data['separation'][158]['data'].= '-------';
        $data['separation'][158]['expected'] = '<h1>Title H1 (Setext-style)</h1>';
        $data['separation'][158]['expected'].= '<hr>';

        $data['separation'][159]['data'] = 'Title H1 (Setext-style)'.NL;
        $data['separation'][159]['data'].= '======================='.NL;
        $data['separation'][159]['data'].= NL;
        $data['separation'][159]['data'].= '*  *  *';
        $data['separation'][159]['expected'] = '<h1>Title H1 (Setext-style)</h1>';
        $data['separation'][159]['expected'].= '<hr>';

        $data['separation'][160]['data'] = 'Title H1 (Setext-style)'.NL;
        $data['separation'][160]['data'].= '======================='.NL;
        $data['separation'][160]['data'].= NL;
        $data['separation'][160]['data'].= '-  -  -';
        $data['separation'][160]['expected'] = '<h1>Title H1 (Setext-style)</h1>';
        $data['separation'][160]['expected'].= '<hr>';

        $data['separation'][161]['data'] = 'Title H1 (Setext-style)'.NL;
        $data['separation'][161]['data'].= '======================='.NL;
        $data['separation'][161]['data'].= NL;
        $data['separation'][161]['data'].= '-------';
        $data['separation'][161]['expected'] = '<h1>Title H1 (Setext-style)</h1>';
        $data['separation'][161]['expected'].= '<hr>';

        ##########################
        ### header near markup ###
        ##########################

        $data['separation'][170]['data'] = '# Title H1 (atx-style)'.NL;
        $data['separation'][170]['data'].= '<div>'.NL;
        $data['separation'][170]['data'].= '  markup line 1'.NL;
        $data['separation'][170]['data'].= '  markup line 2'.NL;
        $data['separation'][170]['data'].= '  markup line 3'.NL;
        $data['separation'][170]['data'].= '</div>';
        $data['separation'][170]['expected'] = '<h1>Title H1 (atx-style)</h1>';
        $data['separation'][170]['expected'].= '<div>'.NL;
        $data['separation'][170]['expected'].= '  markup line 1'.NL;
        $data['separation'][170]['expected'].= '  markup line 2'.NL;
        $data['separation'][170]['expected'].= '  markup line 3'.NL;
        $data['separation'][170]['expected'].= '</div>';

        $data['separation'][171]['data'] = '# Title H1 (atx-style)'.NL;
        $data['separation'][171]['data'].= NL;
        $data['separation'][171]['data'].= '<div>'.NL;
        $data['separation'][171]['data'].= '  markup line 1'.NL;
        $data['separation'][171]['data'].= '  markup line 2'.NL;
        $data['separation'][171]['data'].= '  markup line 3'.NL;
        $data['separation'][171]['data'].= '</div>';
        $data['separation'][171]['expected'] = '<h1>Title H1 (atx-style)</h1>';
        $data['separation'][171]['expected'].= '<div>'.NL;
        $data['separation'][171]['expected'].= '  markup line 1'.NL;
        $data['separation'][171]['expected'].= '  markup line 2'.NL;
        $data['separation'][171]['expected'].= '  markup line 3'.NL;
        $data['separation'][171]['expected'].= '</div>';

        $data['separation'][172]['data'] = 'Title H1 (Setext-style)'.NL;
        $data['separation'][172]['data'].= '======================='.NL;
        $data['separation'][172]['data'].= '<div>'.NL;
        $data['separation'][172]['data'].= '  markup line 1'.NL;
        $data['separation'][172]['data'].= '  markup line 2'.NL;
        $data['separation'][172]['data'].= '  markup line 3'.NL;
        $data['separation'][172]['data'].= '</div>';
        $data['separation'][172]['expected'] = '<h1>Title H1 (Setext-style)</h1>';
        $data['separation'][172]['expected'].= '<div>'.NL;
        $data['separation'][172]['expected'].= '  markup line 1'.NL;
        $data['separation'][172]['expected'].= '  markup line 2'.NL;
        $data['separation'][172]['expected'].= '  markup line 3'.NL;
        $data['separation'][172]['expected'].= '</div>';

        $data['separation'][173]['data'] = 'Title H1 (Setext-style)'.NL;
        $data['separation'][173]['data'].= '======================='.NL;
        $data['separation'][173]['data'].= NL;
        $data['separation'][173]['data'].= '<div>'.NL;
        $data['separation'][173]['data'].= '  markup line 1'.NL;
        $data['separation'][173]['data'].= '  markup line 2'.NL;
        $data['separation'][173]['data'].= '  markup line 3'.NL;
        $data['separation'][173]['data'].= '</div>';
        $data['separation'][173]['expected'] = '<h1>Title H1 (Setext-style)</h1>';
        $data['separation'][173]['expected'].= '<div>'.NL;
        $data['separation'][173]['expected'].= '  markup line 1'.NL;
        $data['separation'][173]['expected'].= '  markup line 2'.NL;
        $data['separation'][173]['expected'].= '  markup line 3'.NL;
        $data['separation'][173]['expected'].= '</div>';

        #############################
        ### paragraph near header ###
        #############################

        $data['separation'][200]['data'] = 'paragraph line 1'.NL;
        $data['separation'][200]['data'].= 'paragraph line 2'.NL;
        $data['separation'][200]['data'].= 'paragraph line 3'.NL;
        $data['separation'][200]['data'].= '# Title H1 (atx-style)';
        $data['separation'][200]['expected'] = '<p>';
        $data['separation'][200]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][200]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][200]['expected'].=   'paragraph line 3';
        $data['separation'][200]['expected'].= '</p>';
        $data['separation'][200]['expected'].= '<h1>Title H1 (atx-style)</h1>';

        $data['separation'][201]['data'] = 'paragraph line 1'.NL;
        $data['separation'][201]['data'].= 'paragraph line 2'.NL;
        $data['separation'][201]['data'].= 'paragraph line 3'.NL;
        $data['separation'][201]['data'].= NL;
        $data['separation'][201]['data'].= '# Title H1 (atx-style)';
        $data['separation'][201]['expected'] = '<p>';
        $data['separation'][201]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][201]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][201]['expected'].=   'paragraph line 3';
        $data['separation'][201]['expected'].= '</p>';
        $data['separation'][201]['expected'].= '<h1>Title H1 (atx-style)</h1>';

        $data['separation'][202]['data'] = 'paragraph line 1'.NL;
        $data['separation'][202]['data'].= 'paragraph line 2'.NL;
        $data['separation'][202]['data'].= 'paragraph line 3'.NL;
        $data['separation'][202]['data'].= 'Title H1 (Setex-style)'.NL;
        $data['separation'][202]['data'].= '======================';
        $data['separation'][202]['expected'] = '<h1>';
        $data['separation'][202]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][202]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][202]['expected'].=   'paragraph line 3'.NL;
        $data['separation'][202]['expected'].=   'Title H1 (Setex-style)';
        $data['separation'][202]['expected'].= '</h1>';

        $data['separation'][203]['data'] = 'paragraph line 1'.NL;
        $data['separation'][203]['data'].= 'paragraph line 2'.NL;
        $data['separation'][203]['data'].= 'paragraph line 3'.NL;
        $data['separation'][203]['data'].= NL;
        $data['separation'][203]['data'].= 'Title H1 (Setex-style)'.NL;
        $data['separation'][203]['data'].= '======================';
        $data['separation'][203]['expected'] = '<p>';
        $data['separation'][203]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][203]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][203]['expected'].=   'paragraph line 3';
        $data['separation'][203]['expected'].= '</p>';
        $data['separation'][203]['expected'].= '<h1>Title H1 (Setex-style)</h1>';

        ################################
        ### paragraph near paragraph ###
        ################################

        $data['separation'][210]['data'] = 'paragraph 1 line 1'.NL;
        $data['separation'][210]['data'].= 'paragraph 1 line 2'.NL;
        $data['separation'][210]['data'].= 'paragraph 1 line 3'.NL;
        $data['separation'][210]['data'].= NL;
        $data['separation'][210]['data'].= 'paragraph 2 line 1'.NL;
        $data['separation'][210]['data'].= 'paragraph 2 line 2'.NL;
        $data['separation'][210]['data'].= 'paragraph 2 line 3';
        $data['separation'][210]['expected'] = '<p>';
        $data['separation'][210]['expected'].=   'paragraph 1 line 1'.NL;
        $data['separation'][210]['expected'].=   'paragraph 1 line 2'.NL;
        $data['separation'][210]['expected'].=   'paragraph 1 line 3';
        $data['separation'][210]['expected'].= '</p>';
        $data['separation'][210]['expected'].= '<p>';
        $data['separation'][210]['expected'].=   'paragraph 2 line 1'.NL;
        $data['separation'][210]['expected'].=   'paragraph 2 line 2'.NL;
        $data['separation'][210]['expected'].=   'paragraph 2 line 3';
        $data['separation'][210]['expected'].= '</p>';

        $data['separation'][211]['data'] = 'paragraph 1 line 1'.NL;
        $data['separation'][211]['data'].= 'paragraph 1 line 2'.NL;
        $data['separation'][211]['data'].= 'paragraph 1 line 3'.NL;
        $data['separation'][211]['data'].= NL.NL.NL;
        $data['separation'][211]['data'].= 'paragraph 2 line 1'.NL;
        $data['separation'][211]['data'].= 'paragraph 2 line 2'.NL;
        $data['separation'][211]['data'].= 'paragraph 2 line 3';
        $data['separation'][211]['expected'] = '<p>';
        $data['separation'][211]['expected'].=   'paragraph 1 line 1'.NL;
        $data['separation'][211]['expected'].=   'paragraph 1 line 2'.NL;
        $data['separation'][211]['expected'].=   'paragraph 1 line 3';
        $data['separation'][211]['expected'].= '</p>';
        $data['separation'][211]['expected'].= '<p>';
        $data['separation'][211]['expected'].=   'paragraph 2 line 1'.NL;
        $data['separation'][211]['expected'].=   'paragraph 2 line 2'.NL;
        $data['separation'][211]['expected'].=   'paragraph 2 line 3';
        $data['separation'][211]['expected'].= '</p>';

        ###########################
        ### paragraph near list ###
        ###########################

        $data['separation'][220]['data'] = 'paragraph line 1'.NL;
        $data['separation'][220]['data'].= 'paragraph line 2'.NL;
        $data['separation'][220]['data'].= 'paragraph line 3'.NL;
        $data['separation'][220]['data'].= '* list item 1'.NL;
        $data['separation'][220]['data'].= '* list item 2'.NL;
        $data['separation'][220]['data'].= '* list item 3';
        $data['separation'][220]['expected'] = '<p>';
        $data['separation'][220]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][220]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][220]['expected'].=   'paragraph line 3';
        $data['separation'][220]['expected'].= '</p>';
        $data['separation'][220]['expected'].= '<ul>';
        $data['separation'][220]['expected'].=   '<li>list item 1</li>';
        $data['separation'][220]['expected'].=   '<li>list item 2</li>';
        $data['separation'][220]['expected'].=   '<li>list item 3</li>';
        $data['separation'][220]['expected'].= '</ul>';

        $data['separation'][221]['data'] = 'paragraph line 1'.NL;
        $data['separation'][221]['data'].= 'paragraph line 2'.NL;
        $data['separation'][221]['data'].= 'paragraph line 3'.NL;
        $data['separation'][221]['data'].= NL;
        $data['separation'][221]['data'].= '* list item 1'.NL;
        $data['separation'][221]['data'].= '* list item 2'.NL;
        $data['separation'][221]['data'].= '* list item 3';
        $data['separation'][221]['expected'] = '<p>';
        $data['separation'][221]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][221]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][221]['expected'].=   'paragraph line 3';
        $data['separation'][221]['expected'].= '</p>';
        $data['separation'][221]['expected'].= '<ul>';
        $data['separation'][221]['expected'].=   '<li>list item 1</li>';
        $data['separation'][221]['expected'].=   '<li>list item 2</li>';
        $data['separation'][221]['expected'].=   '<li>list item 3</li>';
        $data['separation'][221]['expected'].= '</ul>';

        ###########################
        ### paragraph near code ###
        ###########################

        $data['separation'][230]['data'] = 'paragraph line 1'.NL;
        $data['separation'][230]['data'].= 'paragraph line 2'.NL;
        $data['separation'][230]['data'].= 'paragraph line 3'.NL;
        $data['separation'][230]['data'].= '    code line 1'.NL;
        $data['separation'][230]['data'].= '         code line 2'.NL;
        $data['separation'][230]['data'].= '    code line 3';
        $data['separation'][230]['expected'] = '<p>';
        $data['separation'][230]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][230]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][230]['expected'].=   'paragraph line 3'.NL;
        $data['separation'][230]['expected'].=   '    code line 1'.NL;
        $data['separation'][230]['expected'].=   '         code line 2'.NL;
        $data['separation'][230]['expected'].=   '    code line 3';
        $data['separation'][230]['expected'].= '</p>';

        $data['separation'][231]['data'] = 'paragraph line 1'.NL;
        $data['separation'][231]['data'].= 'paragraph line 2'.NL;
        $data['separation'][231]['data'].= 'paragraph line 3'.NL;
        $data['separation'][231]['data'].= NL;
        $data['separation'][231]['data'].= '    code line 1'.NL;
        $data['separation'][231]['data'].= '         code line 2'.NL;
        $data['separation'][231]['data'].= '    code line 3';
        $data['separation'][231]['expected'] = '<p>';
        $data['separation'][231]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][231]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][231]['expected'].=   'paragraph line 3';
        $data['separation'][231]['expected'].= '</p>';
        $data['separation'][231]['expected'].= '<pre>';
        $data['separation'][231]['expected'].=   '<code>';
        $data['separation'][231]['expected'].=     'code line 1'.NL;
        $data['separation'][231]['expected'].=       '     code line 2'.NL;
        $data['separation'][231]['expected'].=     'code line 3';
        $data['separation'][231]['expected'].=   '</code>';
        $data['separation'][231]['expected'].= '</pre>';

        #################################
        ### paragraph near blockquote ###
        #################################

        $data['separation'][240]['data'] = 'paragraph line 1'.NL;
        $data['separation'][240]['data'].= 'paragraph line 2'.NL;
        $data['separation'][240]['data'].= 'paragraph line 3'.NL;
        $data['separation'][240]['data'].= '> blockquote line 1'.NL;
        $data['separation'][240]['data'].= '> blockquote line 2'.NL;
        $data['separation'][240]['data'].= '> blockquote line 3';
        $data['separation'][240]['expected'] = '<p>';
        $data['separation'][240]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][240]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][240]['expected'].=   'paragraph line 3';
        $data['separation'][240]['expected'].= '</p>';
        $data['separation'][240]['expected'].= '<blockquote>';
        $data['separation'][240]['expected'].=   '<p>';
        $data['separation'][240]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][240]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][240]['expected'].=     'blockquote line 3';
        $data['separation'][240]['expected'].=   '</p>';
        $data['separation'][240]['expected'].= '</blockquote>';

        $data['separation'][241]['data'] = 'paragraph line 1'.NL;
        $data['separation'][241]['data'].= 'paragraph line 2'.NL;
        $data['separation'][241]['data'].= 'paragraph line 3'.NL;
        $data['separation'][241]['data'].= NL;
        $data['separation'][241]['data'].= '> blockquote line 1'.NL;
        $data['separation'][241]['data'].= '> blockquote line 2'.NL;
        $data['separation'][241]['data'].= '> blockquote line 3';
        $data['separation'][241]['expected'] = '<p>';
        $data['separation'][241]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][241]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][241]['expected'].=   'paragraph line 3';
        $data['separation'][241]['expected'].= '</p>';
        $data['separation'][241]['expected'].= '<blockquote>';
        $data['separation'][241]['expected'].=   '<p>';
        $data['separation'][241]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][241]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][241]['expected'].=     'blockquote line 3';
        $data['separation'][241]['expected'].=   '</p>';
        $data['separation'][241]['expected'].= '</blockquote>';

        #########################
        ### paragraph near hr ###
        #########################

        $data['separation'][250]['data'] = 'paragraph line 1'.NL;
        $data['separation'][250]['data'].= 'paragraph line 2'.NL;
        $data['separation'][250]['data'].= 'paragraph line 3'.NL;
        $data['separation'][250]['data'].= '*  *  *';
        $data['separation'][250]['expected'] = '<p>';
        $data['separation'][250]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][250]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][250]['expected'].=   'paragraph line 3';
        $data['separation'][250]['expected'].= '</p>';
        $data['separation'][250]['expected'].= '<hr>';

        $data['separation'][251]['data'] = 'paragraph line 1'.NL;
        $data['separation'][251]['data'].= 'paragraph line 2'.NL;
        $data['separation'][251]['data'].= 'paragraph line 3'.NL;
        $data['separation'][251]['data'].= '-  -  -';
        $data['separation'][251]['expected'] = '<p>';
        $data['separation'][251]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][251]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][251]['expected'].=   'paragraph line 3';
        $data['separation'][251]['expected'].= '</p>';
        $data['separation'][251]['expected'].= '<hr>';

        $data['separation'][252]['data'] = 'paragraph line 1'.NL;
        $data['separation'][252]['data'].= 'paragraph line 2'.NL;
        $data['separation'][252]['data'].= 'paragraph line 3'.NL;
        $data['separation'][252]['data'].= '-------';
        $data['separation'][252]['expected'] = '<h2>';
        $data['separation'][252]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][252]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][252]['expected'].=   'paragraph line 3';
        $data['separation'][252]['expected'].= '</h2>';

        $data['separation'][253]['data'] = 'paragraph line 1'.NL;
        $data['separation'][253]['data'].= 'paragraph line 2'.NL;
        $data['separation'][253]['data'].= 'paragraph line 3'.NL;
        $data['separation'][253]['data'].= NL;
        $data['separation'][253]['data'].= '*  *  *';
        $data['separation'][253]['expected'] = '<p>';
        $data['separation'][253]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][253]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][253]['expected'].=   'paragraph line 3';
        $data['separation'][253]['expected'].= '</p>';
        $data['separation'][253]['expected'].= '<hr>';

        $data['separation'][254]['data'] = 'paragraph line 1'.NL;
        $data['separation'][254]['data'].= 'paragraph line 2'.NL;
        $data['separation'][254]['data'].= 'paragraph line 3'.NL;
        $data['separation'][254]['data'].= NL;
        $data['separation'][254]['data'].= '-  -  -';
        $data['separation'][254]['expected'] = '<p>';
        $data['separation'][254]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][254]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][254]['expected'].=   'paragraph line 3';
        $data['separation'][254]['expected'].= '</p>';
        $data['separation'][254]['expected'].= '<hr>';

        $data['separation'][255]['data'] = 'paragraph line 1'.NL;
        $data['separation'][255]['data'].= 'paragraph line 2'.NL;
        $data['separation'][255]['data'].= 'paragraph line 3'.NL;
        $data['separation'][255]['data'].= NL;
        $data['separation'][255]['data'].= '-------';
        $data['separation'][255]['expected'] = '<p>';
        $data['separation'][255]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][255]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][255]['expected'].=   'paragraph line 3';
        $data['separation'][255]['expected'].= '</p>';
        $data['separation'][255]['expected'].= '<hr>';

        #############################
        ### paragraph near markup ###
        #############################

        $data['separation'][260]['data'] = 'paragraph line 1'.NL;
        $data['separation'][260]['data'].= 'paragraph line 2'.NL;
        $data['separation'][260]['data'].= 'paragraph line 3'.NL;
        $data['separation'][260]['data'].= '<div>'.NL;
        $data['separation'][260]['data'].= '  markup line 1'.NL;
        $data['separation'][260]['data'].= '  markup line 2'.NL;
        $data['separation'][260]['data'].= '  markup line 3'.NL;
        $data['separation'][260]['data'].= '</div>';
        $data['separation'][260]['expected'] = '<p>';
        $data['separation'][260]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][260]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][260]['expected'].=   'paragraph line 3';
        $data['separation'][260]['expected'].= '</p>';
        $data['separation'][260]['expected'].= '<div>'.NL;
        $data['separation'][260]['expected'].= '  markup line 1'.NL;
        $data['separation'][260]['expected'].= '  markup line 2'.NL;
        $data['separation'][260]['expected'].= '  markup line 3'.NL;
        $data['separation'][260]['expected'].= '</div>';

        $data['separation'][261]['data'] = 'paragraph line 1'.NL;
        $data['separation'][261]['data'].= 'paragraph line 2'.NL;
        $data['separation'][261]['data'].= 'paragraph line 3'.NL;
        $data['separation'][261]['data'].= NL;
        $data['separation'][261]['data'].= '<div>'.NL;
        $data['separation'][261]['data'].= '  markup line 1'.NL;
        $data['separation'][261]['data'].= '  markup line 2'.NL;
        $data['separation'][261]['data'].= '  markup line 3'.NL;
        $data['separation'][261]['data'].= '</div>';
        $data['separation'][261]['expected'] = '<p>';
        $data['separation'][261]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][261]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][261]['expected'].=   'paragraph line 3';
        $data['separation'][261]['expected'].= '</p>';
        $data['separation'][261]['expected'].= '<div>'.NL;
        $data['separation'][261]['expected'].= '  markup line 1'.NL;
        $data['separation'][261]['expected'].= '  markup line 2'.NL;
        $data['separation'][261]['expected'].= '  markup line 3'.NL;
        $data['separation'][261]['expected'].= '</div>';

        ########################
        ### list near header ###
        ########################

        $data['separation'][300]['data'] = '* list item 1'.NL;
        $data['separation'][300]['data'].= '* list item 2'.NL;
        $data['separation'][300]['data'].= '* list item 3'.NL;
        $data['separation'][300]['data'].= '# Title H1 (atx-style)';
        $data['separation'][300]['expected'] = '<ul>';
        $data['separation'][300]['expected'].=   '<li>list item 1</li>';
        $data['separation'][300]['expected'].=   '<li>list item 2</li>';
        $data['separation'][300]['expected'].=   '<li>list item 3</li>';
        $data['separation'][300]['expected'].= '</ul>';
        $data['separation'][300]['expected'].= '<h1>Title H1 (atx-style)</h1>';

        $data['separation'][301]['data'] = '* list item 1'.NL;
        $data['separation'][301]['data'].= '* list item 2'.NL;
        $data['separation'][301]['data'].= '* list item 3'.NL;
        $data['separation'][301]['data'].= NL;
        $data['separation'][301]['data'].= '# Title H1 (atx-style)';
        $data['separation'][301]['expected'] = '<ul>';
        $data['separation'][301]['expected'].=   '<li>list item 1</li>';
        $data['separation'][301]['expected'].=   '<li>list item 2</li>';
        $data['separation'][301]['expected'].=   '<li>list item 3</li>';
        $data['separation'][301]['expected'].= '</ul>';
        $data['separation'][301]['expected'].= '<h1>Title H1 (atx-style)</h1>';

        $data['separation'][302]['data'] = '* list item 1'.NL;
        $data['separation'][302]['data'].= '* list item 2'.NL;
        $data['separation'][302]['data'].= '* list item 3'.NL;
        $data['separation'][302]['data'].= 'Title H1 (Setex-style)'.NL;
        $data['separation'][302]['data'].= '======================';
        $data['separation'][302]['expected'] = '<ul>';
        $data['separation'][302]['expected'].=   '<li>list item 1</li>';
        $data['separation'][302]['expected'].=   '<li>list item 2</li>';
        $data['separation'][302]['expected'].=   '<li>list item 3';
        $data['separation'][302]['expected'].=     NL;
        $data['separation'][302]['expected'].=     'Title H1 (Setex-style)';
        $data['separation'][302]['expected'].=     NL;
        $data['separation'][302]['expected'].=     '======================';
        $data['separation'][302]['expected'].=   '</li>';
        $data['separation'][302]['expected'].= '</ul>';

        $data['separation'][303]['data'] = '* list item 1'.NL;
        $data['separation'][303]['data'].= '* list item 2'.NL;
        $data['separation'][303]['data'].= '* list item 3'.NL;
        $data['separation'][303]['data'].= NL;
        $data['separation'][303]['data'].= 'Title H1 (Setex-style)'.NL;
        $data['separation'][303]['data'].= '======================';
        $data['separation'][303]['expected'] = '<ul>';
        $data['separation'][303]['expected'].=   '<li>list item 1</li>';
        $data['separation'][303]['expected'].=   '<li>list item 2</li>';
        $data['separation'][303]['expected'].=   '<li>list item 3</li>';
        $data['separation'][303]['expected'].= '</ul>';
        $data['separation'][303]['expected'].= '<h1>Title H1 (Setex-style)</h1>';

        ###########################
        ### list near paragraph ###
        ###########################

        $data['separation'][310]['data'] = '* list item 1'.NL;
        $data['separation'][310]['data'].= '* list item 2'.NL;
        $data['separation'][310]['data'].= '* list item 3'.NL;
        $data['separation'][310]['data'].= 'paragraph line 1'.NL;
        $data['separation'][310]['data'].= 'paragraph line 2'.NL;
        $data['separation'][310]['data'].= 'paragraph line 3'.NL;
        $data['separation'][310]['expected'] = '<ul>';
        $data['separation'][310]['expected'].=   '<li>list item 1</li>';
        $data['separation'][310]['expected'].=   '<li>list item 2</li>';
        $data['separation'][310]['expected'].=   '<li>list item 3'.NL;
        $data['separation'][310]['expected'].=     'paragraph line 1'.NL;
        $data['separation'][310]['expected'].=     'paragraph line 2'.NL;
        $data['separation'][310]['expected'].=     'paragraph line 3</li>';
        $data['separation'][310]['expected'].= '</ul>';

        $data['separation'][311]['data'] = '* list item 1'.NL;
        $data['separation'][311]['data'].= '* list item 2'.NL;
        $data['separation'][311]['data'].= '* list item 3'.NL;
        $data['separation'][311]['data'].= NL;
        $data['separation'][311]['data'].= 'paragraph line 1'.NL;
        $data['separation'][311]['data'].= 'paragraph line 2'.NL;
        $data['separation'][311]['data'].= 'paragraph line 3'.NL;
        $data['separation'][311]['expected'] = '<ul>';
        $data['separation'][311]['expected'].=   '<li>list item 1</li>';
        $data['separation'][311]['expected'].=   '<li>list item 2</li>';
        $data['separation'][311]['expected'].=   '<li>list item 3</li>';
        $data['separation'][311]['expected'].= '</ul>';
        $data['separation'][311]['expected'].= '<p>';
        $data['separation'][311]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][311]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][311]['expected'].=   'paragraph line 3';
        $data['separation'][311]['expected'].= '</p>';

        ######################
        ### list near list ###
        ######################

        $data['separation'][320]['data'] = '* list item 1.1'.NL;
        $data['separation'][320]['data'].= '* list item 1.2'.NL;
        $data['separation'][320]['data'].= '* list item 1.3'.NL;
        $data['separation'][320]['data'].= '+ list item 2.1'.NL;
        $data['separation'][320]['data'].= '+ list item 2.2'.NL;
        $data['separation'][320]['data'].= '+ list item 2.3';
        $data['separation'][320]['expected'] = '<ul>';
        $data['separation'][320]['expected'].=   '<li>list item 1.1</li>';
        $data['separation'][320]['expected'].=   '<li>list item 1.2</li>';
        $data['separation'][320]['expected'].=   '<li>list item 1.3</li>';
        $data['separation'][320]['expected'].=   '<li>list item 2.1</li>';
        $data['separation'][320]['expected'].=   '<li>list item 2.2</li>';
        $data['separation'][320]['expected'].=   '<li>list item 2.3</li>';
        $data['separation'][320]['expected'].= '</ul>';

        $data['separation'][321]['data'] = '* list item 1.1'.NL;
        $data['separation'][321]['data'].= '* list item 1.2'.NL;
        $data['separation'][321]['data'].= '* list item 1.3'.NL;
        $data['separation'][321]['data'].= NL;
        $data['separation'][321]['data'].= '+ list item 2.1'.NL;
        $data['separation'][321]['data'].= '+ list item 2.2'.NL;
        $data['separation'][321]['data'].= '+ list item 2.3';
        $data['separation'][321]['expected'] = '<ul>';
        $data['separation'][321]['expected'].=   '<li>list item 1.1</li>';
        $data['separation'][321]['expected'].=   '<li>list item 1.2</li>';
        $data['separation'][321]['expected'].=   '<li>list item 1.3</li>';
        $data['separation'][321]['expected'].=   '<li>list item 2.1</li>';
        $data['separation'][321]['expected'].=   '<li>list item 2.2</li>';
        $data['separation'][321]['expected'].=   '<li>list item 2.3</li>';
        $data['separation'][321]['expected'].= '</ul>';

        ######################
        ### list near code ###
        ######################

        $data['separation'][330]['data'] = '* list item 1'.NL;
        $data['separation'][330]['data'].= '* list item 2'.NL;
        $data['separation'][330]['data'].= '* list item 3'.NL;
        $data['separation'][330]['data'].= '     code line 1'.NL;
        $data['separation'][330]['data'].= '           code line 2'.NL;
        $data['separation'][330]['data'].= '     code line 3';
        $data['separation'][330]['expected'] = '<ul>';
        $data['separation'][330]['expected'].=   '<li>list item 1</li>';
        $data['separation'][330]['expected'].=   '<li>list item 2</li>';
        $data['separation'][330]['expected'].=   '<li>list item 3'.NL;
        $data['separation'][330]['expected'].=     'code line 1'.NL;
        $data['separation'][330]['expected'].=     'code line 2'.NL;
        $data['separation'][330]['expected'].=     'code line 3</li>';
        $data['separation'][330]['expected'].= '</ul>';

        $data['separation'][331]['data'] = '* list item 1'.NL;
        $data['separation'][331]['data'].= '* list item 2'.NL;
        $data['separation'][331]['data'].= '* list item 3'.NL;
        $data['separation'][331]['data'].= NL;
        $data['separation'][331]['data'].= '     code line 1'.NL;
        $data['separation'][331]['data'].= '           code line 2'.NL;
        $data['separation'][331]['data'].= '     code line 3';
        $data['separation'][331]['expected'] = '<ul>';
        $data['separation'][331]['expected'].=   '<li>list item 1</li>';
        $data['separation'][331]['expected'].=   '<li>list item 2</li>';
        $data['separation'][331]['expected'].=   '<li>list item 3';
        $data['separation'][331]['expected'].=     '<p>';
        $data['separation'][331]['expected'].=       'code line 1'.NL;
        $data['separation'][331]['expected'].=       'code line 2'.NL;
        $data['separation'][331]['expected'].=       'code line 3';
        $data['separation'][331]['expected'].=     '</p>';
        $data['separation'][331]['expected'].=   '</li>';
        $data['separation'][331]['expected'].= '</ul>';

        ############################
        ### list near blockquote ###
        ############################

        $data['separation'][340]['data'] = '* list item 1'.NL;
        $data['separation'][340]['data'].= '* list item 2'.NL;
        $data['separation'][340]['data'].= '* list item 3'.NL;
        $data['separation'][340]['data'].= '> blockquote line 1'.NL;
        $data['separation'][340]['data'].= '> blockquote line 2'.NL;
        $data['separation'][340]['data'].= '> blockquote line 3';
        $data['separation'][340]['expected'] = '<ul>';
        $data['separation'][340]['expected'].=   '<li>list item 1</li>';
        $data['separation'][340]['expected'].=   '<li>list item 2</li>';
        $data['separation'][340]['expected'].=   '<li>list item 3</li>';
        $data['separation'][340]['expected'].= '</ul>';
        $data['separation'][340]['expected'].= '<blockquote>';
        $data['separation'][340]['expected'].=   '<p>';
        $data['separation'][340]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][340]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][340]['expected'].=     'blockquote line 3';
        $data['separation'][340]['expected'].=   '</p>';
        $data['separation'][340]['expected'].= '</blockquote>';

        $data['separation'][341]['data'] = '* list item 1'.NL;
        $data['separation'][341]['data'].= '* list item 2'.NL;
        $data['separation'][341]['data'].= '* list item 3'.NL;
        $data['separation'][341]['data'].= NL;
        $data['separation'][341]['data'].= '> blockquote line 1'.NL;
        $data['separation'][341]['data'].= '> blockquote line 2'.NL;
        $data['separation'][341]['data'].= '> blockquote line 3';
        $data['separation'][341]['expected'] = '<ul>';
        $data['separation'][341]['expected'].=   '<li>list item 1</li>';
        $data['separation'][341]['expected'].=   '<li>list item 2</li>';
        $data['separation'][341]['expected'].=   '<li>list item 3</li>';
        $data['separation'][341]['expected'].= '</ul>';
        $data['separation'][341]['expected'].= '<blockquote>';
        $data['separation'][341]['expected'].=   '<p>';
        $data['separation'][341]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][341]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][341]['expected'].=     'blockquote line 3';
        $data['separation'][341]['expected'].=   '</p>';
        $data['separation'][341]['expected'].= '</blockquote>';

        ####################
        ### list near hr ###
        ####################

        $data['separation'][350]['data'] = '* list item 1'.NL;
        $data['separation'][350]['data'].= '* list item 2'.NL;
        $data['separation'][350]['data'].= '* list item 3'.NL;
        $data['separation'][350]['data'].= '*  *  *';
        $data['separation'][350]['expected'] = '<ul>';
        $data['separation'][350]['expected'].=   '<li>list item 1</li>';
        $data['separation'][350]['expected'].=   '<li>list item 2</li>';
        $data['separation'][350]['expected'].=   '<li>list item 3</li>';
        $data['separation'][350]['expected'].= '</ul>';
        $data['separation'][350]['expected'].= '<hr>';

        $data['separation'][351]['data'] = '* list item 1'.NL;
        $data['separation'][351]['data'].= '* list item 2'.NL;
        $data['separation'][351]['data'].= '* list item 3'.NL;
        $data['separation'][351]['data'].= '-  -  -';
        $data['separation'][351]['expected'] = '<ul>';
        $data['separation'][351]['expected'].=   '<li>list item 1</li>';
        $data['separation'][351]['expected'].=   '<li>list item 2</li>';
        $data['separation'][351]['expected'].=   '<li>list item 3</li>';
        $data['separation'][351]['expected'].= '</ul>';
        $data['separation'][351]['expected'].= '<hr>';

        $data['separation'][352]['data'] = '* list item 1'.NL;
        $data['separation'][352]['data'].= '* list item 2'.NL;
        $data['separation'][352]['data'].= '* list item 3'.NL;
        $data['separation'][352]['data'].= '-------';
        $data['separation'][352]['expected'] = '<ul>';
        $data['separation'][352]['expected'].=   '<li>list item 1</li>';
        $data['separation'][352]['expected'].=   '<li>list item 2</li>';
        $data['separation'][352]['expected'].=   '<li>list item 3</li>';
        $data['separation'][352]['expected'].= '</ul>';
        $data['separation'][352]['expected'].= '<hr>';

        $data['separation'][353]['data'] = '* list item 1'.NL;
        $data['separation'][353]['data'].= '* list item 2'.NL;
        $data['separation'][353]['data'].= '* list item 3'.NL;
        $data['separation'][353]['data'].= NL;
        $data['separation'][353]['data'].= '*  *  *';
        $data['separation'][353]['expected'] = '<ul>';
        $data['separation'][353]['expected'].=   '<li>list item 1</li>';
        $data['separation'][353]['expected'].=   '<li>list item 2</li>';
        $data['separation'][353]['expected'].=   '<li>list item 3</li>';
        $data['separation'][353]['expected'].= '</ul>';
        $data['separation'][353]['expected'].= '<hr>';

        $data['separation'][354]['data'] = '* list item 1'.NL;
        $data['separation'][354]['data'].= '* list item 2'.NL;
        $data['separation'][354]['data'].= '* list item 3'.NL;
        $data['separation'][354]['data'].= NL;
        $data['separation'][354]['data'].= '-  -  -';
        $data['separation'][354]['expected'] = '<ul>';
        $data['separation'][354]['expected'].=   '<li>list item 1</li>';
        $data['separation'][354]['expected'].=   '<li>list item 2</li>';
        $data['separation'][354]['expected'].=   '<li>list item 3</li>';
        $data['separation'][354]['expected'].= '</ul>';
        $data['separation'][354]['expected'].= '<hr>';

        $data['separation'][355]['data'] = '* list item 1'.NL;
        $data['separation'][355]['data'].= '* list item 2'.NL;
        $data['separation'][355]['data'].= '* list item 3'.NL;
        $data['separation'][355]['data'].= NL;
        $data['separation'][355]['data'].= '-------';
        $data['separation'][355]['expected'] = '<ul>';
        $data['separation'][355]['expected'].=   '<li>list item 1</li>';
        $data['separation'][355]['expected'].=   '<li>list item 2</li>';
        $data['separation'][355]['expected'].=   '<li>list item 3</li>';
        $data['separation'][355]['expected'].= '</ul>';
        $data['separation'][355]['expected'].= '<hr>';

        ########################
        ### list near markup ###
        ########################

        $data['separation'][360]['data'] = '* list item 1'.NL;
        $data['separation'][360]['data'].= '* list item 2'.NL;
        $data['separation'][360]['data'].= '* list item 3'.NL;
        $data['separation'][360]['data'].= '<div>'.NL;
        $data['separation'][360]['data'].= '  markup line 1'.NL;
        $data['separation'][360]['data'].= '  markup line 2'.NL;
        $data['separation'][360]['data'].= '  markup line 3'.NL;
        $data['separation'][360]['data'].= '</div>';
        $data['separation'][360]['expected'] = '<ul>';
        $data['separation'][360]['expected'].=   '<li>list item 1</li>';
        $data['separation'][360]['expected'].=   '<li>list item 2</li>';
        $data['separation'][360]['expected'].=   '<li>list item 3</li>';
        $data['separation'][360]['expected'].= '</ul>';
        $data['separation'][360]['expected'].= '<div>'.NL;
        $data['separation'][360]['expected'].= '  markup line 1'.NL;
        $data['separation'][360]['expected'].= '  markup line 2'.NL;
        $data['separation'][360]['expected'].= '  markup line 3'.NL;
        $data['separation'][360]['expected'].= '</div>';

        $data['separation'][361]['data'] = '* list item 1'.NL;
        $data['separation'][361]['data'].= '* list item 2'.NL;
        $data['separation'][361]['data'].= '* list item 3'.NL;
        $data['separation'][361]['data'].= NL;
        $data['separation'][361]['data'].= '<div>'.NL;
        $data['separation'][361]['data'].= '  markup line 1'.NL;
        $data['separation'][361]['data'].= '  markup line 2'.NL;
        $data['separation'][361]['data'].= '  markup line 3'.NL;
        $data['separation'][361]['data'].= '</div>';
        $data['separation'][361]['expected'] = '<ul>';
        $data['separation'][361]['expected'].=   '<li>list item 1</li>';
        $data['separation'][361]['expected'].=   '<li>list item 2</li>';
        $data['separation'][361]['expected'].=   '<li>list item 3</li>';
        $data['separation'][361]['expected'].= '</ul>';
        $data['separation'][361]['expected'].= '<div>'.NL;
        $data['separation'][361]['expected'].= '  markup line 1'.NL;
        $data['separation'][361]['expected'].= '  markup line 2'.NL;
        $data['separation'][361]['expected'].= '  markup line 3'.NL;
        $data['separation'][361]['expected'].= '</div>';

        ########################
        ### code near header ###
        ########################

        $data['separation'][400]['data'] = '     code line 1'.NL;
        $data['separation'][400]['data'].= '          code line 2'.NL;
        $data['separation'][400]['data'].= '     code line 3'.NL;
        $data['separation'][400]['data'].= '# Title H1 (atx-style)';
        $data['separation'][400]['expected'] = '<pre>';
        $data['separation'][400]['expected'].=   '<code>';
        $data['separation'][400]['expected'].=     ' code line 1'.NL;
        $data['separation'][400]['expected'].=       '      code line 2'.NL;
        $data['separation'][400]['expected'].=     ' code line 3';
        $data['separation'][400]['expected'].=   '</code>';
        $data['separation'][400]['expected'].= '</pre>';
        $data['separation'][400]['expected'].= '<h1>Title H1 (atx-style)</h1>';

        $data['separation'][401]['data'] = '     code line 1'.NL;
        $data['separation'][401]['data'].= '          code line 2'.NL;
        $data['separation'][401]['data'].= '     code line 3'.NL;
        $data['separation'][401]['data'].= NL;
        $data['separation'][401]['data'].= '# Title H1 (atx-style)';
        $data['separation'][401]['expected'] = '<pre>';
        $data['separation'][401]['expected'].=   '<code>';
        $data['separation'][401]['expected'].=     ' code line 1'.NL;
        $data['separation'][401]['expected'].=       '      code line 2'.NL;
        $data['separation'][401]['expected'].=     ' code line 3';
        $data['separation'][401]['expected'].=   '</code>';
        $data['separation'][401]['expected'].= '</pre>';
        $data['separation'][401]['expected'].= '<h1>Title H1 (atx-style)</h1>';

        $data['separation'][402]['data'] = '     code line 1'.NL;
        $data['separation'][402]['data'].= '          code line 2'.NL;
        $data['separation'][402]['data'].= '     code line 3'.NL;
        $data['separation'][402]['data'].= 'Title H1 (Setex-style)'.NL;
        $data['separation'][402]['data'].= '======================';
        $data['separation'][402]['expected'] = '<pre>';
        $data['separation'][402]['expected'].=   '<code>';
        $data['separation'][402]['expected'].=     ' code line 1'.NL;
        $data['separation'][402]['expected'].=       '      code line 2'.NL;
        $data['separation'][402]['expected'].=     ' code line 3';
        $data['separation'][402]['expected'].=   '</code>';
        $data['separation'][402]['expected'].= '</pre>';
        $data['separation'][402]['expected'].= '<h1>Title H1 (Setex-style)</h1>';

        $data['separation'][403]['data'] = '     code line 1'.NL;
        $data['separation'][403]['data'].= '          code line 2'.NL;
        $data['separation'][403]['data'].= '     code line 3'.NL;
        $data['separation'][403]['data'].= NL;
        $data['separation'][403]['data'].= 'Title H1 (Setex-style)'.NL;
        $data['separation'][403]['data'].= '======================';
        $data['separation'][403]['expected'] = '<pre>';
        $data['separation'][403]['expected'].=   '<code>';
        $data['separation'][403]['expected'].=     ' code line 1'.NL;
        $data['separation'][403]['expected'].=       '      code line 2'.NL;
        $data['separation'][403]['expected'].=     ' code line 3';
        $data['separation'][403]['expected'].=   '</code>';
        $data['separation'][403]['expected'].= '</pre>';
        $data['separation'][403]['expected'].= '<h1>Title H1 (Setex-style)</h1>';

        ###########################
        ### code near paragraph ###
        ###########################

        $data['separation'][410]['data'] = '     code line 1'.NL;
        $data['separation'][410]['data'].= '          code line 2'.NL;
        $data['separation'][410]['data'].= '     code line 3'.NL;
        $data['separation'][410]['data'].= 'paragraph line 1'.NL;
        $data['separation'][410]['data'].= 'paragraph line 2'.NL;
        $data['separation'][410]['data'].= 'paragraph line 3';
        $data['separation'][410]['expected'] = '<pre>';
        $data['separation'][410]['expected'].=   '<code>';
        $data['separation'][410]['expected'].=     ' code line 1'.NL;
        $data['separation'][410]['expected'].=       '      code line 2'.NL;
        $data['separation'][410]['expected'].=     ' code line 3';
        $data['separation'][410]['expected'].=   '</code>';
        $data['separation'][410]['expected'].= '</pre>';
        $data['separation'][410]['expected'].= '<p>';
        $data['separation'][410]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][410]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][410]['expected'].=   'paragraph line 3';
        $data['separation'][410]['expected'].= '</p>';

        $data['separation'][411]['data'] = '     code line 1'.NL;
        $data['separation'][411]['data'].= '          code line 2'.NL;
        $data['separation'][411]['data'].= '     code line 3'.NL;
        $data['separation'][411]['data'].= NL;
        $data['separation'][411]['data'].= 'paragraph line 1'.NL;
        $data['separation'][411]['data'].= 'paragraph line 2'.NL;
        $data['separation'][411]['data'].= 'paragraph line 3';
        $data['separation'][411]['expected'] = '<pre>';
        $data['separation'][411]['expected'].=   '<code>';
        $data['separation'][411]['expected'].=     ' code line 1'.NL;
        $data['separation'][411]['expected'].=       '      code line 2'.NL;
        $data['separation'][411]['expected'].=     ' code line 3';
        $data['separation'][411]['expected'].=   '</code>';
        $data['separation'][411]['expected'].= '</pre>';
        $data['separation'][411]['expected'].= '<p>';
        $data['separation'][411]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][411]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][411]['expected'].=   'paragraph line 3';
        $data['separation'][411]['expected'].= '</p>';

        ######################
        ### code near list ###
        ######################

        $data['separation'][420]['data'] = '     code line 1'.NL;
        $data['separation'][420]['data'].= '          code line 2'.NL;
        $data['separation'][420]['data'].= '     code line 3'.NL;
        $data['separation'][420]['data'].= '* list item 1'.NL;
        $data['separation'][420]['data'].= '* list item 2'.NL;
        $data['separation'][420]['data'].= '* list item 3';
        $data['separation'][420]['expected'] = '<pre>';
        $data['separation'][420]['expected'].=   '<code>';
        $data['separation'][420]['expected'].=     ' code line 1'.NL;
        $data['separation'][420]['expected'].=       '      code line 2'.NL;
        $data['separation'][420]['expected'].=     ' code line 3';
        $data['separation'][420]['expected'].=   '</code>';
        $data['separation'][420]['expected'].= '</pre>';
        $data['separation'][420]['expected'].= '<ul>';
        $data['separation'][420]['expected'].=   '<li>list item 1</li>';
        $data['separation'][420]['expected'].=   '<li>list item 2</li>';
        $data['separation'][420]['expected'].=   '<li>list item 3</li>';
        $data['separation'][420]['expected'].= '</ul>';

        $data['separation'][421]['data'] = '     code line 1'.NL;
        $data['separation'][421]['data'].= '          code line 2'.NL;
        $data['separation'][421]['data'].= '     code line 3'.NL;
        $data['separation'][421]['data'].= NL;
        $data['separation'][421]['data'].= '* list item 1'.NL;
        $data['separation'][421]['data'].= '* list item 2'.NL;
        $data['separation'][421]['data'].= '* list item 3';
        $data['separation'][421]['expected'] = '<pre>';
        $data['separation'][421]['expected'].=   '<code>';
        $data['separation'][421]['expected'].=     ' code line 1'.NL;
        $data['separation'][421]['expected'].=       '      code line 2'.NL;
        $data['separation'][421]['expected'].=     ' code line 3';
        $data['separation'][421]['expected'].=   '</code>';
        $data['separation'][421]['expected'].= '</pre>';
        $data['separation'][421]['expected'].= '<ul>';
        $data['separation'][421]['expected'].=   '<li>list item 1</li>';
        $data['separation'][421]['expected'].=   '<li>list item 2</li>';
        $data['separation'][421]['expected'].=   '<li>list item 3</li>';
        $data['separation'][421]['expected'].= '</ul>';

        ######################
        ### code near code ###
        ######################

        $data['separation'][430]['data'] = '     code 1 line 1'.NL;
        $data['separation'][430]['data'].= '     code 1 line 2'.NL;
        $data['separation'][430]['data'].= '     code 1 line 3'.NL;
        $data['separation'][430]['data'].= NL.NL.NL;
        $data['separation'][430]['data'].= '     code 2 line 1'.NL;
        $data['separation'][430]['data'].= '     code 2 line 2'.NL;
        $data['separation'][430]['data'].= '     code 2 line 3';
        $data['separation'][430]['expected'] = '<pre>';
        $data['separation'][430]['expected'].=   '<code>';
        $data['separation'][430]['expected'].=     ' code 1 line 1'.NL;
        $data['separation'][430]['expected'].=     ' code 1 line 2'.NL;
        $data['separation'][430]['expected'].=     ' code 1 line 3'.NL;
        $data['separation'][430]['expected'].=     NL.NL.NL;
        $data['separation'][430]['expected'].=     ' code 2 line 1'.NL;
        $data['separation'][430]['expected'].=     ' code 2 line 2'.NL;
        $data['separation'][430]['expected'].=     ' code 2 line 3';
        $data['separation'][430]['expected'].=   '</code>';
        $data['separation'][430]['expected'].= '</pre>';

        ############################
        ### code near blockquote ###
        ############################

        $data['separation'][440]['data'] = '     code line 1'.NL;
        $data['separation'][440]['data'].= '          code line 2'.NL;
        $data['separation'][440]['data'].= '     code line 3'.NL;
        $data['separation'][440]['data'].= '> blockquote line 1'.NL;
        $data['separation'][440]['data'].= '> blockquote line 2'.NL;
        $data['separation'][440]['data'].= '> blockquote line 3';
        $data['separation'][440]['expected'] = '<pre>';
        $data['separation'][440]['expected'].=   '<code>';
        $data['separation'][440]['expected'].=     ' code line 1'.NL;
        $data['separation'][440]['expected'].=       '      code line 2'.NL;
        $data['separation'][440]['expected'].=     ' code line 3';
        $data['separation'][440]['expected'].=   '</code>';
        $data['separation'][440]['expected'].= '</pre>';
        $data['separation'][440]['expected'].= '<blockquote>';
        $data['separation'][440]['expected'].=   '<p>';
        $data['separation'][440]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][440]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][440]['expected'].=     'blockquote line 3';
        $data['separation'][440]['expected'].=   '</p>';
        $data['separation'][440]['expected'].= '</blockquote>';

        $data['separation'][441]['data'] = '     code line 1'.NL;
        $data['separation'][441]['data'].= '          code line 2'.NL;
        $data['separation'][441]['data'].= '     code line 3'.NL;
        $data['separation'][441]['data'].= NL;
        $data['separation'][441]['data'].= '> blockquote line 1'.NL;
        $data['separation'][441]['data'].= '> blockquote line 2'.NL;
        $data['separation'][441]['data'].= '> blockquote line 3';
        $data['separation'][441]['expected'] = '<pre>';
        $data['separation'][441]['expected'].=   '<code>';
        $data['separation'][441]['expected'].=     ' code line 1'.NL;
        $data['separation'][441]['expected'].=       '      code line 2'.NL;
        $data['separation'][441]['expected'].=     ' code line 3';
        $data['separation'][441]['expected'].=   '</code>';
        $data['separation'][441]['expected'].= '</pre>';
        $data['separation'][441]['expected'].= '<blockquote>';
        $data['separation'][441]['expected'].=   '<p>';
        $data['separation'][441]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][441]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][441]['expected'].=     'blockquote line 3';
        $data['separation'][441]['expected'].=   '</p>';
        $data['separation'][441]['expected'].= '</blockquote>';

        ####################
        ### code near hr ###
        ####################

        $data['separation'][450]['data'] = '     code line 1'.NL;
        $data['separation'][450]['data'].= '          code line 2'.NL;
        $data['separation'][450]['data'].= '     code line 3'.NL;
        $data['separation'][450]['data'].= '*  *  *';
        $data['separation'][450]['expected'] = '<pre>';
        $data['separation'][450]['expected'].=   '<code>';
        $data['separation'][450]['expected'].=     ' code line 1'.NL;
        $data['separation'][450]['expected'].=       '      code line 2'.NL;
        $data['separation'][450]['expected'].=     ' code line 3';
        $data['separation'][450]['expected'].=   '</code>';
        $data['separation'][450]['expected'].= '</pre>';
        $data['separation'][450]['expected'].= '<hr>';

        $data['separation'][451]['data'] = '     code line 1'.NL;
        $data['separation'][451]['data'].= '          code line 2'.NL;
        $data['separation'][451]['data'].= '     code line 3'.NL;
        $data['separation'][451]['data'].= '-  -  -';
        $data['separation'][451]['expected'] = '<pre>';
        $data['separation'][451]['expected'].=   '<code>';
        $data['separation'][451]['expected'].=     ' code line 1'.NL;
        $data['separation'][451]['expected'].=       '      code line 2'.NL;
        $data['separation'][451]['expected'].=     ' code line 3';
        $data['separation'][451]['expected'].=   '</code>';
        $data['separation'][451]['expected'].= '</pre>';
        $data['separation'][451]['expected'].= '<hr>';

        $data['separation'][452]['data'] = '     code line 1'.NL;
        $data['separation'][452]['data'].= '          code line 2'.NL;
        $data['separation'][452]['data'].= '     code line 3'.NL;
        $data['separation'][452]['data'].= '-------';
        $data['separation'][452]['expected'] = '<pre>';
        $data['separation'][452]['expected'].=   '<code>';
        $data['separation'][452]['expected'].=     ' code line 1'.NL;
        $data['separation'][452]['expected'].=       '      code line 2'.NL;
        $data['separation'][452]['expected'].=     ' code line 3';
        $data['separation'][452]['expected'].=   '</code>';
        $data['separation'][452]['expected'].= '</pre>';
        $data['separation'][452]['expected'].= '<hr>';

        $data['separation'][453]['data'] = '     code line 1'.NL;
        $data['separation'][453]['data'].= '          code line 2'.NL;
        $data['separation'][453]['data'].= '     code line 3'.NL;
        $data['separation'][453]['data'].= NL;
        $data['separation'][453]['data'].= '*  *  *';
        $data['separation'][453]['expected'] = '<pre>';
        $data['separation'][453]['expected'].=   '<code>';
        $data['separation'][453]['expected'].=     ' code line 1'.NL;
        $data['separation'][453]['expected'].=       '      code line 2'.NL;
        $data['separation'][453]['expected'].=     ' code line 3';
        $data['separation'][453]['expected'].=   '</code>';
        $data['separation'][453]['expected'].= '</pre>';
        $data['separation'][453]['expected'].= '<hr>';

        $data['separation'][454]['data'] = '     code line 1'.NL;
        $data['separation'][454]['data'].= '          code line 2'.NL;
        $data['separation'][454]['data'].= '     code line 3'.NL;
        $data['separation'][454]['data'].= NL;
        $data['separation'][454]['data'].= '-  -  -';
        $data['separation'][454]['expected'] = '<pre>';
        $data['separation'][454]['expected'].=   '<code>';
        $data['separation'][454]['expected'].=     ' code line 1'.NL;
        $data['separation'][454]['expected'].=       '      code line 2'.NL;
        $data['separation'][454]['expected'].=     ' code line 3';
        $data['separation'][454]['expected'].=   '</code>';
        $data['separation'][454]['expected'].= '</pre>';
        $data['separation'][454]['expected'].= '<hr>';

        $data['separation'][455]['data'] = '     code line 1'.NL;
        $data['separation'][455]['data'].= '          code line 2'.NL;
        $data['separation'][455]['data'].= '     code line 3'.NL;
        $data['separation'][455]['data'].= NL;
        $data['separation'][455]['data'].= '-------';
        $data['separation'][455]['expected'] = '<pre>';
        $data['separation'][455]['expected'].=   '<code>';
        $data['separation'][455]['expected'].=     ' code line 1'.NL;
        $data['separation'][455]['expected'].=       '      code line 2'.NL;
        $data['separation'][455]['expected'].=     ' code line 3';
        $data['separation'][455]['expected'].=   '</code>';
        $data['separation'][455]['expected'].= '</pre>';
        $data['separation'][455]['expected'].= '<hr>';

        ########################
        ### code near markup ###
        ########################

        $data['separation'][460]['data'] = '    code line 1'.NL;
        $data['separation'][460]['data'].= '         code line 2'.NL;
        $data['separation'][460]['data'].= '    code line 3'.NL;
        $data['separation'][460]['data'].= '<div>'.NL;
        $data['separation'][460]['data'].= '  markup line 1'.NL;
        $data['separation'][460]['data'].= '  markup line 2'.NL;
        $data['separation'][460]['data'].= '  markup line 3'.NL;
        $data['separation'][460]['data'].= '</div>';
        $data['separation'][460]['expected'] = '<pre>';
        $data['separation'][460]['expected'].=   '<code>';
        $data['separation'][460]['expected'].=     'code line 1'.NL;
        $data['separation'][460]['expected'].=       '     code line 2'.NL;
        $data['separation'][460]['expected'].=     'code line 3';
        $data['separation'][460]['expected'].=   '</code>';
        $data['separation'][460]['expected'].= '</pre>';
        $data['separation'][460]['expected'].= '<div>'.NL;
        $data['separation'][460]['expected'].= '  markup line 1'.NL;
        $data['separation'][460]['expected'].= '  markup line 2'.NL;
        $data['separation'][460]['expected'].= '  markup line 3'.NL;
        $data['separation'][460]['expected'].= '</div>';

        $data['separation'][461]['data'] = '    code line 1'.NL;
        $data['separation'][461]['data'].= '         code line 2'.NL;
        $data['separation'][461]['data'].= '    code line 3'.NL;
        $data['separation'][461]['data'].= NL;
        $data['separation'][461]['data'].= '<div>'.NL;
        $data['separation'][461]['data'].= '  markup line 1'.NL;
        $data['separation'][461]['data'].= '  markup line 2'.NL;
        $data['separation'][461]['data'].= '  markup line 3'.NL;
        $data['separation'][461]['data'].= '</div>';
        $data['separation'][461]['expected'] = '<pre>';
        $data['separation'][461]['expected'].=   '<code>';
        $data['separation'][461]['expected'].=     'code line 1'.NL;
        $data['separation'][461]['expected'].=       '     code line 2'.NL;
        $data['separation'][461]['expected'].=     'code line 3';
        $data['separation'][461]['expected'].=   '</code>';
        $data['separation'][461]['expected'].= '</pre>';
        $data['separation'][461]['expected'].= '<div>'.NL;
        $data['separation'][461]['expected'].= '  markup line 1'.NL;
        $data['separation'][461]['expected'].= '  markup line 2'.NL;
        $data['separation'][461]['expected'].= '  markup line 3'.NL;
        $data['separation'][461]['expected'].= '</div>';

        ##############################
        ### blockquote near header ###
        ##############################

        $data['separation'][500]['data'] = '> blockquote line 1'.NL;
        $data['separation'][500]['data'].= '> blockquote line 2'.NL;
        $data['separation'][500]['data'].= '> blockquote line 3'.NL;
        $data['separation'][500]['data'].= '# Title H1 (atx-style)';
        $data['separation'][500]['expected'] = '<blockquote>';
        $data['separation'][500]['expected'].=   '<p>';
        $data['separation'][500]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][500]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][500]['expected'].=     'blockquote line 3';
        $data['separation'][500]['expected'].=   '</p>';
        $data['separation'][500]['expected'].= '</blockquote>';
        $data['separation'][500]['expected'].= '<h1>Title H1 (atx-style)</h1>';

        $data['separation'][501]['data'] = '> blockquote line 1'.NL;
        $data['separation'][501]['data'].= '> blockquote line 2'.NL;
        $data['separation'][501]['data'].= '> blockquote line 3'.NL;
        $data['separation'][501]['data'].= NL;
        $data['separation'][501]['data'].= '# Title H1 (atx-style)';
        $data['separation'][501]['expected'] = '<blockquote>';
        $data['separation'][501]['expected'].=   '<p>';
        $data['separation'][501]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][501]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][501]['expected'].=     'blockquote line 3';
        $data['separation'][501]['expected'].=   '</p>';
        $data['separation'][501]['expected'].= '</blockquote>';
        $data['separation'][501]['expected'].= '<h1>Title H1 (atx-style)</h1>';

        $data['separation'][502]['data'] = '> blockquote line 1'.NL;
        $data['separation'][502]['data'].= '> blockquote line 2'.NL;
        $data['separation'][502]['data'].= '> blockquote line 3'.NL;
        $data['separation'][502]['data'].= 'Title H1 (Setext-style)'.NL;
        $data['separation'][502]['data'].= '=======================';
        $data['separation'][502]['expected'] = '<blockquote>';
        $data['separation'][502]['expected'].=   '<p>';
        $data['separation'][502]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][502]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][502]['expected'].=     'blockquote line 3'.NL;
        $data['separation'][502]['expected'].=     'Title H1 (Setext-style)'.NL;
        $data['separation'][502]['expected'].=     \effcore\markdown::meta_encode('=======================');
        $data['separation'][502]['expected'].=   '</p>';
        $data['separation'][502]['expected'].= '</blockquote>';

        $data['separation'][503]['data'] = '> blockquote line 1'.NL;
        $data['separation'][503]['data'].= '> blockquote line 2'.NL;
        $data['separation'][503]['data'].= '> blockquote line 3'.NL;
        $data['separation'][503]['data'].= NL;
        $data['separation'][503]['data'].= 'Title H1 (Setext-style)'.NL;
        $data['separation'][503]['data'].= '=======================';
        $data['separation'][503]['expected'] = '<blockquote>';
        $data['separation'][503]['expected'].=   '<p>';
        $data['separation'][503]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][503]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][503]['expected'].=     'blockquote line 3';
        $data['separation'][503]['expected'].=   '</p>';
        $data['separation'][503]['expected'].= '</blockquote>';
        $data['separation'][503]['expected'].= '<h1>Title H1 (Setext-style)</h1>';

        #################################
        ### blockquote near paragraph ###
        #################################

        $data['separation'][510]['data'] = '> blockquote line 1'.NL;
        $data['separation'][510]['data'].= '> blockquote line 2'.NL;
        $data['separation'][510]['data'].= '> blockquote line 3'.NL;
        $data['separation'][510]['data'].= 'paragraph line 1'.NL;
        $data['separation'][510]['data'].= 'paragraph line 2'.NL;
        $data['separation'][510]['data'].= 'paragraph line 3';
        $data['separation'][510]['expected'] = '<blockquote>';
        $data['separation'][510]['expected'].=   '<p>';
        $data['separation'][510]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][510]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][510]['expected'].=     'blockquote line 3'.NL;
        $data['separation'][510]['expected'].=     'paragraph line 1'.NL;
        $data['separation'][510]['expected'].=     'paragraph line 2'.NL;
        $data['separation'][510]['expected'].=     'paragraph line 3';
        $data['separation'][510]['expected'].=   '</p>';
        $data['separation'][510]['expected'].= '</blockquote>';

        $data['separation'][511]['data'] = '> blockquote line 1'.NL;
        $data['separation'][511]['data'].= '> blockquote line 2'.NL;
        $data['separation'][511]['data'].= '> blockquote line 3'.NL;
        $data['separation'][511]['data'].= NL;
        $data['separation'][511]['data'].= 'paragraph line 1'.NL;
        $data['separation'][511]['data'].= 'paragraph line 2'.NL;
        $data['separation'][511]['data'].= 'paragraph line 3';
        $data['separation'][511]['expected'] = '<blockquote>';
        $data['separation'][511]['expected'].=   '<p>';
        $data['separation'][511]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][511]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][511]['expected'].=     'blockquote line 3';
        $data['separation'][511]['expected'].=   '</p>';
        $data['separation'][511]['expected'].= '</blockquote>';
        $data['separation'][511]['expected'].= '<p>';
        $data['separation'][511]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][511]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][511]['expected'].=   'paragraph line 3';
        $data['separation'][511]['expected'].= '</p>';

        ############################
        ### blockquote near list ###
        ############################

        $data['separation'][520]['data'] = '> blockquote line 1'.NL;
        $data['separation'][520]['data'].= '> blockquote line 2'.NL;
        $data['separation'][520]['data'].= '> blockquote line 3'.NL;
        $data['separation'][520]['data'].= '* list item 1'.NL;
        $data['separation'][520]['data'].= '* list item 2'.NL;
        $data['separation'][520]['data'].= '* list item 3';
        $data['separation'][520]['expected'] = '<blockquote>';
        $data['separation'][520]['expected'].=   '<p>';
        $data['separation'][520]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][520]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][520]['expected'].=     'blockquote line 3';
        $data['separation'][520]['expected'].=   '</p>';
        $data['separation'][520]['expected'].= '</blockquote>';
        $data['separation'][520]['expected'].= '<ul>';
        $data['separation'][520]['expected'].=   '<li>list item 1</li>';
        $data['separation'][520]['expected'].=   '<li>list item 2</li>';
        $data['separation'][520]['expected'].=   '<li>list item 3</li>';
        $data['separation'][520]['expected'].= '</ul>';

        $data['separation'][521]['data'] = '> blockquote line 1'.NL;
        $data['separation'][521]['data'].= '> blockquote line 2'.NL;
        $data['separation'][521]['data'].= '> blockquote line 3'.NL;
        $data['separation'][521]['data'].= NL;
        $data['separation'][521]['data'].= '* list item 1'.NL;
        $data['separation'][521]['data'].= '* list item 2'.NL;
        $data['separation'][521]['data'].= '* list item 3';
        $data['separation'][521]['expected'] = '<blockquote>';
        $data['separation'][521]['expected'].=   '<p>';
        $data['separation'][521]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][521]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][521]['expected'].=     'blockquote line 3';
        $data['separation'][521]['expected'].=   '</p>';
        $data['separation'][521]['expected'].= '</blockquote>';
        $data['separation'][521]['expected'].= '<ul>';
        $data['separation'][521]['expected'].=   '<li>list item 1</li>';
        $data['separation'][521]['expected'].=   '<li>list item 2</li>';
        $data['separation'][521]['expected'].=   '<li>list item 3</li>';
        $data['separation'][521]['expected'].= '</ul>';

        ############################
        ### blockquote near code ###
        ############################

        $data['separation'][530]['data'] = '> blockquote line 1'.NL;
        $data['separation'][530]['data'].= '> blockquote line 2'.NL;
        $data['separation'][530]['data'].= '> blockquote line 3'.NL;
        $data['separation'][530]['data'].= '    code line 1'.NL;
        $data['separation'][530]['data'].= '         code line 2'.NL;
        $data['separation'][530]['data'].= '    code line 3';
        $data['separation'][530]['expected'] = '<blockquote>';
        $data['separation'][530]['expected'].=   '<p>';
        $data['separation'][530]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][530]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][530]['expected'].=     'blockquote line 3'.NL;
        $data['separation'][530]['expected'].=     '    code line 1'.NL;
        $data['separation'][530]['expected'].=     '         code line 2'.NL;
        $data['separation'][530]['expected'].=     '    code line 3';
        $data['separation'][530]['expected'].=   '</p>';
        $data['separation'][530]['expected'].= '</blockquote>';

        $data['separation'][531]['data'] = '> blockquote line 1'.NL;
        $data['separation'][531]['data'].= '> blockquote line 2'.NL;
        $data['separation'][531]['data'].= '> blockquote line 3'.NL;
        $data['separation'][531]['data'].= NL;
        $data['separation'][531]['data'].= '    code line 1'.NL;
        $data['separation'][531]['data'].= '         code line 2'.NL;
        $data['separation'][531]['data'].= '    code line 3';
        $data['separation'][531]['expected'] = '<blockquote>';
        $data['separation'][531]['expected'].=   '<p>';
        $data['separation'][531]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][531]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][531]['expected'].=     'blockquote line 3';
        $data['separation'][531]['expected'].=   '</p>';
        $data['separation'][531]['expected'].= '</blockquote>';
        $data['separation'][531]['expected'].= '<pre>';
        $data['separation'][531]['expected'].=   '<code>';
        $data['separation'][531]['expected'].=     'code line 1'.NL;
        $data['separation'][531]['expected'].=       '     code line 2'.NL;
        $data['separation'][531]['expected'].=     'code line 3';
        $data['separation'][531]['expected'].=   '</code>';
        $data['separation'][531]['expected'].= '</pre>';

        ##################################
        ### blockquote near blockquote ###
        ##################################

        $data['separation'][540]['data'] = '> blockquote 1 line 1'.NL;
        $data['separation'][540]['data'].= '> blockquote 1 line 2'.NL;
        $data['separation'][540]['data'].= '> blockquote 1 line 3'.NL;
        $data['separation'][540]['data'].= NL;
        $data['separation'][540]['data'].= '> blockquote 2 line 1'.NL;
        $data['separation'][540]['data'].= '> blockquote 2 line 2'.NL;
        $data['separation'][540]['data'].= '> blockquote 2 line 3';
        $data['separation'][540]['expected'] = '<blockquote>';
        $data['separation'][540]['expected'].=   '<p>';
        $data['separation'][540]['expected'].=     'blockquote 1 line 1'.NL;
        $data['separation'][540]['expected'].=     'blockquote 1 line 2'.NL;
        $data['separation'][540]['expected'].=     'blockquote 1 line 3';
        $data['separation'][540]['expected'].=   '</p>';
        $data['separation'][540]['expected'].= '</blockquote>';
        $data['separation'][540]['expected'].= '<blockquote>';
        $data['separation'][540]['expected'].=   '<p>';
        $data['separation'][540]['expected'].=     'blockquote 2 line 1'.NL;
        $data['separation'][540]['expected'].=     'blockquote 2 line 2'.NL;
        $data['separation'][540]['expected'].=     'blockquote 2 line 3';
        $data['separation'][540]['expected'].=   '</p>';
        $data['separation'][540]['expected'].= '</blockquote>';

        ##########################
        ### blockquote near hr ###
        ##########################

        $data['separation'][550]['data'] = '> blockquote line 1'.NL;
        $data['separation'][550]['data'].= '> blockquote line 2'.NL;
        $data['separation'][550]['data'].= '> blockquote line 3'.NL;
        $data['separation'][550]['data'].= '*  *  *';
        $data['separation'][550]['expected'] = '<blockquote>';
        $data['separation'][550]['expected'].=   '<p>';
        $data['separation'][550]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][550]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][550]['expected'].=     'blockquote line 3';
        $data['separation'][550]['expected'].=   '</p>';
        $data['separation'][550]['expected'].= '</blockquote>';
        $data['separation'][550]['expected'].= '<hr>';

        $data['separation'][551]['data'] = '> blockquote line 1'.NL;
        $data['separation'][551]['data'].= '> blockquote line 2'.NL;
        $data['separation'][551]['data'].= '> blockquote line 3'.NL;
        $data['separation'][551]['data'].= '-  -  -';
        $data['separation'][551]['expected'] = '<blockquote>';
        $data['separation'][551]['expected'].=   '<p>';
        $data['separation'][551]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][551]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][551]['expected'].=     'blockquote line 3';
        $data['separation'][551]['expected'].=   '</p>';
        $data['separation'][551]['expected'].= '</blockquote>';
        $data['separation'][551]['expected'].= '<hr>';

        $data['separation'][552]['data'] = '> blockquote line 1'.NL;
        $data['separation'][552]['data'].= '> blockquote line 2'.NL;
        $data['separation'][552]['data'].= '> blockquote line 3'.NL;
        $data['separation'][552]['data'].= '-------';
        $data['separation'][552]['expected'] = '<blockquote>';
        $data['separation'][552]['expected'].=   '<p>';
        $data['separation'][552]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][552]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][552]['expected'].=     'blockquote line 3';
        $data['separation'][552]['expected'].=   '</p>';
        $data['separation'][552]['expected'].= '</blockquote>';
        $data['separation'][552]['expected'].= '<hr>';

        $data['separation'][553]['data'] = '> blockquote line 1'.NL;
        $data['separation'][553]['data'].= '> blockquote line 2'.NL;
        $data['separation'][553]['data'].= '> blockquote line 3'.NL;
        $data['separation'][553]['data'].= NL;
        $data['separation'][553]['data'].= '*  *  *';
        $data['separation'][553]['expected'] = '<blockquote>';
        $data['separation'][553]['expected'].=   '<p>';
        $data['separation'][553]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][553]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][553]['expected'].=     'blockquote line 3';
        $data['separation'][553]['expected'].=   '</p>';
        $data['separation'][553]['expected'].= '</blockquote>';
        $data['separation'][553]['expected'].= '<hr>';

        $data['separation'][554]['data'] = '> blockquote line 1'.NL;
        $data['separation'][554]['data'].= '> blockquote line 2'.NL;
        $data['separation'][554]['data'].= '> blockquote line 3'.NL;
        $data['separation'][554]['data'].= NL;
        $data['separation'][554]['data'].= '-  -  -';
        $data['separation'][554]['expected'] = '<blockquote>';
        $data['separation'][554]['expected'].=   '<p>';
        $data['separation'][554]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][554]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][554]['expected'].=     'blockquote line 3';
        $data['separation'][554]['expected'].=   '</p>';
        $data['separation'][554]['expected'].= '</blockquote>';
        $data['separation'][554]['expected'].= '<hr>';

        $data['separation'][555]['data'] = '> blockquote line 1'.NL;
        $data['separation'][555]['data'].= '> blockquote line 2'.NL;
        $data['separation'][555]['data'].= '> blockquote line 3'.NL;
        $data['separation'][555]['data'].= NL;
        $data['separation'][555]['data'].= '-------';
        $data['separation'][555]['expected'] = '<blockquote>';
        $data['separation'][555]['expected'].=   '<p>';
        $data['separation'][555]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][555]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][555]['expected'].=     'blockquote line 3';
        $data['separation'][555]['expected'].=   '</p>';
        $data['separation'][555]['expected'].= '</blockquote>';
        $data['separation'][555]['expected'].= '<hr>';

        ##############################
        ### blockquote near markup ###
        ##############################

        $data['separation'][560]['data'] = '> blockquote line 1'.NL;
        $data['separation'][560]['data'].= '> blockquote line 2'.NL;
        $data['separation'][560]['data'].= '> blockquote line 3'.NL;
        $data['separation'][560]['data'].= '<div>'.NL;
        $data['separation'][560]['data'].= '  markup line 1'.NL;
        $data['separation'][560]['data'].= '  markup line 2'.NL;
        $data['separation'][560]['data'].= '  markup line 3'.NL;
        $data['separation'][560]['data'].= '</div>';
        $data['separation'][560]['expected'] = '<blockquote>';
        $data['separation'][560]['expected'].=   '<p>';
        $data['separation'][560]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][560]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][560]['expected'].=     'blockquote line 3';
        $data['separation'][560]['expected'].=   '</p>';
        $data['separation'][560]['expected'].= '</blockquote>';
        $data['separation'][560]['expected'].= '<div>'.NL;
        $data['separation'][560]['expected'].= '  markup line 1'.NL;
        $data['separation'][560]['expected'].= '  markup line 2'.NL;
        $data['separation'][560]['expected'].= '  markup line 3'.NL;
        $data['separation'][560]['expected'].= '</div>';

        $data['separation'][561]['data'] = '> blockquote line 1'.NL;
        $data['separation'][561]['data'].= '> blockquote line 2'.NL;
        $data['separation'][561]['data'].= '> blockquote line 3'.NL;
        $data['separation'][561]['data'].= NL;
        $data['separation'][561]['data'].= '<div>'.NL;
        $data['separation'][561]['data'].= '  markup line 1'.NL;
        $data['separation'][561]['data'].= '  markup line 2'.NL;
        $data['separation'][561]['data'].= '  markup line 3'.NL;
        $data['separation'][561]['data'].= '</div>';
        $data['separation'][561]['expected'] = '<blockquote>';
        $data['separation'][561]['expected'].=   '<p>';
        $data['separation'][561]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][561]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][561]['expected'].=     'blockquote line 3';
        $data['separation'][561]['expected'].=   '</p>';
        $data['separation'][561]['expected'].= '</blockquote>';
        $data['separation'][561]['expected'].= '<div>'.NL;
        $data['separation'][561]['expected'].= '  markup line 1'.NL;
        $data['separation'][561]['expected'].= '  markup line 2'.NL;
        $data['separation'][561]['expected'].= '  markup line 3'.NL;
        $data['separation'][561]['expected'].= '</div>';

        ######################
        ### hr near header ###
        ######################

        $data['separation'][600]['data'] = '*  *  *'.NL;
        $data['separation'][600]['data'].= '# Title H1 (atx-style)';
        $data['separation'][600]['expected'] = '<hr>';
        $data['separation'][600]['expected'].= '<h1>Title H1 (atx-style)</h1>';

        $data['separation'][601]['data'] = '-  -  -'.NL;
        $data['separation'][601]['data'].= '# Title H1 (atx-style)';
        $data['separation'][601]['expected'] = '<hr>';
        $data['separation'][601]['expected'].= '<h1>Title H1 (atx-style)</h1>';

        $data['separation'][602]['data'] = '-------'.NL;
        $data['separation'][602]['data'].= '# Title H1 (atx-style)';
        $data['separation'][602]['expected'] = '<hr>';
        $data['separation'][602]['expected'].= '<h1>Title H1 (atx-style)</h1>';

        $data['separation'][603]['data'] = '*  *  *'.NL;
        $data['separation'][603]['data'].= NL;
        $data['separation'][603]['data'].= '# Title H1 (atx-style)';
        $data['separation'][603]['expected'] = '<hr>';
        $data['separation'][603]['expected'].= '<h1>Title H1 (atx-style)</h1>';

        $data['separation'][604]['data'] = '-  -  -'.NL;
        $data['separation'][604]['data'].= NL;
        $data['separation'][604]['data'].= '# Title H1 (atx-style)';
        $data['separation'][604]['expected'] = '<hr>';
        $data['separation'][604]['expected'].= '<h1>Title H1 (atx-style)</h1>';

        $data['separation'][605]['data'] = '-------'.NL;
        $data['separation'][605]['data'].= NL;
        $data['separation'][605]['data'].= '# Title H1 (atx-style)';
        $data['separation'][605]['expected'] = '<hr>';
        $data['separation'][605]['expected'].= '<h1>Title H1 (atx-style)</h1>';

        $data['separation'][606]['data'] = '*  *  *'.NL;
        $data['separation'][606]['data'].= 'Title H1 (Setex-style)'.NL;
        $data['separation'][606]['data'].= '======================';
        $data['separation'][606]['expected'] = '<hr>';
        $data['separation'][606]['expected'].= '<h1>Title H1 (Setex-style)</h1>';

        $data['separation'][607]['data'] = '-  -  -'.NL;
        $data['separation'][607]['data'].= 'Title H1 (Setex-style)'.NL;
        $data['separation'][607]['data'].= '======================';
        $data['separation'][607]['expected'] = '<hr>';
        $data['separation'][607]['expected'].= '<h1>Title H1 (Setex-style)</h1>';

        $data['separation'][608]['data'] = '--------'.NL;
        $data['separation'][608]['data'].= 'Title H1 (Setex-style)'.NL;
        $data['separation'][608]['data'].= '======================';
        $data['separation'][608]['expected'] = '<hr>';
        $data['separation'][608]['expected'].= '<h1>Title H1 (Setex-style)</h1>';

        $data['separation'][609]['data'] = '*  *  *'.NL;
        $data['separation'][609]['data'].= NL;
        $data['separation'][609]['data'].= 'Title H1 (Setex-style)'.NL;
        $data['separation'][609]['data'].= '======================';
        $data['separation'][609]['expected'] = '<hr>';
        $data['separation'][609]['expected'].= '<h1>Title H1 (Setex-style)</h1>';

        $data['separation'][610]['data'] = '-  -  -'.NL;
        $data['separation'][610]['data'].= NL;
        $data['separation'][610]['data'].= 'Title H1 (Setex-style)'.NL;
        $data['separation'][610]['data'].= '======================';
        $data['separation'][610]['expected'] = '<hr>';
        $data['separation'][610]['expected'].= '<h1>Title H1 (Setex-style)</h1>';

        $data['separation'][611]['data'] = '-------'.NL;
        $data['separation'][611]['data'].= NL;
        $data['separation'][611]['data'].= 'Title H1 (Setex-style)'.NL;
        $data['separation'][611]['data'].= '======================';
        $data['separation'][611]['expected'] = '<hr>';
        $data['separation'][611]['expected'].= '<h1>Title H1 (Setex-style)</h1>';

        #########################
        ### hr near paragraph ###
        #########################

        $data['separation'][620]['data'] = '*  *  *'.NL;
        $data['separation'][620]['data'].= 'paragraph line 1'.NL;
        $data['separation'][620]['data'].= 'paragraph line 2'.NL;
        $data['separation'][620]['data'].= 'paragraph line 3';
        $data['separation'][620]['expected'] = '<hr>';
        $data['separation'][620]['expected'].= '<p>';
        $data['separation'][620]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][620]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][620]['expected'].=   'paragraph line 3';
        $data['separation'][620]['expected'].= '</p>';

        $data['separation'][621]['data'] = '-  -  -'.NL;
        $data['separation'][621]['data'].= 'paragraph line 1'.NL;
        $data['separation'][621]['data'].= 'paragraph line 2'.NL;
        $data['separation'][621]['data'].= 'paragraph line 3';
        $data['separation'][621]['expected'] = '<hr>';
        $data['separation'][621]['expected'].= '<p>';
        $data['separation'][621]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][621]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][621]['expected'].=   'paragraph line 3';
        $data['separation'][621]['expected'].= '</p>';

        $data['separation'][622]['data'] = '-------'.NL;
        $data['separation'][622]['data'].= 'paragraph line 1'.NL;
        $data['separation'][622]['data'].= 'paragraph line 2'.NL;
        $data['separation'][622]['data'].= 'paragraph line 3';
        $data['separation'][622]['expected'] = '<hr>';
        $data['separation'][622]['expected'].= '<p>';
        $data['separation'][622]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][622]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][622]['expected'].=   'paragraph line 3';
        $data['separation'][622]['expected'].= '</p>';

        $data['separation'][623]['data'] = '*  *  *'.NL;
        $data['separation'][623]['data'].= NL;
        $data['separation'][623]['data'].= 'paragraph line 1'.NL;
        $data['separation'][623]['data'].= 'paragraph line 2'.NL;
        $data['separation'][623]['data'].= 'paragraph line 3';
        $data['separation'][623]['expected'] = '<hr>';
        $data['separation'][623]['expected'].= '<p>';
        $data['separation'][623]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][623]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][623]['expected'].=   'paragraph line 3';
        $data['separation'][623]['expected'].= '</p>';

        $data['separation'][624]['data'] = '-  -  -'.NL;
        $data['separation'][624]['data'].= NL;
        $data['separation'][624]['data'].= 'paragraph line 1'.NL;
        $data['separation'][624]['data'].= 'paragraph line 2'.NL;
        $data['separation'][624]['data'].= 'paragraph line 3';
        $data['separation'][624]['expected'] = '<hr>';
        $data['separation'][624]['expected'].= '<p>';
        $data['separation'][624]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][624]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][624]['expected'].=   'paragraph line 3';
        $data['separation'][624]['expected'].= '</p>';

        $data['separation'][625]['data'] = '-------'.NL;
        $data['separation'][625]['data'].= NL;
        $data['separation'][625]['data'].= 'paragraph line 1'.NL;
        $data['separation'][625]['data'].= 'paragraph line 2'.NL;
        $data['separation'][625]['data'].= 'paragraph line 3';
        $data['separation'][625]['expected'] = '<hr>';
        $data['separation'][625]['expected'].= '<p>';
        $data['separation'][625]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][625]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][625]['expected'].=   'paragraph line 3';
        $data['separation'][625]['expected'].= '</p>';

        ####################
        ### hr near list ###
        ####################

        $data['separation'][630]['data'] = '*  *  *'.NL;
        $data['separation'][630]['data'].= '* list item 1'.NL;
        $data['separation'][630]['data'].= '* list item 1'.NL;
        $data['separation'][630]['data'].= '* list item 1';
        $data['separation'][630]['expected'] = '<hr>';
        $data['separation'][630]['expected'].= '<ul>';
        $data['separation'][630]['expected'].=   '<li>list item 1</li>';
        $data['separation'][630]['expected'].=   '<li>list item 1</li>';
        $data['separation'][630]['expected'].=   '<li>list item 1</li>';
        $data['separation'][630]['expected'].= '</ul>';

        $data['separation'][631]['data'] = '-  -  -'.NL;
        $data['separation'][631]['data'].= '* list item 1'.NL;
        $data['separation'][631]['data'].= '* list item 1'.NL;
        $data['separation'][631]['data'].= '* list item 1';
        $data['separation'][631]['expected'] = '<hr>';
        $data['separation'][631]['expected'].= '<ul>';
        $data['separation'][631]['expected'].=   '<li>list item 1</li>';
        $data['separation'][631]['expected'].=   '<li>list item 1</li>';
        $data['separation'][631]['expected'].=   '<li>list item 1</li>';
        $data['separation'][631]['expected'].= '</ul>';

        $data['separation'][632]['data'] = '-------'.NL;
        $data['separation'][632]['data'].= '* list item 1'.NL;
        $data['separation'][632]['data'].= '* list item 1'.NL;
        $data['separation'][632]['data'].= '* list item 1';
        $data['separation'][632]['expected'] = '<hr>';
        $data['separation'][632]['expected'].= '<ul>';
        $data['separation'][632]['expected'].=   '<li>list item 1</li>';
        $data['separation'][632]['expected'].=   '<li>list item 1</li>';
        $data['separation'][632]['expected'].=   '<li>list item 1</li>';
        $data['separation'][632]['expected'].= '</ul>';

        $data['separation'][633]['data'] = '*  *  *'.NL;
        $data['separation'][633]['data'].= NL;
        $data['separation'][633]['data'].= '* list item 1'.NL;
        $data['separation'][633]['data'].= '* list item 1'.NL;
        $data['separation'][633]['data'].= '* list item 1';
        $data['separation'][633]['expected'] = '<hr>';
        $data['separation'][633]['expected'].= '<ul>';
        $data['separation'][633]['expected'].=   '<li>list item 1</li>';
        $data['separation'][633]['expected'].=   '<li>list item 1</li>';
        $data['separation'][633]['expected'].=   '<li>list item 1</li>';
        $data['separation'][633]['expected'].= '</ul>';

        $data['separation'][634]['data'] = '-  -  -'.NL;
        $data['separation'][634]['data'].= NL;
        $data['separation'][634]['data'].= '* list item 1'.NL;
        $data['separation'][634]['data'].= '* list item 1'.NL;
        $data['separation'][634]['data'].= '* list item 1';
        $data['separation'][634]['expected'] = '<hr>';
        $data['separation'][634]['expected'].= '<ul>';
        $data['separation'][634]['expected'].=   '<li>list item 1</li>';
        $data['separation'][634]['expected'].=   '<li>list item 1</li>';
        $data['separation'][634]['expected'].=   '<li>list item 1</li>';
        $data['separation'][634]['expected'].= '</ul>';

        $data['separation'][635]['data'] = '-------'.NL;
        $data['separation'][635]['data'].= NL;
        $data['separation'][635]['data'].= '* list item 1'.NL;
        $data['separation'][635]['data'].= '* list item 1'.NL;
        $data['separation'][635]['data'].= '* list item 1';
        $data['separation'][635]['expected'] = '<hr>';
        $data['separation'][635]['expected'].= '<ul>';
        $data['separation'][635]['expected'].=   '<li>list item 1</li>';
        $data['separation'][635]['expected'].=   '<li>list item 1</li>';
        $data['separation'][635]['expected'].=   '<li>list item 1</li>';
        $data['separation'][635]['expected'].= '</ul>';

        ####################
        ### hr near code ###
        ####################

        $data['separation'][640]['data'] = '*  *  *'.NL;
        $data['separation'][640]['data'].= '    code line 1'.NL;
        $data['separation'][640]['data'].= '         code line 2'.NL;
        $data['separation'][640]['data'].= '    code line 3';
        $data['separation'][640]['expected'] = '<hr>';
        $data['separation'][640]['expected'].= '<pre>';
        $data['separation'][640]['expected'].=   '<code>';
        $data['separation'][640]['expected'].=     'code line 1'.NL;
        $data['separation'][640]['expected'].=       '     code line 2'.NL;
        $data['separation'][640]['expected'].=     'code line 3';
        $data['separation'][640]['expected'].=   '</code>';
        $data['separation'][640]['expected'].= '</pre>';

        $data['separation'][641]['data'] = '-  -  -'.NL;
        $data['separation'][641]['data'].= '    code line 1'.NL;
        $data['separation'][641]['data'].= '         code line 2'.NL;
        $data['separation'][641]['data'].= '    code line 3';
        $data['separation'][641]['expected'] = '<hr>';
        $data['separation'][641]['expected'].= '<pre>';
        $data['separation'][641]['expected'].=   '<code>';
        $data['separation'][641]['expected'].=     'code line 1'.NL;
        $data['separation'][641]['expected'].=       '     code line 2'.NL;
        $data['separation'][641]['expected'].=     'code line 3';
        $data['separation'][641]['expected'].=   '</code>';
        $data['separation'][641]['expected'].= '</pre>';

        $data['separation'][642]['data'] = '-------'.NL;
        $data['separation'][642]['data'].= '    code line 1'.NL;
        $data['separation'][642]['data'].= '         code line 2'.NL;
        $data['separation'][642]['data'].= '    code line 3';
        $data['separation'][642]['expected'] = '<hr>';
        $data['separation'][642]['expected'].= '<pre>';
        $data['separation'][642]['expected'].=   '<code>';
        $data['separation'][642]['expected'].=     'code line 1'.NL;
        $data['separation'][642]['expected'].=       '     code line 2'.NL;
        $data['separation'][642]['expected'].=     'code line 3';
        $data['separation'][642]['expected'].=   '</code>';
        $data['separation'][642]['expected'].= '</pre>';

        $data['separation'][643]['data'] = '*  *  *'.NL;
        $data['separation'][643]['data'].= NL;
        $data['separation'][643]['data'].= '    code line 1'.NL;
        $data['separation'][643]['data'].= '         code line 2'.NL;
        $data['separation'][643]['data'].= '    code line 3';
        $data['separation'][643]['expected'] = '<hr>';
        $data['separation'][643]['expected'].= '<pre>';
        $data['separation'][643]['expected'].=   '<code>';
        $data['separation'][643]['expected'].=     'code line 1'.NL;
        $data['separation'][643]['expected'].=       '     code line 2'.NL;
        $data['separation'][643]['expected'].=     'code line 3';
        $data['separation'][643]['expected'].=   '</code>';
        $data['separation'][643]['expected'].= '</pre>';

        $data['separation'][644]['data'] = '-  -  -'.NL;
        $data['separation'][644]['data'].= NL;
        $data['separation'][644]['data'].= '    code line 1'.NL;
        $data['separation'][644]['data'].= '         code line 2'.NL;
        $data['separation'][644]['data'].= '    code line 3';
        $data['separation'][644]['expected'] = '<hr>';
        $data['separation'][644]['expected'].= '<pre>';
        $data['separation'][644]['expected'].=   '<code>';
        $data['separation'][644]['expected'].=     'code line 1'.NL;
        $data['separation'][644]['expected'].=       '     code line 2'.NL;
        $data['separation'][644]['expected'].=     'code line 3';
        $data['separation'][644]['expected'].=   '</code>';
        $data['separation'][644]['expected'].= '</pre>';

        $data['separation'][645]['data'] = '-------'.NL;
        $data['separation'][645]['data'].= NL;
        $data['separation'][645]['data'].= '    code line 1'.NL;
        $data['separation'][645]['data'].= '         code line 2'.NL;
        $data['separation'][645]['data'].= '    code line 3';
        $data['separation'][645]['expected'] = '<hr>';
        $data['separation'][645]['expected'].= '<pre>';
        $data['separation'][645]['expected'].=   '<code>';
        $data['separation'][645]['expected'].=     'code line 1'.NL;
        $data['separation'][645]['expected'].=       '     code line 2'.NL;
        $data['separation'][645]['expected'].=     'code line 3';
        $data['separation'][645]['expected'].=   '</code>';
        $data['separation'][645]['expected'].= '</pre>';

        ##########################
        ### hr near blockquote ###
        ##########################

        $data['separation'][650]['data'] = '*  *  *'.NL;
        $data['separation'][650]['data'].= '> blockquote line 1'.NL;
        $data['separation'][650]['data'].= '> blockquote line 2'.NL;
        $data['separation'][650]['data'].= '> blockquote line 3';
        $data['separation'][650]['expected'] = '<hr>';
        $data['separation'][650]['expected'].= '<blockquote>';
        $data['separation'][650]['expected'].=   '<p>';
        $data['separation'][650]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][650]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][650]['expected'].=     'blockquote line 3';
        $data['separation'][650]['expected'].=   '</p>';
        $data['separation'][650]['expected'].= '</blockquote>';

        $data['separation'][651]['data'] = '-  -  -'.NL;
        $data['separation'][651]['data'].= '> blockquote line 1'.NL;
        $data['separation'][651]['data'].= '> blockquote line 2'.NL;
        $data['separation'][651]['data'].= '> blockquote line 3';
        $data['separation'][651]['expected'] = '<hr>';
        $data['separation'][651]['expected'].= '<blockquote>';
        $data['separation'][651]['expected'].=   '<p>';
        $data['separation'][651]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][651]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][651]['expected'].=     'blockquote line 3';
        $data['separation'][651]['expected'].=   '</p>';
        $data['separation'][651]['expected'].= '</blockquote>';

        $data['separation'][652]['data'] = '-------'.NL;
        $data['separation'][652]['data'].= '> blockquote line 1'.NL;
        $data['separation'][652]['data'].= '> blockquote line 2'.NL;
        $data['separation'][652]['data'].= '> blockquote line 3';
        $data['separation'][652]['expected'] = '<hr>';
        $data['separation'][652]['expected'].= '<blockquote>';
        $data['separation'][652]['expected'].=   '<p>';
        $data['separation'][652]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][652]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][652]['expected'].=     'blockquote line 3';
        $data['separation'][652]['expected'].=   '</p>';
        $data['separation'][652]['expected'].= '</blockquote>';

        $data['separation'][653]['data'] = '*  *  *'.NL;
        $data['separation'][653]['data'].= NL;
        $data['separation'][653]['data'].= '> blockquote line 1'.NL;
        $data['separation'][653]['data'].= '> blockquote line 2'.NL;
        $data['separation'][653]['data'].= '> blockquote line 3';
        $data['separation'][653]['expected'] = '<hr>';
        $data['separation'][653]['expected'].= '<blockquote>';
        $data['separation'][653]['expected'].=   '<p>';
        $data['separation'][653]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][653]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][653]['expected'].=     'blockquote line 3';
        $data['separation'][653]['expected'].=   '</p>';
        $data['separation'][653]['expected'].= '</blockquote>';

        $data['separation'][654]['data'] = '-  -  -'.NL;
        $data['separation'][654]['data'].= NL;
        $data['separation'][654]['data'].= '> blockquote line 1'.NL;
        $data['separation'][654]['data'].= '> blockquote line 2'.NL;
        $data['separation'][654]['data'].= '> blockquote line 3';
        $data['separation'][654]['expected'] = '<hr>';
        $data['separation'][654]['expected'].= '<blockquote>';
        $data['separation'][654]['expected'].=   '<p>';
        $data['separation'][654]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][654]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][654]['expected'].=     'blockquote line 3';
        $data['separation'][654]['expected'].=   '</p>';
        $data['separation'][654]['expected'].= '</blockquote>';

        $data['separation'][655]['data'] = '-------'.NL;
        $data['separation'][655]['data'].= NL;
        $data['separation'][655]['data'].= '> blockquote line 1'.NL;
        $data['separation'][655]['data'].= '> blockquote line 2'.NL;
        $data['separation'][655]['data'].= '> blockquote line 3';
        $data['separation'][655]['expected'] = '<hr>';
        $data['separation'][655]['expected'].= '<blockquote>';
        $data['separation'][655]['expected'].=   '<p>';
        $data['separation'][655]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][655]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][655]['expected'].=     'blockquote line 3';
        $data['separation'][655]['expected'].=   '</p>';
        $data['separation'][655]['expected'].= '</blockquote>';

        ##################
        ### hr near hr ###
        ##################

        $data['separation'][660]['data'] = '-  -  -'.NL;
        $data['separation'][660]['data'].= '-  -  -';
        $data['separation'][660]['expected'] = '<hr>';
        $data['separation'][660]['expected'].= '<hr>';

        $data['separation'][661]['data'] = '*  *  *'.NL;
        $data['separation'][661]['data'].= '*  *  *';
        $data['separation'][661]['expected'] = '<hr>';
        $data['separation'][661]['expected'].= '<hr>';

        $data['separation'][662]['data'] = '*  *  *'.NL;
        $data['separation'][662]['data'].= '-  -  -';
        $data['separation'][662]['expected'] = '<hr>';
        $data['separation'][662]['expected'].= '<hr>';

        $data['separation'][663]['data'] = '-  -  -'.NL;
        $data['separation'][663]['data'].= '*  *  *';
        $data['separation'][663]['expected'] = '<hr>';
        $data['separation'][663]['expected'].= '<hr>';

        $data['separation'][664]['data'] = '-------'.NL;
        $data['separation'][664]['data'].= '-------';
        $data['separation'][664]['expected'] = '<hr>';
        $data['separation'][664]['expected'].= '<hr>';

        $data['separation'][665]['data'] = '*  *  *'.NL;
        $data['separation'][665]['data'].= '*  *  *';
        $data['separation'][665]['expected'] = '<hr>';
        $data['separation'][665]['expected'].= '<hr>';

        $data['separation'][667]['data'] = '*  *  *'.NL;
        $data['separation'][667]['data'].= '-------';
        $data['separation'][667]['expected'] = '<hr>';
        $data['separation'][667]['expected'].= '<hr>';

        $data['separation'][668]['data'] = '-------'.NL;
        $data['separation'][668]['data'].= '*  *  *';
        $data['separation'][668]['expected'] = '<hr>';
        $data['separation'][668]['expected'].= '<hr>';

        $data['separation'][669]['data'] = '-  -  -'.NL;
        $data['separation'][669]['data'].= NL;
        $data['separation'][669]['data'].= '-  -  -';
        $data['separation'][669]['expected'] = '<hr>';
        $data['separation'][669]['expected'].= '<hr>';

        $data['separation'][670]['data'] = '*  *  *'.NL;
        $data['separation'][670]['data'].= NL;
        $data['separation'][670]['data'].= '*  *  *';
        $data['separation'][670]['expected'] = '<hr>';
        $data['separation'][670]['expected'].= '<hr>';

        $data['separation'][671]['data'] = '*  *  *'.NL;
        $data['separation'][671]['data'].= NL;
        $data['separation'][671]['data'].= '-  -  -';
        $data['separation'][671]['expected'] = '<hr>';
        $data['separation'][671]['expected'].= '<hr>';

        $data['separation'][672]['data'] = '-  -  -'.NL;
        $data['separation'][672]['data'].= NL;
        $data['separation'][672]['data'].= '*  *  *';
        $data['separation'][672]['expected'] = '<hr>';
        $data['separation'][672]['expected'].= '<hr>';

        $data['separation'][673]['data'] = '-------'.NL;
        $data['separation'][673]['data'].= NL;
        $data['separation'][673]['data'].= '-------';
        $data['separation'][673]['expected'] = '<hr>';
        $data['separation'][673]['expected'].= '<hr>';

        $data['separation'][674]['data'] = '*  *  *'.NL;
        $data['separation'][674]['data'].= NL;
        $data['separation'][674]['data'].= '*  *  *';
        $data['separation'][674]['expected'] = '<hr>';
        $data['separation'][674]['expected'].= '<hr>';

        $data['separation'][675]['data'] = '*  *  *'.NL;
        $data['separation'][675]['data'].= NL;
        $data['separation'][675]['data'].= '-------';
        $data['separation'][675]['expected'] = '<hr>';
        $data['separation'][675]['expected'].= '<hr>';

        $data['separation'][676]['data'] = '-------'.NL;
        $data['separation'][676]['data'].= NL;
        $data['separation'][676]['data'].= '*  *  *';
        $data['separation'][676]['expected'] = '<hr>';
        $data['separation'][676]['expected'].= '<hr>';

        ######################
        ### hr near markup ###
        ######################

        $data['separation'][680]['data'] = '*  *  *'.NL;
        $data['separation'][680]['data'].= '<div>'.NL;
        $data['separation'][680]['data'].= '  markup line 1'.NL;
        $data['separation'][680]['data'].= '  markup line 2'.NL;
        $data['separation'][680]['data'].= '  markup line 3'.NL;
        $data['separation'][680]['data'].= '</div>';
        $data['separation'][680]['expected'] = '<hr>';
        $data['separation'][680]['expected'].= '<div>'.NL;
        $data['separation'][680]['expected'].= '  markup line 1'.NL;
        $data['separation'][680]['expected'].= '  markup line 2'.NL;
        $data['separation'][680]['expected'].= '  markup line 3'.NL;
        $data['separation'][680]['expected'].= '</div>';

        $data['separation'][681]['data'] = '-  -  -'.NL;
        $data['separation'][681]['data'].= '<div>'.NL;
        $data['separation'][681]['data'].= '  markup line 1'.NL;
        $data['separation'][681]['data'].= '  markup line 2'.NL;
        $data['separation'][681]['data'].= '  markup line 3'.NL;
        $data['separation'][681]['data'].= '</div>';
        $data['separation'][681]['expected'] = '<hr>';
        $data['separation'][681]['expected'].= '<div>'.NL;
        $data['separation'][681]['expected'].= '  markup line 1'.NL;
        $data['separation'][681]['expected'].= '  markup line 2'.NL;
        $data['separation'][681]['expected'].= '  markup line 3'.NL;
        $data['separation'][681]['expected'].= '</div>';

        $data['separation'][682]['data'] = '-------'.NL;
        $data['separation'][682]['data'].= '<div>'.NL;
        $data['separation'][682]['data'].= '  markup line 1'.NL;
        $data['separation'][682]['data'].= '  markup line 2'.NL;
        $data['separation'][682]['data'].= '  markup line 3'.NL;
        $data['separation'][682]['data'].= '</div>';
        $data['separation'][682]['expected'] = '<hr>';
        $data['separation'][682]['expected'].= '<div>'.NL;
        $data['separation'][682]['expected'].= '  markup line 1'.NL;
        $data['separation'][682]['expected'].= '  markup line 2'.NL;
        $data['separation'][682]['expected'].= '  markup line 3'.NL;
        $data['separation'][682]['expected'].= '</div>';

        $data['separation'][683]['data'] = '*  *  *'.NL;
        $data['separation'][683]['data'].= NL;
        $data['separation'][683]['data'].= '<div>'.NL;
        $data['separation'][683]['data'].= '  markup line 1'.NL;
        $data['separation'][683]['data'].= '  markup line 2'.NL;
        $data['separation'][683]['data'].= '  markup line 3'.NL;
        $data['separation'][683]['data'].= '</div>';
        $data['separation'][683]['expected'] = '<hr>';
        $data['separation'][683]['expected'].= '<div>'.NL;
        $data['separation'][683]['expected'].= '  markup line 1'.NL;
        $data['separation'][683]['expected'].= '  markup line 2'.NL;
        $data['separation'][683]['expected'].= '  markup line 3'.NL;
        $data['separation'][683]['expected'].= '</div>';

        $data['separation'][684]['data'] = '-  -  -'.NL;
        $data['separation'][684]['data'].= NL;
        $data['separation'][684]['data'].= '<div>'.NL;
        $data['separation'][684]['data'].= '  markup line 1'.NL;
        $data['separation'][684]['data'].= '  markup line 2'.NL;
        $data['separation'][684]['data'].= '  markup line 3'.NL;
        $data['separation'][684]['data'].= '</div>';
        $data['separation'][684]['expected'] = '<hr>';
        $data['separation'][684]['expected'].= '<div>'.NL;
        $data['separation'][684]['expected'].= '  markup line 1'.NL;
        $data['separation'][684]['expected'].= '  markup line 2'.NL;
        $data['separation'][684]['expected'].= '  markup line 3'.NL;
        $data['separation'][684]['expected'].= '</div>';

        $data['separation'][685]['data'] = '-------'.NL;
        $data['separation'][685]['data'].= NL;
        $data['separation'][685]['data'].= '<div>'.NL;
        $data['separation'][685]['data'].= '  markup line 1'.NL;
        $data['separation'][685]['data'].= '  markup line 2'.NL;
        $data['separation'][685]['data'].= '  markup line 3'.NL;
        $data['separation'][685]['data'].= '</div>';
        $data['separation'][685]['expected'] = '<hr>';
        $data['separation'][685]['expected'].= '<div>'.NL;
        $data['separation'][685]['expected'].= '  markup line 1'.NL;
        $data['separation'][685]['expected'].= '  markup line 2'.NL;
        $data['separation'][685]['expected'].= '  markup line 3'.NL;
        $data['separation'][685]['expected'].= '</div>';

        ##########################
        ### markup near markup ###
        ##########################

        $data['separation'][700]['data'] = '<div>'.NL;
        $data['separation'][700]['data'].= '  markup 1 line 1'.NL;
        $data['separation'][700]['data'].= '  markup 1 line 2'.NL;
        $data['separation'][700]['data'].= '  markup 1 line 3'.NL;
        $data['separation'][700]['data'].= '</div>'.NL;
        $data['separation'][700]['data'].= '<div>'.NL;
        $data['separation'][700]['data'].= '  markup 2 line 1'.NL;
        $data['separation'][700]['data'].= '  markup 2 line 2'.NL;
        $data['separation'][700]['data'].= '  markup 2 line 3'.NL;
        $data['separation'][700]['data'].= '</div>';
        $data['separation'][700]['expected'] = '<div>'.NL;
        $data['separation'][700]['expected'].= '  markup 1 line 1'.NL;
        $data['separation'][700]['expected'].= '  markup 1 line 2'.NL;
        $data['separation'][700]['expected'].= '  markup 1 line 3'.NL;
        $data['separation'][700]['expected'].= '</div>'.NL;
        $data['separation'][700]['expected'].= '<div>'.NL;
        $data['separation'][700]['expected'].= '  markup 2 line 1'.NL;
        $data['separation'][700]['expected'].= '  markup 2 line 2'.NL;
        $data['separation'][700]['expected'].= '  markup 2 line 3'.NL;
        $data['separation'][700]['expected'].= '</div>';

        $data['separation'][701]['data'] = '<div>'.NL;
        $data['separation'][701]['data'].= '  markup 1 line 1'.NL;
        $data['separation'][701]['data'].= '  markup 1 line 2'.NL;
        $data['separation'][701]['data'].= '  markup 1 line 3'.NL;
        $data['separation'][701]['data'].= '</div>'.NL;
        $data['separation'][701]['data'].= NL;
        $data['separation'][701]['data'].= '<div>'.NL;
        $data['separation'][701]['data'].= '  markup 2 line 1'.NL;
        $data['separation'][701]['data'].= '  markup 2 line 2'.NL;
        $data['separation'][701]['data'].= '  markup 2 line 3'.NL;
        $data['separation'][701]['data'].= '</div>';
        $data['separation'][701]['expected'] = '<div>'.NL;
        $data['separation'][701]['expected'].= '  markup 1 line 1'.NL;
        $data['separation'][701]['expected'].= '  markup 1 line 2'.NL;
        $data['separation'][701]['expected'].= '  markup 1 line 3'.NL;
        $data['separation'][701]['expected'].= '</div>';
        $data['separation'][701]['expected'].= '<div>'.NL;
        $data['separation'][701]['expected'].= '  markup 2 line 1'.NL;
        $data['separation'][701]['expected'].= '  markup 2 line 2'.NL;
        $data['separation'][701]['expected'].= '  markup 2 line 3'.NL;
        $data['separation'][701]['expected'].= '</div>';

        ##########################
        ### markup near header ###
        ##########################

        $data['separation'][710]['data'] = '<div>'.NL;
        $data['separation'][710]['data'].= '  markup line 1'.NL;
        $data['separation'][710]['data'].= '  markup line 2'.NL;
        $data['separation'][710]['data'].= '  markup line 3'.NL;
        $data['separation'][710]['data'].= '</div>'.NL;
        $data['separation'][710]['data'].= '# Title H1 (atx-style)';
        $data['separation'][710]['expected'] = '<div>'.NL;
        $data['separation'][710]['expected'].= '  markup line 1'.NL;
        $data['separation'][710]['expected'].= '  markup line 2'.NL;
        $data['separation'][710]['expected'].= '  markup line 3'.NL;
        $data['separation'][710]['expected'].= '</div>'.NL;
        $data['separation'][710]['expected'].= '# Title H1 (atx-style)';

        $data['separation'][711]['data'] = '<div>'.NL;
        $data['separation'][711]['data'].= '  markup line 1'.NL;
        $data['separation'][711]['data'].= '  markup line 2'.NL;
        $data['separation'][711]['data'].= '  markup line 3'.NL;
        $data['separation'][711]['data'].= '</div>'.NL;
        $data['separation'][711]['data'].= NL;
        $data['separation'][711]['data'].= '# Title H1 (atx-style)';
        $data['separation'][711]['expected'] = '<div>'.NL;
        $data['separation'][711]['expected'].= '  markup line 1'.NL;
        $data['separation'][711]['expected'].= '  markup line 2'.NL;
        $data['separation'][711]['expected'].= '  markup line 3'.NL;
        $data['separation'][711]['expected'].= '</div>';
        $data['separation'][711]['expected'].= '<h1>Title H1 (atx-style)</h1>';

        $data['separation'][712]['data'] = '<div>'.NL;
        $data['separation'][712]['data'].= '  markup line 1'.NL;
        $data['separation'][712]['data'].= '  markup line 2'.NL;
        $data['separation'][712]['data'].= '  markup line 3'.NL;
        $data['separation'][712]['data'].= '</div>'.NL;
        $data['separation'][712]['data'].= 'Title H1 (Setext-style)'.NL;
        $data['separation'][712]['data'].= '=======================';
        $data['separation'][712]['expected'] = '<div>'.NL;
        $data['separation'][712]['expected'].= '  markup line 1'.NL;
        $data['separation'][712]['expected'].= '  markup line 2'.NL;
        $data['separation'][712]['expected'].= '  markup line 3'.NL;
        $data['separation'][712]['expected'].= '</div>'.NL;
        $data['separation'][712]['expected'].= 'Title H1 (Setext-style)'.NL;
        $data['separation'][712]['expected'].= '=======================';

        $data['separation'][713]['data'] = '<div>'.NL;
        $data['separation'][713]['data'].= '  markup line 1'.NL;
        $data['separation'][713]['data'].= '  markup line 2'.NL;
        $data['separation'][713]['data'].= '  markup line 3'.NL;
        $data['separation'][713]['data'].= '</div>'.NL;
        $data['separation'][713]['data'].= NL;
        $data['separation'][713]['data'].= 'Title H1 (Setext-style)'.NL;
        $data['separation'][713]['data'].= '=======================';
        $data['separation'][713]['expected'] = '<div>'.NL;
        $data['separation'][713]['expected'].= '  markup line 1'.NL;
        $data['separation'][713]['expected'].= '  markup line 2'.NL;
        $data['separation'][713]['expected'].= '  markup line 3'.NL;
        $data['separation'][713]['expected'].= '</div>';
        $data['separation'][713]['expected'].= '<h1>Title H1 (Setext-style)</h1>';

        #############################
        ### markup near paragraph ###
        #############################

        $data['separation'][720]['data'] = '<div>'.NL;
        $data['separation'][720]['data'].= '  markup line 1'.NL;
        $data['separation'][720]['data'].= '  markup line 2'.NL;
        $data['separation'][720]['data'].= '  markup line 3'.NL;
        $data['separation'][720]['data'].= '</div>'.NL;
        $data['separation'][720]['data'].= 'paragraph line 1'.NL;
        $data['separation'][720]['data'].= 'paragraph line 2'.NL;
        $data['separation'][720]['data'].= 'paragraph line 3';
        $data['separation'][720]['expected'] = '<div>'.NL;
        $data['separation'][720]['expected'].= '  markup line 1'.NL;
        $data['separation'][720]['expected'].= '  markup line 2'.NL;
        $data['separation'][720]['expected'].= '  markup line 3'.NL;
        $data['separation'][720]['expected'].= '</div>'.NL;
        $data['separation'][720]['expected'].= 'paragraph line 1'.NL;
        $data['separation'][720]['expected'].= 'paragraph line 2'.NL;
        $data['separation'][720]['expected'].= 'paragraph line 3';

        $data['separation'][721]['data'] = '<div>'.NL;
        $data['separation'][721]['data'].= '  markup line 1'.NL;
        $data['separation'][721]['data'].= '  markup line 2'.NL;
        $data['separation'][721]['data'].= '  markup line 3'.NL;
        $data['separation'][721]['data'].= '</div>'.NL;
        $data['separation'][721]['data'].= NL;
        $data['separation'][721]['data'].= 'paragraph line 1'.NL;
        $data['separation'][721]['data'].= 'paragraph line 2'.NL;
        $data['separation'][721]['data'].= 'paragraph line 3';
        $data['separation'][721]['expected'] = '<div>'.NL;
        $data['separation'][721]['expected'].= '  markup line 1'.NL;
        $data['separation'][721]['expected'].= '  markup line 2'.NL;
        $data['separation'][721]['expected'].= '  markup line 3'.NL;
        $data['separation'][721]['expected'].= '</div>';
        $data['separation'][721]['expected'].= '<p>';
        $data['separation'][721]['expected'].=   'paragraph line 1'.NL;
        $data['separation'][721]['expected'].=   'paragraph line 2'.NL;
        $data['separation'][721]['expected'].=   'paragraph line 3';
        $data['separation'][721]['expected'].= '</p>';

        ########################
        ### markup near list ###
        ########################

        $data['separation'][730]['data'] = '<div>'.NL;
        $data['separation'][730]['data'].= '  markup line 1'.NL;
        $data['separation'][730]['data'].= '  markup line 2'.NL;
        $data['separation'][730]['data'].= '  markup line 3'.NL;
        $data['separation'][730]['data'].= '</div>'.NL;
        $data['separation'][730]['data'].= '* list item 1'.NL;
        $data['separation'][730]['data'].= '* list item 2'.NL;
        $data['separation'][730]['data'].= '* list item 3';
        $data['separation'][730]['expected'] = '<div>'.NL;
        $data['separation'][730]['expected'].= '  markup line 1'.NL;
        $data['separation'][730]['expected'].= '  markup line 2'.NL;
        $data['separation'][730]['expected'].= '  markup line 3'.NL;
        $data['separation'][730]['expected'].= '</div>'.NL;
        $data['separation'][730]['expected'].= '* list item 1'.NL;
        $data['separation'][730]['expected'].= '* list item 2'.NL;
        $data['separation'][730]['expected'].= '* list item 3';

        $data['separation'][731]['data'] = '<div>'.NL;
        $data['separation'][731]['data'].= '  markup line 1'.NL;
        $data['separation'][731]['data'].= '  markup line 2'.NL;
        $data['separation'][731]['data'].= '  markup line 3'.NL;
        $data['separation'][731]['data'].= '</div>'.NL;
        $data['separation'][731]['data'].= NL;
        $data['separation'][731]['data'].= '* list item 1'.NL;
        $data['separation'][731]['data'].= '* list item 2'.NL;
        $data['separation'][731]['data'].= '* list item 3';
        $data['separation'][731]['expected'] = '<div>'.NL;
        $data['separation'][731]['expected'].= '  markup line 1'.NL;
        $data['separation'][731]['expected'].= '  markup line 2'.NL;
        $data['separation'][731]['expected'].= '  markup line 3'.NL;
        $data['separation'][731]['expected'].= '</div>';
        $data['separation'][731]['expected'].= '<ul>';
        $data['separation'][731]['expected'].=   '<li>list item 1</li>';
        $data['separation'][731]['expected'].=   '<li>list item 2</li>';
        $data['separation'][731]['expected'].=   '<li>list item 3</li>';
        $data['separation'][731]['expected'].= '</ul>';

        ########################
        ### markup near code ###
        ########################

        $data['separation'][740]['data'] = '<div>'.NL;
        $data['separation'][740]['data'].= '  markup line 1'.NL;
        $data['separation'][740]['data'].= '  markup line 2'.NL;
        $data['separation'][740]['data'].= '  markup line 3'.NL;
        $data['separation'][740]['data'].= '</div>'.NL;
        $data['separation'][740]['data'].= '    code line 1'.NL;
        $data['separation'][740]['data'].= '         code line 2'.NL;
        $data['separation'][740]['data'].= '    code line 3';
        $data['separation'][740]['expected'] = '<div>'.NL;
        $data['separation'][740]['expected'].= '  markup line 1'.NL;
        $data['separation'][740]['expected'].= '  markup line 2'.NL;
        $data['separation'][740]['expected'].= '  markup line 3'.NL;
        $data['separation'][740]['expected'].= '</div>'.NL;
        $data['separation'][740]['expected'].= '    code line 1'.NL;
        $data['separation'][740]['expected'].= '         code line 2'.NL;
        $data['separation'][740]['expected'].= '    code line 3';

        $data['separation'][741]['data'] = '<div>'.NL;
        $data['separation'][741]['data'].= '  markup line 1'.NL;
        $data['separation'][741]['data'].= '  markup line 2'.NL;
        $data['separation'][741]['data'].= '  markup line 3'.NL;
        $data['separation'][741]['data'].= '</div>'.NL;
        $data['separation'][741]['data'].= NL;
        $data['separation'][741]['data'].= '    code line 1'.NL;
        $data['separation'][741]['data'].= '         code line 2'.NL;
        $data['separation'][741]['data'].= '    code line 3';
        $data['separation'][741]['expected'] = '<div>'.NL;
        $data['separation'][741]['expected'].= '  markup line 1'.NL;
        $data['separation'][741]['expected'].= '  markup line 2'.NL;
        $data['separation'][741]['expected'].= '  markup line 3'.NL;
        $data['separation'][741]['expected'].= '</div>';
        $data['separation'][741]['expected'].= '<pre>';
        $data['separation'][741]['expected'].=   '<code>';
        $data['separation'][741]['expected'].=     'code line 1'.NL;
        $data['separation'][741]['expected'].=       '     code line 2'.NL;
        $data['separation'][741]['expected'].=     'code line 3';
        $data['separation'][741]['expected'].=   '</code>';
        $data['separation'][741]['expected'].= '</pre>';

        ##############################
        ### markup near blockquote ###
        ##############################

        $data['separation'][750]['data'] = '<div>'.NL;
        $data['separation'][750]['data'].= '  markup line 1'.NL;
        $data['separation'][750]['data'].= '  markup line 2'.NL;
        $data['separation'][750]['data'].= '  markup line 3'.NL;
        $data['separation'][750]['data'].= '</div>'.NL;
        $data['separation'][750]['data'].= '> blockquote line 1'.NL;
        $data['separation'][750]['data'].= '> blockquote line 2'.NL;
        $data['separation'][750]['data'].= '> blockquote line 3';
        $data['separation'][750]['expected'] = '<div>'.NL;
        $data['separation'][750]['expected'].= '  markup line 1'.NL;
        $data['separation'][750]['expected'].= '  markup line 2'.NL;
        $data['separation'][750]['expected'].= '  markup line 3'.NL;
        $data['separation'][750]['expected'].= '</div>'.NL;
        $data['separation'][750]['expected'].= '> blockquote line 1'.NL;
        $data['separation'][750]['expected'].= '> blockquote line 2'.NL;
        $data['separation'][750]['expected'].= '> blockquote line 3';

        $data['separation'][751]['data'] = '<div>'.NL;
        $data['separation'][751]['data'].= '  markup line 1'.NL;
        $data['separation'][751]['data'].= '  markup line 2'.NL;
        $data['separation'][751]['data'].= '  markup line 3'.NL;
        $data['separation'][751]['data'].= '</div>'.NL;
        $data['separation'][751]['data'].= NL;
        $data['separation'][751]['data'].= '> blockquote line 1'.NL;
        $data['separation'][751]['data'].= '> blockquote line 2'.NL;
        $data['separation'][751]['data'].= '> blockquote line 3';
        $data['separation'][751]['expected'] = '<div>'.NL;
        $data['separation'][751]['expected'].= '  markup line 1'.NL;
        $data['separation'][751]['expected'].= '  markup line 2'.NL;
        $data['separation'][751]['expected'].= '  markup line 3'.NL;
        $data['separation'][751]['expected'].= '</div>';
        $data['separation'][751]['expected'].= '<blockquote>';
        $data['separation'][751]['expected'].=   '<p>';
        $data['separation'][751]['expected'].=     'blockquote line 1'.NL;
        $data['separation'][751]['expected'].=     'blockquote line 2'.NL;
        $data['separation'][751]['expected'].=     'blockquote line 3';
        $data['separation'][751]['expected'].=   '</p>';
        $data['separation'][751]['expected'].= '</blockquote>';

        ######################
        ### markup near hr ###
        ######################

        $data['separation'][760]['data'] = '<div>'.NL;
        $data['separation'][760]['data'].= '  markup line 1'.NL;
        $data['separation'][760]['data'].= '  markup line 2'.NL;
        $data['separation'][760]['data'].= '  markup line 3'.NL;
        $data['separation'][760]['data'].= '</div>'.NL;
        $data['separation'][760]['data'].= '*  *  *';
        $data['separation'][760]['expected'] = '<div>'.NL;
        $data['separation'][760]['expected'].= '  markup line 1'.NL;
        $data['separation'][760]['expected'].= '  markup line 2'.NL;
        $data['separation'][760]['expected'].= '  markup line 3'.NL;
        $data['separation'][760]['expected'].= '</div>'.NL;
        $data['separation'][760]['expected'].= '*  *  *';

        $data['separation'][761]['data'] = '<div>'.NL;
        $data['separation'][761]['data'].= '  markup line 1'.NL;
        $data['separation'][761]['data'].= '  markup line 2'.NL;
        $data['separation'][761]['data'].= '  markup line 3'.NL;
        $data['separation'][761]['data'].= '</div>'.NL;
        $data['separation'][761]['data'].= '-  -  -';
        $data['separation'][761]['expected'] = '<div>'.NL;
        $data['separation'][761]['expected'].= '  markup line 1'.NL;
        $data['separation'][761]['expected'].= '  markup line 2'.NL;
        $data['separation'][761]['expected'].= '  markup line 3'.NL;
        $data['separation'][761]['expected'].= '</div>'.NL;
        $data['separation'][761]['expected'].= '-  -  -';

        $data['separation'][762]['data'] = '<div>'.NL;
        $data['separation'][762]['data'].= '  markup line 1'.NL;
        $data['separation'][762]['data'].= '  markup line 2'.NL;
        $data['separation'][762]['data'].= '  markup line 3'.NL;
        $data['separation'][762]['data'].= '</div>'.NL;
        $data['separation'][762]['data'].= '-------';
        $data['separation'][762]['expected'] = '<div>'.NL;
        $data['separation'][762]['expected'].= '  markup line 1'.NL;
        $data['separation'][762]['expected'].= '  markup line 2'.NL;
        $data['separation'][762]['expected'].= '  markup line 3'.NL;
        $data['separation'][762]['expected'].= '</div>'.NL;
        $data['separation'][762]['expected'].= '-------';

        $data['separation'][763]['data'] = '<div>'.NL;
        $data['separation'][763]['data'].= '  markup line 1'.NL;
        $data['separation'][763]['data'].= '  markup line 2'.NL;
        $data['separation'][763]['data'].= '  markup line 3'.NL;
        $data['separation'][763]['data'].= '</div>'.NL;
        $data['separation'][763]['data'].= NL;
        $data['separation'][763]['data'].= '*  *  *';
        $data['separation'][763]['expected'] = '<div>'.NL;
        $data['separation'][763]['expected'].= '  markup line 1'.NL;
        $data['separation'][763]['expected'].= '  markup line 2'.NL;
        $data['separation'][763]['expected'].= '  markup line 3'.NL;
        $data['separation'][763]['expected'].= '</div>';
        $data['separation'][763]['expected'].= '<hr>';

        $data['separation'][764]['data'] = '<div>'.NL;
        $data['separation'][764]['data'].= '  markup line 1'.NL;
        $data['separation'][764]['data'].= '  markup line 2'.NL;
        $data['separation'][764]['data'].= '  markup line 3'.NL;
        $data['separation'][764]['data'].= '</div>'.NL;
        $data['separation'][764]['data'].= NL;
        $data['separation'][764]['data'].= '-  -  -';
        $data['separation'][764]['expected'] = '<div>'.NL;
        $data['separation'][764]['expected'].= '  markup line 1'.NL;
        $data['separation'][764]['expected'].= '  markup line 2'.NL;
        $data['separation'][764]['expected'].= '  markup line 3'.NL;
        $data['separation'][764]['expected'].= '</div>';
        $data['separation'][764]['expected'].= '<hr>';

        $data['separation'][765]['data'] = '<div>'.NL;
        $data['separation'][765]['data'].= '  markup line 1'.NL;
        $data['separation'][765]['data'].= '  markup line 2'.NL;
        $data['separation'][765]['data'].= '  markup line 3'.NL;
        $data['separation'][765]['data'].= '</div>'.NL;
        $data['separation'][765]['data'].= NL;
        $data['separation'][765]['data'].= '-------';
        $data['separation'][765]['expected'] = '<div>'.NL;
        $data['separation'][765]['expected'].= '  markup line 1'.NL;
        $data['separation'][765]['expected'].= '  markup line 2'.NL;
        $data['separation'][765]['expected'].= '  markup line 3'.NL;
        $data['separation'][765]['expected'].= '</div>';
        $data['separation'][765]['expected'].= '<hr>';

        foreach ($data['separation'] as $c_row_id => $c_info) {
            $c_expected = $c_info['expected'];
            $c_value = $c_info['data'];
            $c_gotten = Markdown::markdown_to_markup($c_value)->render();
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__markdown_to_markup__combination(&$test, $dpath, &$c_results) {

        ################################
        ### header (atx) with markup ###
        ################################

        $data['combination'][100]['data'] = '# <a href="http://example.com">link</a>';
        $data['combination'][100]['expected'] = '<h1>';
        $data['combination'][100]['expected'].=   '<a href="http://example.com">link</a>';
        $data['combination'][100]['expected'].= '</h1>';

        $data['combination'][101]['data'] = '# <x-tag data-attribute="true">data</x-tag>';
        $data['combination'][101]['expected'] = '<h1>';
        $data['combination'][101]['expected'].=   '<x-tag data-attribute="true">data</x-tag>';
        $data['combination'][101]['expected'].= '</h1>';

        $data['combination'][102]['data'] = '# <div data-attribute="true">data</div>';
        $data['combination'][102]['expected'] = '<h1>';
        $data['combination'][102]['expected'].=   '<div data-attribute="true">data</div>';
        $data['combination'][102]['expected'].= '</h1>';

        #########################################
        ### header (Setext) with header (atx) ###
        #########################################

        $data['combination'][200]['data'] = '# Title H1'.NL;
        $data['combination'][200]['data'].= '----------';
        $data['combination'][200]['expected'] = '<h1>Title H1</h1>';
        $data['combination'][200]['expected'].= '<hr>';

        ######################################
        ### header (Setext) with paragraph ###
        ######################################

        $data['combination'][210]['data'] = 'paragraph line 1'.NL;
        $data['combination'][210]['data'].= 'paragraph line 2'.NL;
        $data['combination'][210]['data'].= 'paragraph line 3'.NL;
        $data['combination'][210]['data'].= '----------------';
        $data['combination'][210]['expected'] = '<h2>';
        $data['combination'][210]['expected'].=   'paragraph line 1'.NL;
        $data['combination'][210]['expected'].=   'paragraph line 2'.NL;
        $data['combination'][210]['expected'].=   'paragraph line 3';
        $data['combination'][210]['expected'].= '</h2>';

        ######################################
        ### header (Setext) with list item ###
        ######################################

        $data['combination'][220]['data'] = '- list item'.NL;
        $data['combination'][220]['data'].= '-----------';
        $data['combination'][220]['expected'] = '<ul>';
        $data['combination'][220]['expected'].=   '<li>list item</li>';
        $data['combination'][220]['expected'].= '</ul>';
        $data['combination'][220]['expected'].= '<hr>';

        $data['combination'][221]['data'] = '- list item 1'.NL;
        $data['combination'][221]['data'].= '- list item 2'.NL;
        $data['combination'][221]['data'].= '- list item 3'.NL;
        $data['combination'][221]['data'].= '-------------';
        $data['combination'][221]['expected'] = '<ul>';
        $data['combination'][221]['expected'].=   '<li>list item 1</li>';
        $data['combination'][221]['expected'].=   '<li>list item 2</li>';
        $data['combination'][221]['expected'].=   '<li>list item 3</li>';
        $data['combination'][221]['expected'].= '</ul>';
        $data['combination'][221]['expected'].= '<hr>';

        #################################
        ### header (Setext) with code ###
        #################################

        $data['combination'][230]['data'] = '    code line 1'.NL;
        $data['combination'][230]['data'].= '         code line 2'.NL;
        $data['combination'][230]['data'].= '    code line 3'.NL;
        $data['combination'][230]['data'].= '---------------';
        $data['combination'][230]['expected'] = '<pre>';
        $data['combination'][230]['expected'].=   '<code>';
        $data['combination'][230]['expected'].=     'code line 1'.NL;
        $data['combination'][230]['expected'].=       '     code line 2'.NL;
        $data['combination'][230]['expected'].=     'code line 3';
        $data['combination'][230]['expected'].=   '</code>';
        $data['combination'][230]['expected'].= '</pre>';
        $data['combination'][230]['expected'].= '<hr>';

        #######################################
        ### header (Setext) with blockquote ###
        #######################################

        $data['combination'][240]['data'] = '> blockquote line 1'.NL;
        $data['combination'][240]['data'].= '> blockquote line 2'.NL;
        $data['combination'][240]['data'].= '> blockquote line 3'.NL;
        $data['combination'][240]['data'].= '-------------------';
        $data['combination'][240]['expected'] = '<blockquote>';
        $data['combination'][240]['expected'].=   '<p>';
        $data['combination'][240]['expected'].=     'blockquote line 1'.NL;
        $data['combination'][240]['expected'].=     'blockquote line 2'.NL;
        $data['combination'][240]['expected'].=     'blockquote line 3';
        $data['combination'][240]['expected'].=   '</p>';
        $data['combination'][240]['expected'].= '</blockquote>';
        $data['combination'][240]['expected'].= '<hr>';

        ###############################
        ### header (Setext) with hr ###
        ###############################

        $data['combination'][250]['data'] = '*  *  *'.NL;
        $data['combination'][250]['data'].= '-------';
        $data['combination'][250]['expected'] = '<hr>';
        $data['combination'][250]['expected'].= '<hr>';

        ###################################
        ### header (Setext) with markup ###
        ###################################

        $data['combination'][260]['data'] = '<a href="http://example.com">link</a>'.NL;
        $data['combination'][260]['data'].= '-------------------------------------';
        $data['combination'][260]['expected'] = '<h2>';
        $data['combination'][260]['expected'].=   '<a href="http://example.com">link</a>';
        $data['combination'][260]['expected'].= '</h2>';

        $data['combination'][261]['data'] = '<x-tag data-attribute="true">data</x-tag>'.NL;
        $data['combination'][261]['data'].= '-----------------------------------------';
        $data['combination'][261]['expected'] = '<h2>';
        $data['combination'][261]['expected'].=   '<x-tag data-attribute="true">data</x-tag>';
        $data['combination'][261]['expected'].= '</h2>';

        $data['combination'][262]['data'] = '<div data-attribute="true">data</div>'.NL;
        $data['combination'][262]['data'].= '-------------------------------------';
        $data['combination'][262]['expected'] = '<div data-attribute="true">data</div>'.NL;
        $data['combination'][262]['expected'].= '-------------------------------------';

        #############################
        ### paragraph with markup ###
        #############################

        $data['combination'][300]['data'] = 'paragraph line 1'.NL;
        $data['combination'][300]['data'].= '<a href="http://example.com">link</a>'.NL;
        $data['combination'][300]['data'].= 'paragraph line 3';
        $data['combination'][300]['expected'] = '<p>';
        $data['combination'][300]['expected'].=   'paragraph line 1'.NL;
        $data['combination'][300]['expected'].=   '<a href="http://example.com">link</a>'.NL;
        $data['combination'][300]['expected'].=   'paragraph line 3';
        $data['combination'][300]['expected'].= '</p>';

        $data['combination'][301]['data'] = 'paragraph line 1'.NL;
        $data['combination'][301]['data'].= '<x-tag data-attribute="true">data</x-tag>'.NL;
        $data['combination'][301]['data'].= 'paragraph line 3';
        $data['combination'][301]['expected'] = '<p>';
        $data['combination'][301]['expected'].=   'paragraph line 1'.NL;
        $data['combination'][301]['expected'].=   '<x-tag data-attribute="true">data</x-tag>'.NL;
        $data['combination'][301]['expected'].=   'paragraph line 3';
        $data['combination'][301]['expected'].= '</p>';

        $data['combination'][302]['data'] = 'paragraph line 1'.NL;
        $data['combination'][302]['data'].= '<div data-attribute="true">data</div>'.NL;
        $data['combination'][302]['data'].= 'paragraph line 3';
        $data['combination'][302]['expected'] = '<p>';
        $data['combination'][302]['expected'].=   'paragraph line 1';
        $data['combination'][302]['expected'].= '</p>';
        $data['combination'][302]['expected'].= '<div data-attribute="true">data</div>'.NL;
        $data['combination'][302]['expected'].= 'paragraph line 3';

        ##############################
        ### list with header (atx) ###
        ##############################

        $data['combination'][400]['data'] = '- list item 1'.NL;
        $data['combination'][400]['data'].= '# Title H1 (atx-style)'.NL;
        $data['combination'][400]['data'].= '- list item 2'.NL;
        $data['combination'][400]['data'].= '- list item 3';
        $data['combination'][400]['expected'] = '<ul>';
        $data['combination'][400]['expected'].=   '<li>list item 1</li>';
        $data['combination'][400]['expected'].= '</ul>';
        $data['combination'][400]['expected'].= '<h1>Title H1 (atx-style)</h1>';
        $data['combination'][400]['expected'].= '<ul>';
        $data['combination'][400]['expected'].=   '<li>list item 2</li>';
        $data['combination'][400]['expected'].=   '<li>list item 3</li>';
        $data['combination'][400]['expected'].= '</ul>';

        $data['combination'][401]['data'] = '- list item 1'.NL;
        $data['combination'][401]['data'].= '   # Title H1 (atx-style)'.NL;
        $data['combination'][401]['data'].= '- list item 2'.NL;
        $data['combination'][401]['data'].= '- list item 3';
        $data['combination'][401]['expected'] = '<ul>';
        $data['combination'][401]['expected'].=   '<li>list item 1';
        $data['combination'][401]['expected'].=     '<h1>Title H1 (atx-style)</h1>';
        $data['combination'][401]['expected'].=   '</li>';
        $data['combination'][401]['expected'].=   '<li>list item 2</li>';
        $data['combination'][401]['expected'].=   '<li>list item 3</li>';
        $data['combination'][401]['expected'].= '</ul>';

        #################################
        ### list with header (Setext) ###
        #################################

        $data['combination'][410]['data'] = '- list item 1'.NL;
        $data['combination'][410]['data'].= 'Title H1 (Setext-style)'.NL;
        $data['combination'][410]['data'].= '======================='.NL;
        $data['combination'][410]['data'].= '- list item 2'.NL;
        $data['combination'][410]['data'].= '- list item 3';
        $data['combination'][410]['expected'] = '<ul>';
        $data['combination'][410]['expected'].=   '<li>list item 1'.NL;
        $data['combination'][410]['expected'].=     'Title H1 (Setext-style)'.NL;
        $data['combination'][410]['expected'].=     '=======================</li>';
        $data['combination'][410]['expected'].=   '<li>list item 2</li>';
        $data['combination'][410]['expected'].=   '<li>list item 3</li>';
        $data['combination'][410]['expected'].= '</ul>';

        $data['combination'][411]['data'] = '- list item 1'.NL;
        $data['combination'][411]['data'].= '  Title H1 (Setext-style)'.NL;
        $data['combination'][411]['data'].= '  ======================='.NL;
        $data['combination'][411]['data'].= '- list item 2'.NL;
        $data['combination'][411]['data'].= '- list item 3';
        $data['combination'][411]['expected'] = '<ul>';
        $data['combination'][411]['expected'].=   '<li>list item 1'.NL;
        $data['combination'][411]['expected'].=     'Title H1 (Setext-style)'.NL;
        $data['combination'][411]['expected'].=     '=======================</li>';
        $data['combination'][411]['expected'].=   '<li>list item 2</li>';
        $data['combination'][411]['expected'].=   '<li>list item 3</li>';
        $data['combination'][411]['expected'].= '</ul>';

        ###########################
        ### list with paragraph ###
        ###########################

        $data['combination'][420]['data'] = '- list item 1'.NL;
        $data['combination'][420]['data'].= 'paragraph line 1'.NL;
        $data['combination'][420]['data'].= 'paragraph line 2'.NL;
        $data['combination'][420]['data'].= 'paragraph line 3'.NL;
        $data['combination'][420]['data'].= '- list item 2'.NL;
        $data['combination'][420]['data'].= '- list item 3';
        $data['combination'][420]['expected'] = '<ul>';
        $data['combination'][420]['expected'].=   '<li>list item 1'.NL;
        $data['combination'][420]['expected'].=     'paragraph line 1'.NL;
        $data['combination'][420]['expected'].=     'paragraph line 2'.NL;
        $data['combination'][420]['expected'].=     'paragraph line 3</li>';
        $data['combination'][420]['expected'].=   '<li>list item 2</li>';
        $data['combination'][420]['expected'].=   '<li>list item 3</li>';
        $data['combination'][420]['expected'].= '</ul>';

        $data['combination'][421]['data'] = '- list item 1'.NL;
        $data['combination'][421]['data'].= '          paragraph line 1'.NL;
        $data['combination'][421]['data'].= '          paragraph line 2'.NL;
        $data['combination'][421]['data'].= '          paragraph line 3'.NL;
        $data['combination'][421]['data'].= '- list item 2'.NL;
        $data['combination'][421]['data'].= '- list item 3';
        $data['combination'][421]['expected'] = '<ul>';
        $data['combination'][421]['expected'].=   '<li>list item 1'.NL;
        $data['combination'][421]['expected'].=     'paragraph line 1'.NL;
        $data['combination'][421]['expected'].=     'paragraph line 2'.NL;
        $data['combination'][421]['expected'].=     'paragraph line 3</li>';
        $data['combination'][421]['expected'].=   '<li>list item 2</li>';
        $data['combination'][421]['expected'].=   '<li>list item 3</li>';
        $data['combination'][421]['expected'].= '</ul>';

        $data['combination'][422]['data'] = '- list item 1'.NL;
        $data['combination'][422]['data'].= 'paragraph line 1'.NL;
        $data['combination'][422]['data'].= 'paragraph line 2'.NL;
        $data['combination'][422]['data'].= 'paragraph line 3'.NL;
        $data['combination'][422]['data'].= NL;
        $data['combination'][422]['data'].= '- list item 2'.NL;
        $data['combination'][422]['data'].= '- list item 3';
        $data['combination'][422]['expected'] = '<ul>';
        $data['combination'][422]['expected'].=   '<li>list item 1'.NL;
        $data['combination'][422]['expected'].=     'paragraph line 1'.NL;
        $data['combination'][422]['expected'].=     'paragraph line 2'.NL;
        $data['combination'][422]['expected'].=     'paragraph line 3</li>';
        $data['combination'][422]['expected'].=   '<li>list item 2</li>';
        $data['combination'][422]['expected'].=   '<li>list item 3</li>';
        $data['combination'][422]['expected'].= '</ul>';

        $data['combination'][423]['data'] = '- list item 1'.NL;
        $data['combination'][423]['data'].= '          paragraph line 1'.NL;
        $data['combination'][423]['data'].= '          paragraph line 2'.NL;
        $data['combination'][423]['data'].= '          paragraph line 3'.NL;
        $data['combination'][423]['data'].= NL;
        $data['combination'][423]['data'].= '- list item 2'.NL;
        $data['combination'][423]['data'].= '- list item 3';
        $data['combination'][423]['expected'] = '<ul>';
        $data['combination'][423]['expected'].=   '<li>list item 1'.NL;
        $data['combination'][423]['expected'].=     'paragraph line 1'.NL;
        $data['combination'][423]['expected'].=     'paragraph line 2'.NL;
        $data['combination'][423]['expected'].=     'paragraph line 3</li>';
        $data['combination'][423]['expected'].=   '<li>list item 2</li>';
        $data['combination'][423]['expected'].=   '<li>list item 3</li>';
        $data['combination'][423]['expected'].= '</ul>';

        $data['combination'][424]['data'] = '- list item 1'.NL;
        $data['combination'][424]['data'].= NL;
        $data['combination'][424]['data'].= 'paragraph line 1'.NL;
        $data['combination'][424]['data'].= 'paragraph line 2'.NL;
        $data['combination'][424]['data'].= 'paragraph line 3'.NL;
        $data['combination'][424]['data'].= '- list item 2'.NL;
        $data['combination'][424]['data'].= '- list item 3';
        $data['combination'][424]['expected'] = '<ul>';
        $data['combination'][424]['expected'].=   '<li>list item 1</li>';
        $data['combination'][424]['expected'].= '</ul>';
        $data['combination'][424]['expected'].= '<p>';
        $data['combination'][424]['expected'].=   'paragraph line 1'.NL;
        $data['combination'][424]['expected'].=   'paragraph line 2'.NL;
        $data['combination'][424]['expected'].=   'paragraph line 3';
        $data['combination'][424]['expected'].= '</p>';
        $data['combination'][424]['expected'].= '<ul>';
        $data['combination'][424]['expected'].=   '<li>list item 2</li>';
        $data['combination'][424]['expected'].=   '<li>list item 3</li>';
        $data['combination'][424]['expected'].= '</ul>';

        $data['combination'][425]['data'] = '- list item 1'.NL;
        $data['combination'][425]['data'].= NL;
        $data['combination'][425]['data'].= '  paragraph line 1'.NL;
        $data['combination'][425]['data'].= '  paragraph line 2'.NL;
        $data['combination'][425]['data'].= '  paragraph line 3'.NL;
        $data['combination'][425]['data'].= '- list item 2'.NL;
        $data['combination'][425]['data'].= '- list item 3';
        $data['combination'][425]['expected'] = '<ul>';
        $data['combination'][425]['expected'].=   '<li>list item 1';
        $data['combination'][425]['expected'].=     '<p>';
        $data['combination'][425]['expected'].=       'paragraph line 1'.NL;
        $data['combination'][425]['expected'].=       'paragraph line 2'.NL;
        $data['combination'][425]['expected'].=       'paragraph line 3';
        $data['combination'][425]['expected'].=     '</p>';
        $data['combination'][425]['expected'].=   '</li>';
        $data['combination'][425]['expected'].=   '<li>list item 2</li>';
        $data['combination'][425]['expected'].=   '<li>list item 3</li>';
        $data['combination'][425]['expected'].= '</ul>';

        $data['combination'][426]['data'] = '- list item 1'.NL;
        $data['combination'][426]['data'].= NL;
        $data['combination'][426]['data'].= '      paragraph line 1'.NL;
        $data['combination'][426]['data'].= '      paragraph line 2'.NL;
        $data['combination'][426]['data'].= '      paragraph line 3'.NL;
        $data['combination'][426]['data'].= '- list item 2'.NL;
        $data['combination'][426]['data'].= '- list item 3';
        $data['combination'][426]['expected'] = '<ul>';
        $data['combination'][426]['expected'].=   '<li>list item 1';
        $data['combination'][426]['expected'].=     '<pre>';
        $data['combination'][426]['expected'].=       '<code>';
        $data['combination'][426]['expected'].=         'paragraph line 1'.NL;
        $data['combination'][426]['expected'].=         'paragraph line 2'.NL;
        $data['combination'][426]['expected'].=         'paragraph line 3';
        $data['combination'][426]['expected'].=       '</code>';
        $data['combination'][426]['expected'].=     '</pre>';
        $data['combination'][426]['expected'].=   '</li>';
        $data['combination'][426]['expected'].=   '<li>list item 2</li>';
        $data['combination'][426]['expected'].=   '<li>list item 3</li>';
        $data['combination'][426]['expected'].= '</ul>';

        ######################
        ### list with code ###
        ######################

        $data['combination'][430]['data'] = '- list item 1'.NL;
        $data['combination'][430]['data'].= NL;
        $data['combination'][430]['data'].= '      code line 1'.NL;
        $data['combination'][430]['data'].= '           code line 2'.NL;
        $data['combination'][430]['data'].= '      code line 3'.NL;
        $data['combination'][430]['data'].= '- list item 2'.NL;
        $data['combination'][430]['data'].= '- list item 3';
        $data['combination'][430]['expected'] = '<ul>';
        $data['combination'][430]['expected'].=   '<li>list item 1';
        $data['combination'][430]['expected'].=     '<pre>';
        $data['combination'][430]['expected'].=       '<code>';
        $data['combination'][430]['expected'].=         'code line 1'.NL;
        $data['combination'][430]['expected'].=         '     code line 2'.NL;
        $data['combination'][430]['expected'].=         'code line 3';
        $data['combination'][430]['expected'].=       '</code>';
        $data['combination'][430]['expected'].=     '</pre>';
        $data['combination'][430]['expected'].=   '</li>';
        $data['combination'][430]['expected'].=   '<li>list item 2</li>';
        $data['combination'][430]['expected'].=   '<li>list item 3</li>';
        $data['combination'][430]['expected'].= '</ul>';

        ############################
        ### list with blockquote ###
        ############################

        $data['combination'][440]['data'] = '- list item 1'.NL;
        $data['combination'][440]['data'].= '> blockquote line 1'.NL;
        $data['combination'][440]['data'].= '> blockquote line 2'.NL;
        $data['combination'][440]['data'].= '> blockquote line 3'.NL;
        $data['combination'][440]['data'].= '- list item 2'.NL;
        $data['combination'][440]['data'].= '- list item 3';
        $data['combination'][440]['expected'] = '<ul>';
        $data['combination'][440]['expected'].=   '<li>list item 1</li>';
        $data['combination'][440]['expected'].= '</ul>';
        $data['combination'][440]['expected'].= '<blockquote>';
        $data['combination'][440]['expected'].=   '<p>';
        $data['combination'][440]['expected'].=     'blockquote line 1'.NL;
        $data['combination'][440]['expected'].=     'blockquote line 2'.NL;
        $data['combination'][440]['expected'].=     'blockquote line 3';
        $data['combination'][440]['expected'].=   '</p>';
        $data['combination'][440]['expected'].= '</blockquote>';
        $data['combination'][440]['expected'].= '<ul>';
        $data['combination'][440]['expected'].=   '<li>list item 2</li>';
        $data['combination'][440]['expected'].=   '<li>list item 3</li>';
        $data['combination'][440]['expected'].= '</ul>';

        $data['combination'][441]['data'] = '- list item 1'.NL;
        $data['combination'][441]['data'].= '  > blockquote line 1'.NL;
        $data['combination'][441]['data'].= '  > blockquote line 2'.NL;
        $data['combination'][441]['data'].= '  > blockquote line 3'.NL;
        $data['combination'][441]['data'].= '- list item 2'.NL;
        $data['combination'][441]['data'].= '- list item 3';
        $data['combination'][441]['expected'] = '<ul>';
        $data['combination'][441]['expected'].=   '<li>list item 1';
        $data['combination'][441]['expected'].=     '<blockquote>';
        $data['combination'][441]['expected'].=       '<p>';
        $data['combination'][441]['expected'].=         'blockquote line 1'.NL;
        $data['combination'][441]['expected'].=         'blockquote line 2'.NL;
        $data['combination'][441]['expected'].=         'blockquote line 3';
        $data['combination'][441]['expected'].=       '</p>';
        $data['combination'][441]['expected'].=     '</blockquote>';
        $data['combination'][441]['expected'].=   '</li>';
        $data['combination'][441]['expected'].=   '<li>list item 2</li>';
        $data['combination'][441]['expected'].=   '<li>list item 3</li>';
        $data['combination'][441]['expected'].= '</ul>';

        $data['combination'][442]['data'] = '- list item 1'.NL;
        $data['combination'][442]['data'].= '      > blockquote line 1'.NL;
        $data['combination'][442]['data'].= '        > blockquote line 2'.NL;
        $data['combination'][442]['data'].= '      > blockquote line 3'.NL;
        $data['combination'][442]['data'].= '- list item 2'.NL;
        $data['combination'][442]['data'].= '- list item 3';
        $data['combination'][442]['expected'] = '<ul>';
        $data['combination'][442]['expected'].=   '<li>list item 1'.NL;
        $data['combination'][442]['expected'].=     '&gt; blockquote line 1'.NL;
        $data['combination'][442]['expected'].=     '  &gt; blockquote line 2'.NL;
        $data['combination'][442]['expected'].=     '&gt; blockquote line 3</li>';
        $data['combination'][442]['expected'].=   '<li>list item 2</li>';
        $data['combination'][442]['expected'].=   '<li>list item 3</li>';
        $data['combination'][442]['expected'].= '</ul>';

        $data['combination'][443]['data'] = '- list item 1'.NL;
        $data['combination'][443]['data'].= NL;
        $data['combination'][443]['data'].= '      > blockquote line 1'.NL;
        $data['combination'][443]['data'].= '        > blockquote line 2'.NL;
        $data['combination'][443]['data'].= '      > blockquote line 3'.NL;
        $data['combination'][443]['data'].= '- list item 2'.NL;
        $data['combination'][443]['data'].= '- list item 3';
        $data['combination'][443]['expected'] = '<ul>';
        $data['combination'][443]['expected'].=   '<li>list item 1';
        $data['combination'][443]['expected'].=     '<pre>';
        $data['combination'][443]['expected'].=       '<code>';
        $data['combination'][443]['expected'].=         '&gt; blockquote line 1'.NL;
        $data['combination'][443]['expected'].=         '  &gt; blockquote line 2'.NL;
        $data['combination'][443]['expected'].=         '&gt; blockquote line 3';
        $data['combination'][443]['expected'].=       '</code>';
        $data['combination'][443]['expected'].=     '</pre>';
        $data['combination'][443]['expected'].=   '</li>';
        $data['combination'][443]['expected'].=   '<li>list item 2</li>';
        $data['combination'][443]['expected'].=   '<li>list item 3</li>';
        $data['combination'][443]['expected'].= '</ul>';

        ####################
        ### list with hr ###
        ####################

        $data['combination'][450]['data'] = '- list item 1'.NL;
        $data['combination'][450]['data'].= '*  *  *'.NL;
        $data['combination'][450]['data'].= '- list item 2'.NL;
        $data['combination'][450]['data'].= '- list item 3';
        $data['combination'][450]['expected'] = '<ul>';
        $data['combination'][450]['expected'].=   '<li>list item 1</li>';
        $data['combination'][450]['expected'].= '</ul>';
        $data['combination'][450]['expected'].= '<hr>';
        $data['combination'][450]['expected'].= '<ul>';
        $data['combination'][450]['expected'].=   '<li>list item 2</li>';
        $data['combination'][450]['expected'].=   '<li>list item 3</li>';
        $data['combination'][450]['expected'].= '</ul>';

        $data['combination'][451]['data'] = '- list item 1'.NL;
        $data['combination'][451]['data'].= '-  -  -'.NL;
        $data['combination'][451]['data'].= '- list item 2'.NL;
        $data['combination'][451]['data'].= '- list item 3';
        $data['combination'][451]['expected'] = '<ul>';
        $data['combination'][451]['expected'].=   '<li>list item 1</li>';
        $data['combination'][451]['expected'].= '</ul>';
        $data['combination'][451]['expected'].= '<hr>';
        $data['combination'][451]['expected'].= '<ul>';
        $data['combination'][451]['expected'].=   '<li>list item 2</li>';
        $data['combination'][451]['expected'].=   '<li>list item 3</li>';
        $data['combination'][451]['expected'].= '</ul>';

        $data['combination'][452]['data'] = '- list item 1'.NL;
        $data['combination'][452]['data'].= '  *  *  *'.NL;
        $data['combination'][452]['data'].= '- list item 2'.NL;
        $data['combination'][452]['data'].= '- list item 3';
        $data['combination'][452]['expected'] = '<ul>';
        $data['combination'][452]['expected'].=   '<li>list item 1';
        $data['combination'][452]['expected'].=     '<hr>';
        $data['combination'][452]['expected'].=   '</li>';
        $data['combination'][452]['expected'].=   '<li>list item 2</li>';
        $data['combination'][452]['expected'].=   '<li>list item 3</li>';
        $data['combination'][452]['expected'].= '</ul>';

        $data['combination'][453]['data'] = '- list item 1'.NL;
        $data['combination'][453]['data'].= '  -  -  -'.NL;
        $data['combination'][453]['data'].= '- list item 2'.NL;
        $data['combination'][453]['data'].= '- list item 3';
        $data['combination'][453]['expected'] = '<ul>';
        $data['combination'][453]['expected'].=   '<li>list item 1';
        $data['combination'][453]['expected'].=     '<hr>';
        $data['combination'][453]['expected'].=   '</li>';
        $data['combination'][453]['expected'].=   '<li>list item 2</li>';
        $data['combination'][453]['expected'].=   '<li>list item 3</li>';
        $data['combination'][453]['expected'].= '</ul>';

        $data['combination'][454]['data'] = '- list item 1'.NL;
        $data['combination'][454]['data'].= '      *  *  *'.NL;
        $data['combination'][454]['data'].= '- list item 2'.NL;
        $data['combination'][454]['data'].= '- list item 3';
        $data['combination'][454]['expected'] = '<ul>';
        $data['combination'][454]['expected'].=   '<li>list item 1'.NL;
        $data['combination'][454]['expected'].=     '&#42;  &#42;  &#42;';
        $data['combination'][454]['expected'].=   '</li>';
        $data['combination'][454]['expected'].=   '<li>list item 2</li>';
        $data['combination'][454]['expected'].=   '<li>list item 3</li>';
        $data['combination'][454]['expected'].= '</ul>';

        $data['combination'][455]['data'] = '- list item 1'.NL;
        $data['combination'][455]['data'].= '      -  -  -'.NL;
        $data['combination'][455]['data'].= '- list item 2'.NL;
        $data['combination'][455]['data'].= '- list item 3';
        $data['combination'][455]['expected'] = '<ul>';
        $data['combination'][455]['expected'].=   '<li>list item 1'.NL;
        $data['combination'][455]['expected'].=     '&#45;  &#45;  &#45;';
        $data['combination'][455]['expected'].=   '</li>';
        $data['combination'][455]['expected'].=   '<li>list item 2</li>';
        $data['combination'][455]['expected'].=   '<li>list item 3</li>';
        $data['combination'][455]['expected'].= '</ul>';

        $data['combination'][456]['data'] = '- list item 1'.NL;
        $data['combination'][456]['data'].= NL;
        $data['combination'][456]['data'].= '      *  *  *'.NL;
        $data['combination'][456]['data'].= '- list item 2'.NL;
        $data['combination'][456]['data'].= '- list item 3';
        $data['combination'][456]['expected'] = '<ul>';
        $data['combination'][456]['expected'].=   '<li>list item 1';
        $data['combination'][456]['expected'].=     '<pre>';
        $data['combination'][456]['expected'].=       '<code>';
        $data['combination'][456]['expected'].=         '*  *  *';
        $data['combination'][456]['expected'].=       '</code>';
        $data['combination'][456]['expected'].=     '</pre>';
        $data['combination'][456]['expected'].=   '</li>';
        $data['combination'][456]['expected'].=   '<li>list item 2</li>';
        $data['combination'][456]['expected'].=   '<li>list item 3</li>';
        $data['combination'][456]['expected'].= '</ul>';

        $data['combination'][457]['data'] = '- list item 1'.NL;
        $data['combination'][457]['data'].= NL;
        $data['combination'][457]['data'].= '      -  -  -'.NL;
        $data['combination'][457]['data'].= '- list item 2'.NL;
        $data['combination'][457]['data'].= '- list item 3';
        $data['combination'][457]['expected'] = '<ul>';
        $data['combination'][457]['expected'].=   '<li>list item 1';
        $data['combination'][457]['expected'].=     '<pre>';
        $data['combination'][457]['expected'].=       '<code>';
        $data['combination'][457]['expected'].=         '-  -  -';
        $data['combination'][457]['expected'].=       '</code>';
        $data['combination'][457]['expected'].=     '</pre>';
        $data['combination'][457]['expected'].=   '</li>';
        $data['combination'][457]['expected'].=   '<li>list item 2</li>';
        $data['combination'][457]['expected'].=   '<li>list item 3</li>';
        $data['combination'][457]['expected'].= '</ul>';

        ########################
        ### list with markup ###
        ########################

        $data['combination'][460]['data'] = '- list item 1'.NL;
        $data['combination'][460]['data'].= '<a href="http://example.com">link</a>'.NL;
        $data['combination'][460]['data'].= '- list item 2'.NL;
        $data['combination'][460]['data'].= '- list item 3';
        $data['combination'][460]['expected'] = '<ul>';
        $data['combination'][460]['expected'].=   '<li>list item 1'.NL;
        $data['combination'][460]['expected'].=     '<a href="http://example.com">link</a>';
        $data['combination'][460]['expected'].=   '</li>';
        $data['combination'][460]['expected'].=   '<li>list item 2</li>';
        $data['combination'][460]['expected'].=   '<li>list item 3</li>';
        $data['combination'][460]['expected'].= '</ul>';

        $data['combination'][461]['data'] = '- list item 1'.NL;
        $data['combination'][461]['data'].= '<x-tag data-attribute="true">data</x-tag>'.NL;
        $data['combination'][461]['data'].= '- list item 2'.NL;
        $data['combination'][461]['data'].= '- list item 3';
        $data['combination'][461]['expected'] = '<ul>';
        $data['combination'][461]['expected'].=   '<li>list item 1'.NL;
        $data['combination'][461]['expected'].=     '<x-tag data-attribute="true">data</x-tag>';
        $data['combination'][461]['expected'].=   '</li>';
        $data['combination'][461]['expected'].=   '<li>list item 2</li>';
        $data['combination'][461]['expected'].=   '<li>list item 3</li>';
        $data['combination'][461]['expected'].= '</ul>';

        $data['combination'][462]['data'] = '- list item 1'.NL;
        $data['combination'][462]['data'].= '<div data-attribute="true">data</div>'.NL;
        $data['combination'][462]['data'].= '- list item 2'.NL;
        $data['combination'][462]['data'].= '- list item 3';
        $data['combination'][462]['expected'] = '<ul>';
        $data['combination'][462]['expected'].=   '<li>list item 1</li>';
        $data['combination'][462]['expected'].= '</ul>';
        $data['combination'][462]['expected'].= '<div data-attribute="true">data</div>'.NL;
        $data['combination'][462]['expected'].= '- list item 2'.NL;
        $data['combination'][462]['expected'].= '- list item 3';

        $data['combination'][463]['data'] = '- list item 1'.NL;
        $data['combination'][463]['data'].= '  <a href="http://example.com">link</a>'.NL;
        $data['combination'][463]['data'].= '- list item 2'.NL;
        $data['combination'][463]['data'].= '- list item 3';
        $data['combination'][463]['expected'] = '<ul>';
        $data['combination'][463]['expected'].=   '<li>list item 1'.NL;
        $data['combination'][463]['expected'].=     '<a href="http://example.com">link</a>';
        $data['combination'][463]['expected'].=   '</li>';
        $data['combination'][463]['expected'].=   '<li>list item 2</li>';
        $data['combination'][463]['expected'].=   '<li>list item 3</li>';
        $data['combination'][463]['expected'].= '</ul>';

        $data['combination'][464]['data'] = '- list item 1'.NL;
        $data['combination'][464]['data'].= '  <x-tag data-attribute="true">data</x-tag>'.NL;
        $data['combination'][464]['data'].= '- list item 2'.NL;
        $data['combination'][464]['data'].= '- list item 3';
        $data['combination'][464]['expected'] = '<ul>';
        $data['combination'][464]['expected'].=   '<li>list item 1'.NL;
        $data['combination'][464]['expected'].=     '<x-tag data-attribute="true">data</x-tag>';
        $data['combination'][464]['expected'].=   '</li>';
        $data['combination'][464]['expected'].=   '<li>list item 2</li>';
        $data['combination'][464]['expected'].=   '<li>list item 3</li>';
        $data['combination'][464]['expected'].= '</ul>';

        $data['combination'][465]['data'] = '- list item 1'.NL;
        $data['combination'][465]['data'].= '  <div data-attribute="true">data</div>'.NL;
        $data['combination'][465]['data'].= '- list item 2'.NL;
        $data['combination'][465]['data'].= '- list item 3';
        $data['combination'][465]['expected'] = '<ul>';
        $data['combination'][465]['expected'].=   '<li>list item 1</li>';
        $data['combination'][465]['expected'].= '</ul>';
        $data['combination'][465]['expected'].= '  <div data-attribute="true">data</div>'.NL;
        $data['combination'][465]['expected'].= '- list item 2'.NL;
        $data['combination'][465]['expected'].= '- list item 3';

        $data['combination'][466]['data'] = '- list item 1'.NL;
        $data['combination'][466]['data'].= '      <a href="http://example.com">link</a>'.NL;
        $data['combination'][466]['data'].= '- list item 2'.NL;
        $data['combination'][466]['data'].= '- list item 3';
        $data['combination'][466]['expected'] = '<ul>';
        $data['combination'][466]['expected'].=   '<li>list item 1'.NL;
        $data['combination'][466]['expected'].=     '<a href="http://example.com">link</a>';
        $data['combination'][466]['expected'].=   '</li>';
        $data['combination'][466]['expected'].=   '<li>list item 2</li>';
        $data['combination'][466]['expected'].=   '<li>list item 3</li>';
        $data['combination'][466]['expected'].= '</ul>';

        $data['combination'][467]['data'] = '- list item 1'.NL;
        $data['combination'][467]['data'].= '      <x-tag data-attribute="true">data</x-tag>'.NL;
        $data['combination'][467]['data'].= '- list item 2'.NL;
        $data['combination'][467]['data'].= '- list item 3';
        $data['combination'][467]['expected'] = '<ul>';
        $data['combination'][467]['expected'].=   '<li>list item 1'.NL;
        $data['combination'][467]['expected'].=     '<x-tag data-attribute="true">data</x-tag>';
        $data['combination'][467]['expected'].=   '</li>';
        $data['combination'][467]['expected'].=   '<li>list item 2</li>';
        $data['combination'][467]['expected'].=   '<li>list item 3</li>';
        $data['combination'][467]['expected'].= '</ul>';

        $data['combination'][468]['data'] = '- list item 1'.NL;
        $data['combination'][468]['data'].= '      <div data-attribute="true">data</div>'.NL;
        $data['combination'][468]['data'].= '- list item 2'.NL;
        $data['combination'][468]['data'].= '- list item 3';
        $data['combination'][468]['expected'] = '<ul>';
        $data['combination'][468]['expected'].=   '<li>list item 1</li>';
        $data['combination'][468]['expected'].= '</ul>';
        $data['combination'][468]['expected'].= '      <div data-attribute="true">data</div>'.NL;
        $data['combination'][468]['expected'].= '- list item 2'.NL;
        $data['combination'][468]['expected'].= '- list item 3';

        $data['combination'][469]['data'] = '- list item 1'.NL;
        $data['combination'][469]['data'].= NL;
        $data['combination'][469]['data'].= '<a href="http://example.com">link</a>'.NL;
        $data['combination'][469]['data'].= '- list item 2'.NL;
        $data['combination'][469]['data'].= '- list item 3';
        $data['combination'][469]['expected'] = '<ul>';
        $data['combination'][469]['expected'].=   '<li>list item 1</li>';
        $data['combination'][469]['expected'].= '</ul>';
        $data['combination'][469]['expected'].= '<p>';
        $data['combination'][469]['expected'].=   '<a href="http://example.com">link</a>';
        $data['combination'][469]['expected'].= '</p>';
        $data['combination'][469]['expected'].= '<ul>';
        $data['combination'][469]['expected'].=   '<li>list item 2</li>';
        $data['combination'][469]['expected'].=   '<li>list item 3</li>';
        $data['combination'][469]['expected'].= '</ul>';

        $data['combination'][470]['data'] = '- list item 1'.NL;
        $data['combination'][470]['data'].= NL;
        $data['combination'][470]['data'].= '<x-tag data-attribute="true">data</x-tag>'.NL;
        $data['combination'][470]['data'].= '- list item 2'.NL;
        $data['combination'][470]['data'].= '- list item 3';
        $data['combination'][470]['expected'] = '<ul>';
        $data['combination'][470]['expected'].=   '<li>list item 1</li>';
        $data['combination'][470]['expected'].= '</ul>';
        $data['combination'][470]['expected'].= '<p>';
        $data['combination'][470]['expected'].=   '<x-tag data-attribute="true">data</x-tag>';
        $data['combination'][470]['expected'].= '</p>';
        $data['combination'][470]['expected'].= '<ul>';
        $data['combination'][470]['expected'].=   '<li>list item 2</li>';
        $data['combination'][470]['expected'].=   '<li>list item 3</li>';
        $data['combination'][470]['expected'].= '</ul>';

        $data['combination'][471]['data'] = '- list item 1'.NL;
        $data['combination'][471]['data'].= NL;
        $data['combination'][471]['data'].= '<div data-attribute="true">data</div>'.NL;
        $data['combination'][471]['data'].= '- list item 2'.NL;
        $data['combination'][471]['data'].= '- list item 3';
        $data['combination'][471]['expected'] = '<ul>';
        $data['combination'][471]['expected'].=   '<li>list item 1</li>';
        $data['combination'][471]['expected'].= '</ul>';
        $data['combination'][471]['expected'].= '<div data-attribute="true">data</div>'.NL;
        $data['combination'][471]['expected'].= '- list item 2'.NL;
        $data['combination'][471]['expected'].= '- list item 3';

        $data['combination'][472]['data'] = '- list item 1'.NL;
        $data['combination'][472]['data'].= NL;
        $data['combination'][472]['data'].= '  <a href="http://example.com">link</a>'.NL;
        $data['combination'][472]['data'].= '- list item 2'.NL;
        $data['combination'][472]['data'].= '- list item 3';
        $data['combination'][472]['expected'] = '<ul>';
        $data['combination'][472]['expected'].=   '<li>list item 1';
        $data['combination'][472]['expected'].= '<p>';
        $data['combination'][472]['expected'].=   '<a href="http://example.com">link</a>';
        $data['combination'][472]['expected'].= '</p>';
        $data['combination'][472]['expected'].= '</li>';
        $data['combination'][472]['expected'].=   '<li>list item 2</li>';
        $data['combination'][472]['expected'].=   '<li>list item 3</li>';
        $data['combination'][472]['expected'].= '</ul>';

        $data['combination'][473]['data'] = '- list item 1'.NL;
        $data['combination'][473]['data'].= NL;
        $data['combination'][473]['data'].= '  <x-tag data-attribute="true">data</x-tag>'.NL;
        $data['combination'][473]['data'].= '- list item 2'.NL;
        $data['combination'][473]['data'].= '- list item 3';
        $data['combination'][473]['expected'] = '<ul>';
        $data['combination'][473]['expected'].=   '<li>list item 1';
        $data['combination'][473]['expected'].=     '<p>';
        $data['combination'][473]['expected'].=       '<x-tag data-attribute="true">data</x-tag>';
        $data['combination'][473]['expected'].=     '</p>';
        $data['combination'][473]['expected'].=   '</li>';
        $data['combination'][473]['expected'].=   '<li>list item 2</li>';
        $data['combination'][473]['expected'].=   '<li>list item 3</li>';
        $data['combination'][473]['expected'].= '</ul>';

        $data['combination'][474]['data'] = '- list item 1'.NL;
        $data['combination'][474]['data'].= NL;
        $data['combination'][474]['data'].= '  <div data-attribute="true">data</div>'.NL;
        $data['combination'][474]['data'].= '- list item 2'.NL;
        $data['combination'][474]['data'].= '- list item 3';
        $data['combination'][474]['expected'] = '<ul>';
        $data['combination'][474]['expected'].=   '<li>list item 1</li>';
        $data['combination'][474]['expected'].= '</ul>';
        $data['combination'][474]['expected'].= '  <div data-attribute="true">data</div>'.NL;
        $data['combination'][474]['expected'].= '- list item 2'.NL;
        $data['combination'][474]['expected'].= '- list item 3';

        $data['combination'][475]['data'] = '- list item 1'.NL;
        $data['combination'][475]['data'].= NL;
        $data['combination'][475]['data'].= '      <a href="http://example.com">link</a>'.NL;
        $data['combination'][475]['data'].= '- list item 2'.NL;
        $data['combination'][475]['data'].= '- list item 3';
        $data['combination'][475]['expected'] = '<ul>';
        $data['combination'][475]['expected'].=   '<li>list item 1';
        $data['combination'][475]['expected'].=     '<pre>';
        $data['combination'][475]['expected'].=       '<code>';
        $data['combination'][475]['expected'].=         '&lt;a href=&quot;http://example.com&quot;&gt;link&lt;/a&gt;';
        $data['combination'][475]['expected'].=       '</code>';
        $data['combination'][475]['expected'].=     '</pre>';
        $data['combination'][475]['expected'].=   '</li>';
        $data['combination'][475]['expected'].=   '<li>list item 2</li>';
        $data['combination'][475]['expected'].=   '<li>list item 3</li>';
        $data['combination'][475]['expected'].= '</ul>';

        $data['combination'][476]['data'] = '- list item 1'.NL;
        $data['combination'][476]['data'].= NL;
        $data['combination'][476]['data'].= '      <x-tag data-attribute="true">data</x-tag>'.NL;
        $data['combination'][476]['data'].= '- list item 2'.NL;
        $data['combination'][476]['data'].= '- list item 3';
        $data['combination'][476]['expected'] = '<ul>';
        $data['combination'][476]['expected'].=   '<li>list item 1';
        $data['combination'][476]['expected'].=     '<pre>';
        $data['combination'][476]['expected'].=       '<code>';
        $data['combination'][476]['expected'].=         '&lt;x-tag data-attribute=&quot;true&quot;&gt;data&lt;/x-tag&gt;';
        $data['combination'][476]['expected'].=       '</code>';
        $data['combination'][476]['expected'].=     '</pre>';
        $data['combination'][476]['expected'].=   '</li>';
        $data['combination'][476]['expected'].=   '<li>list item 2</li>';
        $data['combination'][476]['expected'].=   '<li>list item 3</li>';
        $data['combination'][476]['expected'].= '</ul>';

        $data['combination'][477]['data'] = '- list item 1'.NL;
        $data['combination'][477]['data'].= NL;
        $data['combination'][477]['data'].= '      <div data-attribute="true">data</div>'.NL;
        $data['combination'][477]['data'].= '- list item 2'.NL;
        $data['combination'][477]['data'].= '- list item 3';
        $data['combination'][477]['expected'] = '<ul>';
        $data['combination'][477]['expected'].=   '<li>list item 1</li>';
        $data['combination'][477]['expected'].= '</ul>';
        $data['combination'][477]['expected'].= '      <div data-attribute="true">data</div>'.NL;
        $data['combination'][477]['expected'].= '- list item 2'.NL;
        $data['combination'][477]['expected'].= '- list item 3';

        ##############################
        ### code with header (atx) ###
        ##############################

        $data['combination'][500]['data'] = '    # Title H1 (atx-style)';
        $data['combination'][500]['expected'] = '<pre>';
        $data['combination'][500]['expected'].=   '<code>';
        $data['combination'][500]['expected'].=     '# Title H1 (atx-style)';
        $data['combination'][500]['expected'].=   '</code>';
        $data['combination'][500]['expected'].= '</pre>';

        #################################
        ### code with header (Setext) ###
        #################################

        $data['combination'][510]['data'] = '    Title H1 (Setext-style)'.NL;
        $data['combination'][510]['data'].= '    =======================';
        $data['combination'][510]['expected'] = '<pre>';
        $data['combination'][510]['expected'].=   '<code>';
        $data['combination'][510]['expected'].=     'Title H1 (Setext-style)'.NL;
        $data['combination'][510]['expected'].=     '=======================';
        $data['combination'][510]['expected'].=   '</code>';
        $data['combination'][510]['expected'].= '</pre>';

        ######################
        ### code with list ###
        ######################

        $data['combination'][520]['data'] = '    - list item 1'.NL;
        $data['combination'][520]['data'].= '    - list item 2'.NL;
        $data['combination'][520]['data'].= '    - list item 3';
        $data['combination'][520]['expected'] = '<pre>';
        $data['combination'][520]['expected'].=   '<code>';
        $data['combination'][520]['expected'].=     '- list item 1'.NL;
        $data['combination'][520]['expected'].=     '- list item 2'.NL;
        $data['combination'][520]['expected'].=     '- list item 3';
        $data['combination'][520]['expected'].=   '</code>';
        $data['combination'][520]['expected'].= '</pre>';

        ############################
        ### code with blockquote ###
        ############################

        $data['combination'][530]['data'] = '    > blockquote line 1'.NL;
        $data['combination'][530]['data'].= '    > blockquote line 2'.NL;
        $data['combination'][530]['data'].= '    > blockquote line 3';
        $data['combination'][530]['expected'] = '<pre>';
        $data['combination'][530]['expected'].=   '<code>';
        $data['combination'][530]['expected'].=     '&gt; blockquote line 1'.NL;
        $data['combination'][530]['expected'].=     '&gt; blockquote line 2'.NL;
        $data['combination'][530]['expected'].=     '&gt; blockquote line 3';
        $data['combination'][530]['expected'].=   '</code>';
        $data['combination'][530]['expected'].= '</pre>';

        ####################
        ### code with hr ###
        ####################

        $data['combination'][540]['data'] = '    *  *  *';
        $data['combination'][540]['expected'] = '<pre>';
        $data['combination'][540]['expected'].=   '<code>';
        $data['combination'][540]['expected'].=     '*  *  *';
        $data['combination'][540]['expected'].=   '</code>';
        $data['combination'][540]['expected'].= '</pre>';

        $data['combination'][541]['data'] = '    -  -  -';
        $data['combination'][541]['expected'] = '<pre>';
        $data['combination'][541]['expected'].=   '<code>';
        $data['combination'][541]['expected'].=     '-  -  -';
        $data['combination'][541]['expected'].=   '</code>';
        $data['combination'][541]['expected'].= '</pre>';

        ########################
        ### code with markup ###
        ########################

        $data['combination'][550]['data'] = '    code line 1'.NL;
        $data['combination'][550]['data'].= '    <a href="http://example.com">link</a>'.NL;
        $data['combination'][550]['data'].= '    code line 3';
        $data['combination'][550]['expected'] = '<pre>';
        $data['combination'][550]['expected'].=   '<code>';
        $data['combination'][550]['expected'].=     'code line 1'.NL;
        $data['combination'][550]['expected'].=     '&lt;a href=&quot;http://example.com&quot;&gt;link&lt;/a&gt;'.NL;
        $data['combination'][550]['expected'].=     'code line 3';
        $data['combination'][550]['expected'].=   '</code>';
        $data['combination'][550]['expected'].= '</pre>';

        $data['combination'][551]['data'] = '    code line 1'.NL;
        $data['combination'][551]['data'].= '    <x-tag data-attribute="true">data</x-tag>'.NL;
        $data['combination'][551]['data'].= '    code line 3';
        $data['combination'][551]['expected'] = '<pre>';
        $data['combination'][551]['expected'].=   '<code>';
        $data['combination'][551]['expected'].=     'code line 1'.NL;
        $data['combination'][551]['expected'].=     '&lt;x-tag data-attribute=&quot;true&quot;&gt;data&lt;/x-tag&gt;'.NL;
        $data['combination'][551]['expected'].=     'code line 3';
        $data['combination'][551]['expected'].=   '</code>';
        $data['combination'][551]['expected'].= '</pre>';

        $data['combination'][552]['data'] = '    code line 1'.NL;
        $data['combination'][552]['data'].= '    <div data-attribute="true">data</div>'.NL;
        $data['combination'][552]['data'].= '    code line 3';
        $data['combination'][552]['expected'] = '<pre>';
        $data['combination'][552]['expected'].=   '<code>';
        $data['combination'][552]['expected'].=     'code line 1'.NL;
        $data['combination'][552]['expected'].=     '&lt;div data-attribute=&quot;true&quot;&gt;data&lt;/div&gt;'.NL;
        $data['combination'][552]['expected'].=     'code line 3';
        $data['combination'][552]['expected'].=   '</code>';
        $data['combination'][552]['expected'].= '</pre>';

        ####################################
        ### blockquote with header (atx) ###
        ####################################

        $data['combination'][600]['data'] = '> # Title H1 (atx-style)';
        $data['combination'][600]['expected'] = '<blockquote>';
        $data['combination'][600]['expected'].=   '<h1>Title H1 (atx-style)</h1>';
        $data['combination'][600]['expected'].= '</blockquote>';

        #######################################
        ### blockquote with header (Setext) ###
        #######################################

        $data['combination'][610]['data'] = '> Title H1 (Setext-style)'.NL;
        $data['combination'][610]['data'].= '> =======================';
        $data['combination'][610]['expected'] = '<blockquote>';
        $data['combination'][610]['expected'].=   '<h1>Title H1 (Setext-style)</h1>';
        $data['combination'][610]['expected'].= '</blockquote>';

        #####################################
        ### blockquote with bulleted list ###
        #####################################

        $data['combination'][620]['data'] = '> - list item 1'.NL;
        $data['combination'][620]['data'].= '>   - list item 1.1'.NL;
        $data['combination'][620]['data'].= '>     - list item 1.1.1'.NL;
        $data['combination'][620]['data'].= '>   - list item 1.2'.NL;
        $data['combination'][620]['data'].= '> - list item 2';
        $data['combination'][620]['expected'] = '<blockquote>';
        $data['combination'][620]['expected'].=   '<ul>';
        $data['combination'][620]['expected'].=     '<li>list item 1';
        $data['combination'][620]['expected'].=       '<ul>';
        $data['combination'][620]['expected'].=         '<li>list item 1.1';
        $data['combination'][620]['expected'].=           '<ul>';
        $data['combination'][620]['expected'].=             '<li>list item 1.1.1</li>';
        $data['combination'][620]['expected'].=           '</ul>';
        $data['combination'][620]['expected'].=         '</li>';
        $data['combination'][620]['expected'].=         '<li>list item 1.2</li>';
        $data['combination'][620]['expected'].=       '</ul>';
        $data['combination'][620]['expected'].=     '</li>';
        $data['combination'][620]['expected'].=     '<li>list item 2</li>';
        $data['combination'][620]['expected'].=   '</ul>';
        $data['combination'][620]['expected'].= '</blockquote>';

        #####################################
        ### blockquote with numbered list ###
        #####################################

        $data['combination'][630]['data'] = '> 1. numbered list item 1'.NL;
        $data['combination'][630]['data'].= '> 2. numbered list item 2'.NL;
        $data['combination'][630]['data'].= '> 3. numbered list item 3';
        $data['combination'][630]['expected'] = '<blockquote>';
        $data['combination'][630]['expected'].=   '<ol>';
        $data['combination'][630]['expected'].=     '<li>numbered list item 1</li>';
        $data['combination'][630]['expected'].=     '<li>numbered list item 2</li>';
        $data['combination'][630]['expected'].=     '<li>numbered list item 3</li>';
        $data['combination'][630]['expected'].=   '</ol>';
        $data['combination'][630]['expected'].= '</blockquote>';

        ############################
        ### blockquote with code ###
        ############################

        $data['combination'][640]['data'] = '>     code line 1'.NL;
        $data['combination'][640]['data'].= '>          code line 2'.NL;
        $data['combination'][640]['data'].= '>     code line 3';
        $data['combination'][640]['expected'] = '<blockquote>';
        $data['combination'][640]['expected'].=   '<pre>';
        $data['combination'][640]['expected'].=     '<code>';
        $data['combination'][640]['expected'].=       'code line 1'.NL;
        $data['combination'][640]['expected'].=       '     code line 2'.NL;
        $data['combination'][640]['expected'].=       'code line 3';
        $data['combination'][640]['expected'].=     '</code>';
        $data['combination'][640]['expected'].=   '</pre>';
        $data['combination'][640]['expected'].= '</blockquote>';

        ##################################
        ### blockquote with blockquote ###
        ##################################

        $data['combination'][650]['data'] = '> blockquote line 1'.NL;
        $data['combination'][650]['data'].= '> > blockquote nested line 2'.NL;
        $data['combination'][650]['data'].= '> blockquote line 3';
        $data['combination'][650]['expected'] = '<blockquote>';
        $data['combination'][650]['expected'].=   '<p>';
        $data['combination'][650]['expected'].=     'blockquote line 1';
        $data['combination'][650]['expected'].=   '</p>';
        $data['combination'][650]['expected'].=   '<blockquote>';
        $data['combination'][650]['expected'].=     '<p>';
        $data['combination'][650]['expected'].=         'blockquote nested line 2'.NL;
        $data['combination'][650]['expected'].=       'blockquote line 3';
        $data['combination'][650]['expected'].=     '</p>';
        $data['combination'][650]['expected'].=   '</blockquote>';
        $data['combination'][650]['expected'].= '</blockquote>';

        ##########################
        ### blockquote with hr ###
        ##########################

        $data['combination'][660]['data'] = '> *  *  *';
        $data['combination'][660]['expected'] = '<blockquote>';
        $data['combination'][660]['expected'].=   '<hr>';
        $data['combination'][660]['expected'].= '</blockquote>';

        $data['combination'][661]['data'] = '> -  -  -';
        $data['combination'][661]['expected'] = '<blockquote>';
        $data['combination'][661]['expected'].=   '<hr>';
        $data['combination'][661]['expected'].= '</blockquote>';

        ##############################
        ### blockquote with markup ###
        ##############################

        $data['combination'][670]['data'] = '> blockquote line 1'.NL;
        $data['combination'][670]['data'].= '> <a href="http://example.com">link</a>'.NL;
        $data['combination'][670]['data'].= '> blockquote line 3';
        $data['combination'][670]['expected'] = '<blockquote>';
        $data['combination'][670]['expected'].=   '<p>';
        $data['combination'][670]['expected'].=     'blockquote line 1'.NL;
        $data['combination'][670]['expected'].=     '<a href="http://example.com">link</a>'.NL;
        $data['combination'][670]['expected'].=     'blockquote line 3';
        $data['combination'][670]['expected'].=   '</p>';
        $data['combination'][670]['expected'].= '</blockquote>';

        $data['combination'][671]['data'] = '> blockquote line 1'.NL;
        $data['combination'][671]['data'].= '> <x-tag data-attribute="true">data</x-tag>'.NL;
        $data['combination'][671]['data'].= '> blockquote line 3';
        $data['combination'][671]['expected'] = '<blockquote>';
        $data['combination'][671]['expected'].=   '<p>';
        $data['combination'][671]['expected'].=     'blockquote line 1'.NL;
        $data['combination'][671]['expected'].=     '<x-tag data-attribute="true">data</x-tag>'.NL;
        $data['combination'][671]['expected'].=     'blockquote line 3';
        $data['combination'][671]['expected'].=   '</p>';
        $data['combination'][671]['expected'].= '</blockquote>';

        $data['combination'][672]['data'] = '> blockquote line 1'.NL;
        $data['combination'][672]['data'].= '> <div data-attribute="true">data</div>'.NL;
        $data['combination'][672]['data'].= '> blockquote line 3';
        $data['combination'][672]['expected'] = '<blockquote>';
        $data['combination'][672]['expected'].=   '<p>';
        $data['combination'][672]['expected'].=     'blockquote line 1';
        $data['combination'][672]['expected'].=   '</p>';
        $data['combination'][672]['expected'].=   '<div data-attribute="true">data</div>'.NL;
        $data['combination'][672]['expected'].=   'blockquote line 3';
        $data['combination'][672]['expected'].= '</blockquote>';

        #####################
        ### list with mix ###
        #####################

        $data['combination'][700]['data'] = '- item 1'.NL;
        $data['combination'][700]['data'].= '  - item 1.1'.NL;
        $data['combination'][700]['data'].= '    - item 1.1.1'.NL;
        $data['combination'][700]['data'].= NL;
        $data['combination'][700]['data'].= '          title in code (Setext-style)'.NL;
        $data['combination'][700]['data'].= '          ---'.NL;
        $data['combination'][700]['data'].= '          # title in code (atx-style)'.NL;
        $data['combination'][700]['data'].= '          > blockquote in code'.NL;
        $data['combination'][700]['data'].= '          * * *'.NL;
        $data['combination'][700]['data'].= '          paragraph in code'.NL;
        $data['combination'][700]['data'].= '          - list item 1 in code'.NL;
        $data['combination'][700]['data'].= '            - list item 1.1 in code'.NL;
        $data['combination'][700]['data'].= '              - list item 1.1.1 in code'.NL;
        $data['combination'][700]['data'].= '            - list item 1.2 in code'.NL;
        $data['combination'][700]['data'].= '          - list item 2 in code';
        $data['combination'][700]['expected'] = '<ul>';
        $data['combination'][700]['expected'].=   '<li>item 1';
        $data['combination'][700]['expected'].=     '<ul>';
        $data['combination'][700]['expected'].=       '<li>item 1.1';
        $data['combination'][700]['expected'].=         '<ul>';
        $data['combination'][700]['expected'].=           '<li>item 1.1.1';
        $data['combination'][700]['expected'].=             '<pre>';
        $data['combination'][700]['expected'].=               '<code>';
        $data['combination'][700]['expected'].=                 'title in code (Setext-style)'.NL;
        $data['combination'][700]['expected'].=                 '---'.NL;
        $data['combination'][700]['expected'].=                 '# title in code (atx-style)'.NL;
        $data['combination'][700]['expected'].=                 '&gt; blockquote in code'.NL;
        $data['combination'][700]['expected'].=                 '* * *'.NL;
        $data['combination'][700]['expected'].=                 'paragraph in code'.NL;
        $data['combination'][700]['expected'].=                 '- list item 1 in code'.NL;
        $data['combination'][700]['expected'].=                 '  - list item 1.1 in code'.NL;
        $data['combination'][700]['expected'].=                 '    - list item 1.1.1 in code'.NL;
        $data['combination'][700]['expected'].=                 '  - list item 1.2 in code'.NL;
        $data['combination'][700]['expected'].=                 '- list item 2 in code';
        $data['combination'][700]['expected'].=               '</code>';
        $data['combination'][700]['expected'].=             '</pre>';
        $data['combination'][700]['expected'].=           '</li>';
        $data['combination'][700]['expected'].=         '</ul>';
        $data['combination'][700]['expected'].=       '</li>';
        $data['combination'][700]['expected'].=     '</ul>';
        $data['combination'][700]['expected'].=   '</li>';
        $data['combination'][700]['expected'].= '</ul>';

        foreach ($data['combination'] as $c_row_id => $c_info) {
            $c_expected = $c_info['expected'];
            $c_value = $c_info['data'];
            $c_gotten = Markdown::markdown_to_markup($c_value)->render();
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

}
