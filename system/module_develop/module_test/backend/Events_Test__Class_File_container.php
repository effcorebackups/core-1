<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use effcore\Directory;
use effcore\Extend_exception;
use effcore\File_container;
use effcore\File;
use effcore\Temporary;
use effcore\Test;
use effcore\Text_multiline;
use effcore\Text;
use Exception;

abstract class Events_Test__Class_File_container {

    static function test_step_code__path_parse(&$test, $dpath, &$c_results) {

        # see description in: Events_Test__Class_File::test_step_code__path_parse__with_protocol

        $data = [
            ''                                                   => ['protocol' => '',          'path_root' => '',                      'path_file' => '',              'target' => 'root'],
            'name'                                               => ['protocol' => '',          'path_root' => 'name',                  'path_file' => '',              'target' => 'root'],
            '.type'                                              => ['protocol' => '',          'path_root' => '.type',                 'path_file' => '',              'target' => 'root'],
            'name.type'                                          => ['protocol' => '',          'path_root' => 'name.type',             'path_file' => '',              'target' => 'root'],
            '/'                                                  => ['protocol' => '',          'path_root' => '/',                     'path_file' => '',              'target' => 'root'],
            '/name'                                              => ['protocol' => '',          'path_root' => '/name',                 'path_file' => '',              'target' => 'root'],
            '/.type'                                             => ['protocol' => '',          'path_root' => '/.type',                'path_file' => '',              'target' => 'root'],
            '/name.type'                                         => ['protocol' => '',          'path_root' => '/name.type',            'path_file' => '',              'target' => 'root'],
            'dirs/'                                              => ['protocol' => '',          'path_root' => 'dirs/',                 'path_file' => '',              'target' => 'root'],
            'dirs/name'                                          => ['protocol' => '',          'path_root' => 'dirs/name',             'path_file' => '',              'target' => 'root'],
            'dirs/.type'                                         => ['protocol' => '',          'path_root' => 'dirs/.type',            'path_file' => '',              'target' => 'root'],
            'dirs/name.type'                                     => ['protocol' => '',          'path_root' => 'dirs/name.type',        'path_file' => '',              'target' => 'root'],
            '/dirs/'                                             => ['protocol' => '',          'path_root' => '/dirs/',                'path_file' => '',              'target' => 'root'],
            '/dirs/name'                                         => ['protocol' => '',          'path_root' => '/dirs/name',            'path_file' => '',              'target' => 'root'],
            '/dirs/.type'                                        => ['protocol' => '',          'path_root' => '/dirs/.type',           'path_file' => '',              'target' => 'root'],
            '/dirs/name.type'                                    => ['protocol' => '',          'path_root' => '/dirs/name.type',       'path_file' => '',              'target' => 'root'],
            'container://'                                       => ['protocol' => 'container', 'path_root' => '',                      'path_file' => '',              'target' => 'root'],
            'container://name'                                   => ['protocol' => 'container', 'path_root' => 'name',                  'path_file' => '',              'target' => 'root'],
            'container://.type'                                  => ['protocol' => 'container', 'path_root' => '.type',                 'path_file' => '',              'target' => 'root'],
            'container://name.type'                              => ['protocol' => 'container', 'path_root' => 'name.type',             'path_file' => '',              'target' => 'root'],
            'container:///'                                      => ['protocol' => 'container', 'path_root' => '/',                     'path_file' => '',              'target' => 'root'],
            'container:///name'                                  => ['protocol' => 'container', 'path_root' => '/name',                 'path_file' => '',              'target' => 'root'],
            'container:///.type'                                 => ['protocol' => 'container', 'path_root' => '/.type',                'path_file' => '',              'target' => 'root'],
            'container:///name.type'                             => ['protocol' => 'container', 'path_root' => '/name.type',            'path_file' => '',              'target' => 'root'],
            'container://dirs/'                                  => ['protocol' => 'container', 'path_root' => 'dirs/',                 'path_file' => '',              'target' => 'root'],
            'container://dirs/name'                              => ['protocol' => 'container', 'path_root' => 'dirs/name',             'path_file' => '',              'target' => 'root'],
            'container://dirs/.type'                             => ['protocol' => 'container', 'path_root' => 'dirs/.type',            'path_file' => '',              'target' => 'root'],
            'container://dirs/name.type'                         => ['protocol' => 'container', 'path_root' => 'dirs/name.type',        'path_file' => '',              'target' => 'root'],
            'container:///dirs/'                                 => ['protocol' => 'container', 'path_root' => '/dirs/',                'path_file' => '',              'target' => 'root'],
            'container:///dirs/name'                             => ['protocol' => 'container', 'path_root' => '/dirs/name',            'path_file' => '',              'target' => 'root'],
            'container:///dirs/.type'                            => ['protocol' => 'container', 'path_root' => '/dirs/.type',           'path_file' => '',              'target' => 'root'],
            'container:///dirs/name.type'                        => ['protocol' => 'container', 'path_root' => '/dirs/name.type',       'path_file' => '',              'target' => 'root'],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'C:/'.''                                             => ['protocol' => '',          'path_root' => 'C:/'.'',                'path_file' => '',              'target' => 'root'],
            'C:/'.'name'                                         => ['protocol' => '',          'path_root' => 'C:/'.'name',            'path_file' => '',              'target' => 'root'],
            'C:/'.'.type'                                        => ['protocol' => '',          'path_root' => 'C:/'.'.type',           'path_file' => '',              'target' => 'root'],
            'C:/'.'name.type'                                    => ['protocol' => '',          'path_root' => 'C:/'.'name.type',       'path_file' => '',              'target' => 'root'],
            'C:/'.'/'                                            => ['protocol' => '',          'path_root' => 'C:/'.'/',               'path_file' => '',              'target' => 'root'],
            'C:/'.'/name'                                        => ['protocol' => '',          'path_root' => 'C:/'.'/name',           'path_file' => '',              'target' => 'root'],
            'C:/'.'/.type'                                       => ['protocol' => '',          'path_root' => 'C:/'.'/.type',          'path_file' => '',              'target' => 'root'],
            'C:/'.'/name.type'                                   => ['protocol' => '',          'path_root' => 'C:/'.'/name.type',      'path_file' => '',              'target' => 'root'],
            'C:/'.'dirs/'                                        => ['protocol' => '',          'path_root' => 'C:/'.'dirs/',           'path_file' => '',              'target' => 'root'],
            'C:/'.'dirs/name'                                    => ['protocol' => '',          'path_root' => 'C:/'.'dirs/name',       'path_file' => '',              'target' => 'root'],
            'C:/'.'dirs/.type'                                   => ['protocol' => '',          'path_root' => 'C:/'.'dirs/.type',      'path_file' => '',              'target' => 'root'],
            'C:/'.'dirs/name.type'                               => ['protocol' => '',          'path_root' => 'C:/'.'dirs/name.type',  'path_file' => '',              'target' => 'root'],
            'C:/'.'/dirs/'                                       => ['protocol' => '',          'path_root' => 'C:/'.'/dirs/',          'path_file' => '',              'target' => 'root'],
            'C:/'.'/dirs/name'                                   => ['protocol' => '',          'path_root' => 'C:/'.'/dirs/name',      'path_file' => '',              'target' => 'root'],
            'C:/'.'/dirs/.type'                                  => ['protocol' => '',          'path_root' => 'C:/'.'/dirs/.type',     'path_file' => '',              'target' => 'root'],
            'C:/'.'/dirs/name.type'                              => ['protocol' => '',          'path_root' => 'C:/'.'/dirs/name.type', 'path_file' => '',              'target' => 'root'],
            'container://'.'C:/'.''                              => ['protocol' => 'container', 'path_root' => 'C:/'.'',                'path_file' => '',              'target' => 'root'],
            'container://'.'C:/'.'name'                          => ['protocol' => 'container', 'path_root' => 'C:/'.'name',            'path_file' => '',              'target' => 'root'],
            'container://'.'C:/'.'.type'                         => ['protocol' => 'container', 'path_root' => 'C:/'.'.type',           'path_file' => '',              'target' => 'root'],
            'container://'.'C:/'.'name.type'                     => ['protocol' => 'container', 'path_root' => 'C:/'.'name.type',       'path_file' => '',              'target' => 'root'],
            'container://'.'C:/'.'/'                             => ['protocol' => 'container', 'path_root' => 'C:/'.'/',               'path_file' => '',              'target' => 'root'],
            'container://'.'C:/'.'/name'                         => ['protocol' => 'container', 'path_root' => 'C:/'.'/name',           'path_file' => '',              'target' => 'root'],
            'container://'.'C:/'.'/.type'                        => ['protocol' => 'container', 'path_root' => 'C:/'.'/.type',          'path_file' => '',              'target' => 'root'],
            'container://'.'C:/'.'/name.type'                    => ['protocol' => 'container', 'path_root' => 'C:/'.'/name.type',      'path_file' => '',              'target' => 'root'],
            'container://'.'C:/'.'dirs/'                         => ['protocol' => 'container', 'path_root' => 'C:/'.'dirs/',           'path_file' => '',              'target' => 'root'],
            'container://'.'C:/'.'dirs/name'                     => ['protocol' => 'container', 'path_root' => 'C:/'.'dirs/name',       'path_file' => '',              'target' => 'root'],
            'container://'.'C:/'.'dirs/.type'                    => ['protocol' => 'container', 'path_root' => 'C:/'.'dirs/.type',      'path_file' => '',              'target' => 'root'],
            'container://'.'C:/'.'dirs/name.type'                => ['protocol' => 'container', 'path_root' => 'C:/'.'dirs/name.type',  'path_file' => '',              'target' => 'root'],
            'container://'.'C:/'.'/dirs/'                        => ['protocol' => 'container', 'path_root' => 'C:/'.'/dirs/',          'path_file' => '',              'target' => 'root'],
            'container://'.'C:/'.'/dirs/name'                    => ['protocol' => 'container', 'path_root' => 'C:/'.'/dirs/name',      'path_file' => '',              'target' => 'root'],
            'container://'.'C:/'.'/dirs/.type'                   => ['protocol' => 'container', 'path_root' => 'C:/'.'/dirs/.type',     'path_file' => '',              'target' => 'root'],
            'container://'.'C:/'.'/dirs/name.type'               => ['protocol' => 'container', 'path_root' => 'C:/'.'/dirs/name.type', 'path_file' => '',              'target' => 'root'],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            ':internal_path'                                     => ['protocol' => '',          'path_root' => '',                      'path_file' => 'internal_path', 'target' => 'file'],
            'name:internal_path'                                 => ['protocol' => '',          'path_root' => 'name',                  'path_file' => 'internal_path', 'target' => 'file'],
            '.type:internal_path'                                => ['protocol' => '',          'path_root' => '.type',                 'path_file' => 'internal_path', 'target' => 'file'],
            'name.type:internal_path'                            => ['protocol' => '',          'path_root' => 'name.type',             'path_file' => 'internal_path', 'target' => 'file'],
            '/:internal_path'                                    => ['protocol' => '',          'path_root' => '/',                     'path_file' => 'internal_path', 'target' => 'file'],
            '/name:internal_path'                                => ['protocol' => '',          'path_root' => '/name',                 'path_file' => 'internal_path', 'target' => 'file'],
            '/.type:internal_path'                               => ['protocol' => '',          'path_root' => '/.type',                'path_file' => 'internal_path', 'target' => 'file'],
            '/name.type:internal_path'                           => ['protocol' => '',          'path_root' => '/name.type',            'path_file' => 'internal_path', 'target' => 'file'],
            'dirs/:internal_path'                                => ['protocol' => '',          'path_root' => 'dirs/',                 'path_file' => 'internal_path', 'target' => 'file'],
            'dirs/name:internal_path'                            => ['protocol' => '',          'path_root' => 'dirs/name',             'path_file' => 'internal_path', 'target' => 'file'],
            'dirs/.type:internal_path'                           => ['protocol' => '',          'path_root' => 'dirs/.type',            'path_file' => 'internal_path', 'target' => 'file'],
            'dirs/name.type:internal_path'                       => ['protocol' => '',          'path_root' => 'dirs/name.type',        'path_file' => 'internal_path', 'target' => 'file'],
            '/dirs/:internal_path'                               => ['protocol' => '',          'path_root' => '/dirs/',                'path_file' => 'internal_path', 'target' => 'file'],
            '/dirs/name:internal_path'                           => ['protocol' => '',          'path_root' => '/dirs/name',            'path_file' => 'internal_path', 'target' => 'file'],
            '/dirs/.type:internal_path'                          => ['protocol' => '',          'path_root' => '/dirs/.type',           'path_file' => 'internal_path', 'target' => 'file'],
            '/dirs/name.type:internal_path'                      => ['protocol' => '',          'path_root' => '/dirs/name.type',       'path_file' => 'internal_path', 'target' => 'file'],
            'container://:internal_path'                         => ['protocol' => 'container', 'path_root' => '',                      'path_file' => 'internal_path', 'target' => 'file'],
            'container://name:internal_path'                     => ['protocol' => 'container', 'path_root' => 'name',                  'path_file' => 'internal_path', 'target' => 'file'],
            'container://.type:internal_path'                    => ['protocol' => 'container', 'path_root' => '.type',                 'path_file' => 'internal_path', 'target' => 'file'],
            'container://name.type:internal_path'                => ['protocol' => 'container', 'path_root' => 'name.type',             'path_file' => 'internal_path', 'target' => 'file'],
            'container:///:internal_path'                        => ['protocol' => 'container', 'path_root' => '/',                     'path_file' => 'internal_path', 'target' => 'file'],
            'container:///name:internal_path'                    => ['protocol' => 'container', 'path_root' => '/name',                 'path_file' => 'internal_path', 'target' => 'file'],
            'container:///.type:internal_path'                   => ['protocol' => 'container', 'path_root' => '/.type',                'path_file' => 'internal_path', 'target' => 'file'],
            'container:///name.type:internal_path'               => ['protocol' => 'container', 'path_root' => '/name.type',            'path_file' => 'internal_path', 'target' => 'file'],
            'container://dirs/:internal_path'                    => ['protocol' => 'container', 'path_root' => 'dirs/',                 'path_file' => 'internal_path', 'target' => 'file'],
            'container://dirs/name:internal_path'                => ['protocol' => 'container', 'path_root' => 'dirs/name',             'path_file' => 'internal_path', 'target' => 'file'],
            'container://dirs/.type:internal_path'               => ['protocol' => 'container', 'path_root' => 'dirs/.type',            'path_file' => 'internal_path', 'target' => 'file'],
            'container://dirs/name.type:internal_path'           => ['protocol' => 'container', 'path_root' => 'dirs/name.type',        'path_file' => 'internal_path', 'target' => 'file'],
            'container:///dirs/:internal_path'                   => ['protocol' => 'container', 'path_root' => '/dirs/',                'path_file' => 'internal_path', 'target' => 'file'],
            'container:///dirs/name:internal_path'               => ['protocol' => 'container', 'path_root' => '/dirs/name',            'path_file' => 'internal_path', 'target' => 'file'],
            'container:///dirs/.type:internal_path'              => ['protocol' => 'container', 'path_root' => '/dirs/.type',           'path_file' => 'internal_path', 'target' => 'file'],
            'container:///dirs/name.type:internal_path'          => ['protocol' => 'container', 'path_root' => '/dirs/name.type',       'path_file' => 'internal_path', 'target' => 'file'],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'C:/'.':internal_path'                               => ['protocol' => '',          'path_root' => 'C:/'.'',                'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'name:internal_path'                           => ['protocol' => '',          'path_root' => 'C:/'.'name',            'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'.type:internal_path'                          => ['protocol' => '',          'path_root' => 'C:/'.'.type',           'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'name.type:internal_path'                      => ['protocol' => '',          'path_root' => 'C:/'.'name.type',       'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'/:internal_path'                              => ['protocol' => '',          'path_root' => 'C:/'.'/',               'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'/name:internal_path'                          => ['protocol' => '',          'path_root' => 'C:/'.'/name',           'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'/.type:internal_path'                         => ['protocol' => '',          'path_root' => 'C:/'.'/.type',          'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'/name.type:internal_path'                     => ['protocol' => '',          'path_root' => 'C:/'.'/name.type',      'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'dirs/:internal_path'                          => ['protocol' => '',          'path_root' => 'C:/'.'dirs/',           'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'dirs/name:internal_path'                      => ['protocol' => '',          'path_root' => 'C:/'.'dirs/name',       'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'dirs/.type:internal_path'                     => ['protocol' => '',          'path_root' => 'C:/'.'dirs/.type',      'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'dirs/name.type:internal_path'                 => ['protocol' => '',          'path_root' => 'C:/'.'dirs/name.type',  'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'/dirs/:internal_path'                         => ['protocol' => '',          'path_root' => 'C:/'.'/dirs/',          'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'/dirs/name:internal_path'                     => ['protocol' => '',          'path_root' => 'C:/'.'/dirs/name',      'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'/dirs/.type:internal_path'                    => ['protocol' => '',          'path_root' => 'C:/'.'/dirs/.type',     'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'/dirs/name.type:internal_path'                => ['protocol' => '',          'path_root' => 'C:/'.'/dirs/name.type', 'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.':internal_path'                => ['protocol' => 'container', 'path_root' => 'C:/'.'',                'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'name:internal_path'            => ['protocol' => 'container', 'path_root' => 'C:/'.'name',            'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'.type:internal_path'           => ['protocol' => 'container', 'path_root' => 'C:/'.'.type',           'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'name.type:internal_path'       => ['protocol' => 'container', 'path_root' => 'C:/'.'name.type',       'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'/:internal_path'               => ['protocol' => 'container', 'path_root' => 'C:/'.'/',               'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'/name:internal_path'           => ['protocol' => 'container', 'path_root' => 'C:/'.'/name',           'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'/.type:internal_path'          => ['protocol' => 'container', 'path_root' => 'C:/'.'/.type',          'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'/name.type:internal_path'      => ['protocol' => 'container', 'path_root' => 'C:/'.'/name.type',      'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'dirs/:internal_path'           => ['protocol' => 'container', 'path_root' => 'C:/'.'dirs/',           'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'dirs/name:internal_path'       => ['protocol' => 'container', 'path_root' => 'C:/'.'dirs/name',       'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'dirs/.type:internal_path'      => ['protocol' => 'container', 'path_root' => 'C:/'.'dirs/.type',      'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'dirs/name.type:internal_path'  => ['protocol' => 'container', 'path_root' => 'C:/'.'dirs/name.type',  'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'/dirs/:internal_path'          => ['protocol' => 'container', 'path_root' => 'C:/'.'/dirs/',          'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'/dirs/name:internal_path'      => ['protocol' => 'container', 'path_root' => 'C:/'.'/dirs/name',      'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'/dirs/.type:internal_path'     => ['protocol' => 'container', 'path_root' => 'C:/'.'/dirs/.type',     'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'/dirs/name.type:internal_path' => ['protocol' => 'container', 'path_root' => 'C:/'.'/dirs/name.type', 'path_file' => 'internal_path', 'target' => 'file'],
        ];

        foreach ($data as $c_value => $c_expected) {
            $c_gotten = File_container::__path_parse($c_value);
            $c_result = $c_gotten['protocol' ] === $c_expected['protocol' ] &&
                        $c_gotten['path_root'] === $c_expected['path_root'] &&
                        $c_gotten['path_file'] === $c_expected['path_file'] &&
                        $c_gotten['target'   ] === $c_expected['target'   ];
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__fopen_fwrite_fread_fseek(&$test, $dpath, &$c_results) {

        #############################
        ### create test directory ###
        #############################

        $path_root = Temporary::DIRECTORY.'test/File_container/';

        if (!Directory::create($path_root)) {
            $c_results['reports'][$dpath][] = new Text_multiline([
                'Directory "%%_directory" cannot be created!',
                'Parent directory permissions are too strict!'], ['directory' => $path_root]);
            $c_results['return'] = 0;
            return;
        }

        ##################################################################
        ### create simple file: fopen() + fwrite() + fseek() + fread() ###
        ##################################################################

        $path_container =                $path_root.'simple.box';
        $path_internal  = 'container://'.$path_root.'simple.box:file_1';

        # restore state if a previous attempt of this test is failed
        if (file_exists($path_container)) {
            if (!File::delete($path_container)) {
                $c_results['reports'][$dpath][] = new Text_multiline([
                    'File "%%_file" cannot be deleted!',
                    'Directory permissions are too strict!'], ['file' => $path_container]);
                $c_results['return'] = 0;
                return;
            }
        }

        try {

            ###########################################################################################
            ### fopen() | [ fwrite(,A) ] | fseek(,1) → fread(,3) | fseek(,6) → fread(,3) | fclose() ###
            ###########################################################################################

            $gotten = true;
            $handle = fopen($path_internal, 'c+b');
            stream_set_read_buffer ($handle, 0);
            stream_set_write_buffer($handle, 0);
                fwrite($handle, '0');
                fwrite($handle, '12');
                fwrite($handle, '345');
                fwrite($handle, '67');
                fwrite($handle, '8');
                fwrite($handle, '9');
                fseek($handle, 1); $gotten&= fread($handle, 3) === '123';
                fseek($handle, 6); $gotten&= fread($handle, 3) === '678';
            fclose($handle);
            $expected = true;
            $result = (bool)$gotten === $expected;
            if ($result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'fopen() | [ fwrite(,A) ] | fseek(,1) → fread(,3) | fseek(,6) → fread(,3) | fclose()', 'result' => (new Text('success'))->render()]);
            if ($result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'fopen() | [ fwrite(,A) ] | fseek(,1) → fread(,3) | fseek(,6) → fread(,3) | fclose()', 'result' => (new Text('failure'))->render()]);
            if ($result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($gotten)]);
                $c_results['return'] = 0;
                return;
            }

            ##########################################
            ### fopen() → [ fread(,1) ] → fclose() ###
            ##########################################

            $handle = fopen($path_internal, 'rb');
            stream_set_read_buffer ($handle, 0);
            stream_set_write_buffer($handle, 0);
            $gotten = fread($handle, 1) === '0' &&
                      fread($handle, 1) === '1' &&
                      fread($handle, 1) === '2' &&
                      fread($handle, 1) === '3' &&
                      fread($handle, 1) === '4' &&
                      fread($handle, 1) === '5' &&
                      fread($handle, 1) === '6' &&
                      fread($handle, 1) === '7' &&
                      fread($handle, 1) === '8' &&
                      fread($handle, 1) === '9' &&
                      fread($handle, 1) === '';
            fclose($handle);
            $expected = true;
            $result = $gotten === $expected;
            if ($result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'fopen() → [ fread(,1) ] → fclose()', 'result' => (new Text('success'))->render()]);
            if ($result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'fopen() → [ fread(,1) ] → fclose()', 'result' => (new Text('failure'))->render()]);
            if ($result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($gotten)]);
                $c_results['return'] = 0;
                return;
            }

            ##########################################
            ### fopen() → [ fread(,N) ] → fclose() ###
            ##########################################

            $handle = fopen($path_internal, 'rb');
            stream_set_read_buffer ($handle, 0);
            stream_set_write_buffer($handle, 0);
            $gotten = fread($handle, 1) === '0'    &&
                      fread($handle, 2) === '12'   &&
                      fread($handle, 3) === '345'  &&
                      fread($handle, 4) === '6789' &&
                      fread($handle, 5) === '';
            fclose($handle);
            $expected = true;
            $result = $gotten === $expected;
            if ($result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'fopen() → [ fread(,N) ] → fclose()', 'result' => (new Text('success'))->render()]);
            if ($result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'fopen() → [ fread(,N) ] → fclose()', 'result' => (new Text('failure'))->render()]);
            if ($result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($gotten)]);
                $c_results['return'] = 0;
                return;
            }

            ##########################################
            ### [ fopen() → fread(,N) → fclose() ] ###
            ##########################################

            $gotten = true;
                $handle = fopen($path_internal, 'rb'); stream_set_read_buffer($handle, 0); stream_set_write_buffer($handle, 0); $gotten&= fread($handle,  1) === '0';          fclose($handle);
                $handle = fopen($path_internal, 'rb'); stream_set_read_buffer($handle, 0); stream_set_write_buffer($handle, 0); $gotten&= fread($handle,  2) === '01';         fclose($handle);
                $handle = fopen($path_internal, 'rb'); stream_set_read_buffer($handle, 0); stream_set_write_buffer($handle, 0); $gotten&= fread($handle,  3) === '012';        fclose($handle);
                $handle = fopen($path_internal, 'rb'); stream_set_read_buffer($handle, 0); stream_set_write_buffer($handle, 0); $gotten&= fread($handle,  4) === '0123';       fclose($handle);
                $handle = fopen($path_internal, 'rb'); stream_set_read_buffer($handle, 0); stream_set_write_buffer($handle, 0); $gotten&= fread($handle,  5) === '01234';      fclose($handle);
                $handle = fopen($path_internal, 'rb'); stream_set_read_buffer($handle, 0); stream_set_write_buffer($handle, 0); $gotten&= fread($handle,  6) === '012345';     fclose($handle);
                $handle = fopen($path_internal, 'rb'); stream_set_read_buffer($handle, 0); stream_set_write_buffer($handle, 0); $gotten&= fread($handle,  7) === '0123456';    fclose($handle);
                $handle = fopen($path_internal, 'rb'); stream_set_read_buffer($handle, 0); stream_set_write_buffer($handle, 0); $gotten&= fread($handle,  8) === '01234567';   fclose($handle);
                $handle = fopen($path_internal, 'rb'); stream_set_read_buffer($handle, 0); stream_set_write_buffer($handle, 0); $gotten&= fread($handle,  9) === '012345678';  fclose($handle);
                $handle = fopen($path_internal, 'rb'); stream_set_read_buffer($handle, 0); stream_set_write_buffer($handle, 0); $gotten&= fread($handle, 10) === '0123456789'; fclose($handle);
                $handle = fopen($path_internal, 'rb'); stream_set_read_buffer($handle, 0); stream_set_write_buffer($handle, 0); $gotten&= fread($handle, 11) === '0123456789'; fclose($handle);
            $expected = true;
            $result = (bool)$gotten === $expected;
            if ($result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => '[ fopen() → fread(,N) → fclose() ]', 'result' => (new Text('success'))->render()]);
            if ($result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => '[ fopen() → fread(,N) → fclose() ]', 'result' => (new Text('failure'))->render()]);
            if ($result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($gotten)]);
                $c_results['return'] = 0;
                return;
            }

            ######################################################
            ### fopen() → [ fseek(,0) → fread(,N) ] → fclose() ###
            ######################################################

            $handle = fopen($path_internal, 'rb');
            stream_set_read_buffer ($handle, 0);
            stream_set_write_buffer($handle, 0);
                fseek($handle, 0, SEEK_SET); $gotten&= fread($handle,  1) === '0';
                fseek($handle, 0, SEEK_SET); $gotten&= fread($handle,  2) === '01';
                fseek($handle, 0, SEEK_SET); $gotten&= fread($handle,  3) === '012';
                fseek($handle, 0, SEEK_SET); $gotten&= fread($handle,  4) === '0123';
                fseek($handle, 0, SEEK_SET); $gotten&= fread($handle,  5) === '01234';
                fseek($handle, 0, SEEK_SET); $gotten&= fread($handle,  6) === '012345';
                fseek($handle, 0, SEEK_SET); $gotten&= fread($handle,  7) === '0123456';
                fseek($handle, 0, SEEK_SET); $gotten&= fread($handle,  8) === '01234567';
                fseek($handle, 0, SEEK_SET); $gotten&= fread($handle,  9) === '012345678';
                fseek($handle, 0, SEEK_SET); $gotten&= fread($handle, 10) === '0123456789';
                fseek($handle, 0, SEEK_SET); $gotten&= fread($handle, 11) === '0123456789';
            fclose($handle);
            $expected = true;
            $result = (bool)$gotten === $expected;
            if ($result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'fopen() → [ fseek(, 0) → fread(,N) ] → fclose()', 'result' => (new Text('success'))->render()]);
            if ($result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'fopen() → [ fseek(, 0) → fread(,N) ] → fclose()', 'result' => (new Text('failure'))->render()]);
            if ($result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($gotten)]);
                $c_results['return'] = 0;
                return;
            }

            ######################################################
            ### fopen() → [ fseek(,5) → fread(,N) ] → fclose() ###
            ######################################################

            $handle = fopen($path_internal, 'rb');
            stream_set_read_buffer ($handle, 0);
            stream_set_write_buffer($handle, 0);
                fseek($handle, 5, SEEK_SET); $gotten&= fread($handle, 1) === '5';
                fseek($handle, 5, SEEK_SET); $gotten&= fread($handle, 2) === '56';
                fseek($handle, 5, SEEK_SET); $gotten&= fread($handle, 3) === '567';
                fseek($handle, 5, SEEK_SET); $gotten&= fread($handle, 4) === '5678';
                fseek($handle, 5, SEEK_SET); $gotten&= fread($handle, 5) === '56789';
                fseek($handle, 5, SEEK_SET); $gotten&= fread($handle, 6) === '56789';
            fclose($handle);
            $expected = true;
            $result = (bool)$gotten === $expected;
            if ($result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'fopen() → [ fseek(, 5) → fread(,N) ] → fclose()', 'result' => (new Text('success'))->render()]);
            if ($result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'fopen() → [ fseek(, 5) → fread(,N) ] → fclose()', 'result' => (new Text('failure'))->render()]);
            if ($result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($gotten)]);
                $c_results['return'] = 0;
                return;
            }

            #######################################################
            ### fopen() → [ fseek(,11) → fread(,N) ] → fclose() ###
            #######################################################

            $handle = fopen($path_internal, 'rb');
            stream_set_read_buffer ($handle, 0);
            stream_set_write_buffer($handle, 0);
                fseek($handle, 11, SEEK_SET); $gotten&= fread($handle, 1) === '';
                fseek($handle, 11, SEEK_SET); $gotten&= fread($handle, 2) === '';
            fclose($handle);
            $expected = true;
            $result = (bool)$gotten === $expected;
            if ($result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'fopen() → [ fseek(,11) → fread(,N) ] → fclose()', 'result' => (new Text('success'))->render()]);
            if ($result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'fopen() → [ fseek(,11) → fread(,N) ] → fclose()', 'result' => (new Text('failure'))->render()]);
            if ($result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($gotten)]);
                $c_results['return'] = 0;
                return;
            }

        } catch (Extend_exception|Exception $e) {
            if     ($e instanceof Extend_exception) $c_results['reports'][$dpath][] = $e->getExMessageTextObject();
            elseif ($e instanceof Exception       ) $c_results['reports'][$dpath][] = $e->getMessage();
            $c_results['return'] = 0;
            return;
        }

        #########################
        ### delete test file ####
        #########################

        if (file_exists($path_container)) {
            if (!File::delete($path_container)) {
                $c_results['reports'][$dpath][] = new Text_multiline([
                    'File "%%_file" cannot be deleted!',
                    'Directory permissions are too strict!'], ['file' => $path_container]);
                $c_results['return'] = 0;
                return;
            }
        }

        #############################
        ### delete test directory ###
        #############################

        if (!Directory::delete($path_root)) {
            $c_results['reports'][$dpath][] = new Text_multiline([
                'Directory "%%_directory" cannot be deleted!',
                'Parent directory permissions are too strict!'], ['directory' => $path_root]);
            $c_results['return'] = 0;
            return;
        }
    }

}
