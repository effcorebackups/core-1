<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use const effcore\DIR_ROOT;
use effcore\Core;
use effcore\Directory;
use effcore\Extend_exception;
use effcore\File_container;
use effcore\File_history;
use effcore\File;
use effcore\Module;
use effcore\Storage_Data;
use effcore\Temporary;
use effcore\Test;
use effcore\Text_multiline;
use effcore\Text;
use Exception;

abstract class Events_Test__Class_File_container {

    static function test_step_code__path_parse(&$test, $dpath, &$c_results) {

        # see description in: Events_Test__Class_File::test_step_code__path_parse__with_protocol

        $data = [
            ''                                                   => ['protocol' => ''         , 'path_root' => ''                     , 'path_file' => ''             , 'target' => 'root'],
            'name'                                               => ['protocol' => ''         , 'path_root' => 'name'                 , 'path_file' => ''             , 'target' => 'root'],
            '.type'                                              => ['protocol' => ''         , 'path_root' => '.type'                , 'path_file' => ''             , 'target' => 'root'],
            'name.type'                                          => ['protocol' => ''         , 'path_root' => 'name.type'            , 'path_file' => ''             , 'target' => 'root'],
            '/'                                                  => ['protocol' => ''         , 'path_root' => '/'                    , 'path_file' => ''             , 'target' => 'root'],
            '/name'                                              => ['protocol' => ''         , 'path_root' => '/name'                , 'path_file' => ''             , 'target' => 'root'],
            '/.type'                                             => ['protocol' => ''         , 'path_root' => '/.type'               , 'path_file' => ''             , 'target' => 'root'],
            '/name.type'                                         => ['protocol' => ''         , 'path_root' => '/name.type'           , 'path_file' => ''             , 'target' => 'root'],
            'dirs/'                                              => ['protocol' => ''         , 'path_root' => 'dirs/'                , 'path_file' => ''             , 'target' => 'root'],
            'dirs/name'                                          => ['protocol' => ''         , 'path_root' => 'dirs/name'            , 'path_file' => ''             , 'target' => 'root'],
            'dirs/.type'                                         => ['protocol' => ''         , 'path_root' => 'dirs/.type'           , 'path_file' => ''             , 'target' => 'root'],
            'dirs/name.type'                                     => ['protocol' => ''         , 'path_root' => 'dirs/name.type'       , 'path_file' => ''             , 'target' => 'root'],
            '/dirs/'                                             => ['protocol' => ''         , 'path_root' => '/dirs/'               , 'path_file' => ''             , 'target' => 'root'],
            '/dirs/name'                                         => ['protocol' => ''         , 'path_root' => '/dirs/name'           , 'path_file' => ''             , 'target' => 'root'],
            '/dirs/.type'                                        => ['protocol' => ''         , 'path_root' => '/dirs/.type'          , 'path_file' => ''             , 'target' => 'root'],
            '/dirs/name.type'                                    => ['protocol' => ''         , 'path_root' => '/dirs/name.type'      , 'path_file' => ''             , 'target' => 'root'],
            'container://'                                       => ['protocol' => 'container', 'path_root' => ''                     , 'path_file' => ''             , 'target' => 'root'],
            'container://name'                                   => ['protocol' => 'container', 'path_root' => 'name'                 , 'path_file' => ''             , 'target' => 'root'],
            'container://.type'                                  => ['protocol' => 'container', 'path_root' => '.type'                , 'path_file' => ''             , 'target' => 'root'],
            'container://name.type'                              => ['protocol' => 'container', 'path_root' => 'name.type'            , 'path_file' => ''             , 'target' => 'root'],
            'container:///'                                      => ['protocol' => 'container', 'path_root' => '/'                    , 'path_file' => ''             , 'target' => 'root'],
            'container:///name'                                  => ['protocol' => 'container', 'path_root' => '/name'                , 'path_file' => ''             , 'target' => 'root'],
            'container:///.type'                                 => ['protocol' => 'container', 'path_root' => '/.type'               , 'path_file' => ''             , 'target' => 'root'],
            'container:///name.type'                             => ['protocol' => 'container', 'path_root' => '/name.type'           , 'path_file' => ''             , 'target' => 'root'],
            'container://dirs/'                                  => ['protocol' => 'container', 'path_root' => 'dirs/'                , 'path_file' => ''             , 'target' => 'root'],
            'container://dirs/name'                              => ['protocol' => 'container', 'path_root' => 'dirs/name'            , 'path_file' => ''             , 'target' => 'root'],
            'container://dirs/.type'                             => ['protocol' => 'container', 'path_root' => 'dirs/.type'           , 'path_file' => ''             , 'target' => 'root'],
            'container://dirs/name.type'                         => ['protocol' => 'container', 'path_root' => 'dirs/name.type'       , 'path_file' => ''             , 'target' => 'root'],
            'container:///dirs/'                                 => ['protocol' => 'container', 'path_root' => '/dirs/'               , 'path_file' => ''             , 'target' => 'root'],
            'container:///dirs/name'                             => ['protocol' => 'container', 'path_root' => '/dirs/name'           , 'path_file' => ''             , 'target' => 'root'],
            'container:///dirs/.type'                            => ['protocol' => 'container', 'path_root' => '/dirs/.type'          , 'path_file' => ''             , 'target' => 'root'],
            'container:///dirs/name.type'                        => ['protocol' => 'container', 'path_root' => '/dirs/name.type'      , 'path_file' => ''             , 'target' => 'root'],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'C:/'.''                                             => ['protocol' => ''         , 'path_root' => 'C:/'.''               , 'path_file' => ''             , 'target' => 'root'],
            'C:/'.'name'                                         => ['protocol' => ''         , 'path_root' => 'C:/'.'name'           , 'path_file' => ''             , 'target' => 'root'],
            'C:/'.'.type'                                        => ['protocol' => ''         , 'path_root' => 'C:/'.'.type'          , 'path_file' => ''             , 'target' => 'root'],
            'C:/'.'name.type'                                    => ['protocol' => ''         , 'path_root' => 'C:/'.'name.type'      , 'path_file' => ''             , 'target' => 'root'],
            'C:/'.'/'                                            => ['protocol' => ''         , 'path_root' => 'C:/'.'/'              , 'path_file' => ''             , 'target' => 'root'],
            'C:/'.'/name'                                        => ['protocol' => ''         , 'path_root' => 'C:/'.'/name'          , 'path_file' => ''             , 'target' => 'root'],
            'C:/'.'/.type'                                       => ['protocol' => ''         , 'path_root' => 'C:/'.'/.type'         , 'path_file' => ''             , 'target' => 'root'],
            'C:/'.'/name.type'                                   => ['protocol' => ''         , 'path_root' => 'C:/'.'/name.type'     , 'path_file' => ''             , 'target' => 'root'],
            'C:/'.'dirs/'                                        => ['protocol' => ''         , 'path_root' => 'C:/'.'dirs/'          , 'path_file' => ''             , 'target' => 'root'],
            'C:/'.'dirs/name'                                    => ['protocol' => ''         , 'path_root' => 'C:/'.'dirs/name'      , 'path_file' => ''             , 'target' => 'root'],
            'C:/'.'dirs/.type'                                   => ['protocol' => ''         , 'path_root' => 'C:/'.'dirs/.type'     , 'path_file' => ''             , 'target' => 'root'],
            'C:/'.'dirs/name.type'                               => ['protocol' => ''         , 'path_root' => 'C:/'.'dirs/name.type' , 'path_file' => ''             , 'target' => 'root'],
            'C:/'.'/dirs/'                                       => ['protocol' => ''         , 'path_root' => 'C:/'.'/dirs/'         , 'path_file' => ''             , 'target' => 'root'],
            'C:/'.'/dirs/name'                                   => ['protocol' => ''         , 'path_root' => 'C:/'.'/dirs/name'     , 'path_file' => ''             , 'target' => 'root'],
            'C:/'.'/dirs/.type'                                  => ['protocol' => ''         , 'path_root' => 'C:/'.'/dirs/.type'    , 'path_file' => ''             , 'target' => 'root'],
            'C:/'.'/dirs/name.type'                              => ['protocol' => ''         , 'path_root' => 'C:/'.'/dirs/name.type', 'path_file' => ''             , 'target' => 'root'],
            'container://'.'C:/'.''                              => ['protocol' => 'container', 'path_root' => 'C:/'.''               , 'path_file' => ''             , 'target' => 'root'],
            'container://'.'C:/'.'name'                          => ['protocol' => 'container', 'path_root' => 'C:/'.'name'           , 'path_file' => ''             , 'target' => 'root'],
            'container://'.'C:/'.'.type'                         => ['protocol' => 'container', 'path_root' => 'C:/'.'.type'          , 'path_file' => ''             , 'target' => 'root'],
            'container://'.'C:/'.'name.type'                     => ['protocol' => 'container', 'path_root' => 'C:/'.'name.type'      , 'path_file' => ''             , 'target' => 'root'],
            'container://'.'C:/'.'/'                             => ['protocol' => 'container', 'path_root' => 'C:/'.'/'              , 'path_file' => ''             , 'target' => 'root'],
            'container://'.'C:/'.'/name'                         => ['protocol' => 'container', 'path_root' => 'C:/'.'/name'          , 'path_file' => ''             , 'target' => 'root'],
            'container://'.'C:/'.'/.type'                        => ['protocol' => 'container', 'path_root' => 'C:/'.'/.type'         , 'path_file' => ''             , 'target' => 'root'],
            'container://'.'C:/'.'/name.type'                    => ['protocol' => 'container', 'path_root' => 'C:/'.'/name.type'     , 'path_file' => ''             , 'target' => 'root'],
            'container://'.'C:/'.'dirs/'                         => ['protocol' => 'container', 'path_root' => 'C:/'.'dirs/'          , 'path_file' => ''             , 'target' => 'root'],
            'container://'.'C:/'.'dirs/name'                     => ['protocol' => 'container', 'path_root' => 'C:/'.'dirs/name'      , 'path_file' => ''             , 'target' => 'root'],
            'container://'.'C:/'.'dirs/.type'                    => ['protocol' => 'container', 'path_root' => 'C:/'.'dirs/.type'     , 'path_file' => ''             , 'target' => 'root'],
            'container://'.'C:/'.'dirs/name.type'                => ['protocol' => 'container', 'path_root' => 'C:/'.'dirs/name.type' , 'path_file' => ''             , 'target' => 'root'],
            'container://'.'C:/'.'/dirs/'                        => ['protocol' => 'container', 'path_root' => 'C:/'.'/dirs/'         , 'path_file' => ''             , 'target' => 'root'],
            'container://'.'C:/'.'/dirs/name'                    => ['protocol' => 'container', 'path_root' => 'C:/'.'/dirs/name'     , 'path_file' => ''             , 'target' => 'root'],
            'container://'.'C:/'.'/dirs/.type'                   => ['protocol' => 'container', 'path_root' => 'C:/'.'/dirs/.type'    , 'path_file' => ''             , 'target' => 'root'],
            'container://'.'C:/'.'/dirs/name.type'               => ['protocol' => 'container', 'path_root' => 'C:/'.'/dirs/name.type', 'path_file' => ''             , 'target' => 'root'],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            ':internal_path'                                     => ['protocol' => ''         , 'path_root' => ''                     , 'path_file' => 'internal_path', 'target' => 'file'],
            'name:internal_path'                                 => ['protocol' => ''         , 'path_root' => 'name'                 , 'path_file' => 'internal_path', 'target' => 'file'],
            '.type:internal_path'                                => ['protocol' => ''         , 'path_root' => '.type'                , 'path_file' => 'internal_path', 'target' => 'file'],
            'name.type:internal_path'                            => ['protocol' => ''         , 'path_root' => 'name.type'            , 'path_file' => 'internal_path', 'target' => 'file'],
            '/:internal_path'                                    => ['protocol' => ''         , 'path_root' => '/'                    , 'path_file' => 'internal_path', 'target' => 'file'],
            '/name:internal_path'                                => ['protocol' => ''         , 'path_root' => '/name'                , 'path_file' => 'internal_path', 'target' => 'file'],
            '/.type:internal_path'                               => ['protocol' => ''         , 'path_root' => '/.type'               , 'path_file' => 'internal_path', 'target' => 'file'],
            '/name.type:internal_path'                           => ['protocol' => ''         , 'path_root' => '/name.type'           , 'path_file' => 'internal_path', 'target' => 'file'],
            'dirs/:internal_path'                                => ['protocol' => ''         , 'path_root' => 'dirs/'                , 'path_file' => 'internal_path', 'target' => 'file'],
            'dirs/name:internal_path'                            => ['protocol' => ''         , 'path_root' => 'dirs/name'            , 'path_file' => 'internal_path', 'target' => 'file'],
            'dirs/.type:internal_path'                           => ['protocol' => ''         , 'path_root' => 'dirs/.type'           , 'path_file' => 'internal_path', 'target' => 'file'],
            'dirs/name.type:internal_path'                       => ['protocol' => ''         , 'path_root' => 'dirs/name.type'       , 'path_file' => 'internal_path', 'target' => 'file'],
            '/dirs/:internal_path'                               => ['protocol' => ''         , 'path_root' => '/dirs/'               , 'path_file' => 'internal_path', 'target' => 'file'],
            '/dirs/name:internal_path'                           => ['protocol' => ''         , 'path_root' => '/dirs/name'           , 'path_file' => 'internal_path', 'target' => 'file'],
            '/dirs/.type:internal_path'                          => ['protocol' => ''         , 'path_root' => '/dirs/.type'          , 'path_file' => 'internal_path', 'target' => 'file'],
            '/dirs/name.type:internal_path'                      => ['protocol' => ''         , 'path_root' => '/dirs/name.type'      , 'path_file' => 'internal_path', 'target' => 'file'],
            'container://:internal_path'                         => ['protocol' => 'container', 'path_root' => ''                     , 'path_file' => 'internal_path', 'target' => 'file'],
            'container://name:internal_path'                     => ['protocol' => 'container', 'path_root' => 'name'                 , 'path_file' => 'internal_path', 'target' => 'file'],
            'container://.type:internal_path'                    => ['protocol' => 'container', 'path_root' => '.type'                , 'path_file' => 'internal_path', 'target' => 'file'],
            'container://name.type:internal_path'                => ['protocol' => 'container', 'path_root' => 'name.type'            , 'path_file' => 'internal_path', 'target' => 'file'],
            'container:///:internal_path'                        => ['protocol' => 'container', 'path_root' => '/'                    , 'path_file' => 'internal_path', 'target' => 'file'],
            'container:///name:internal_path'                    => ['protocol' => 'container', 'path_root' => '/name'                , 'path_file' => 'internal_path', 'target' => 'file'],
            'container:///.type:internal_path'                   => ['protocol' => 'container', 'path_root' => '/.type'               , 'path_file' => 'internal_path', 'target' => 'file'],
            'container:///name.type:internal_path'               => ['protocol' => 'container', 'path_root' => '/name.type'           , 'path_file' => 'internal_path', 'target' => 'file'],
            'container://dirs/:internal_path'                    => ['protocol' => 'container', 'path_root' => 'dirs/'                , 'path_file' => 'internal_path', 'target' => 'file'],
            'container://dirs/name:internal_path'                => ['protocol' => 'container', 'path_root' => 'dirs/name'            , 'path_file' => 'internal_path', 'target' => 'file'],
            'container://dirs/.type:internal_path'               => ['protocol' => 'container', 'path_root' => 'dirs/.type'           , 'path_file' => 'internal_path', 'target' => 'file'],
            'container://dirs/name.type:internal_path'           => ['protocol' => 'container', 'path_root' => 'dirs/name.type'       , 'path_file' => 'internal_path', 'target' => 'file'],
            'container:///dirs/:internal_path'                   => ['protocol' => 'container', 'path_root' => '/dirs/'               , 'path_file' => 'internal_path', 'target' => 'file'],
            'container:///dirs/name:internal_path'               => ['protocol' => 'container', 'path_root' => '/dirs/name'           , 'path_file' => 'internal_path', 'target' => 'file'],
            'container:///dirs/.type:internal_path'              => ['protocol' => 'container', 'path_root' => '/dirs/.type'          , 'path_file' => 'internal_path', 'target' => 'file'],
            'container:///dirs/name.type:internal_path'          => ['protocol' => 'container', 'path_root' => '/dirs/name.type'      , 'path_file' => 'internal_path', 'target' => 'file'],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'C:/'.':internal_path'                               => ['protocol' => ''         , 'path_root' => 'C:/'.''               , 'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'name:internal_path'                           => ['protocol' => ''         , 'path_root' => 'C:/'.'name'           , 'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'.type:internal_path'                          => ['protocol' => ''         , 'path_root' => 'C:/'.'.type'          , 'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'name.type:internal_path'                      => ['protocol' => ''         , 'path_root' => 'C:/'.'name.type'      , 'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'/:internal_path'                              => ['protocol' => ''         , 'path_root' => 'C:/'.'/'              , 'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'/name:internal_path'                          => ['protocol' => ''         , 'path_root' => 'C:/'.'/name'          , 'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'/.type:internal_path'                         => ['protocol' => ''         , 'path_root' => 'C:/'.'/.type'         , 'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'/name.type:internal_path'                     => ['protocol' => ''         , 'path_root' => 'C:/'.'/name.type'     , 'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'dirs/:internal_path'                          => ['protocol' => ''         , 'path_root' => 'C:/'.'dirs/'          , 'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'dirs/name:internal_path'                      => ['protocol' => ''         , 'path_root' => 'C:/'.'dirs/name'      , 'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'dirs/.type:internal_path'                     => ['protocol' => ''         , 'path_root' => 'C:/'.'dirs/.type'     , 'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'dirs/name.type:internal_path'                 => ['protocol' => ''         , 'path_root' => 'C:/'.'dirs/name.type' , 'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'/dirs/:internal_path'                         => ['protocol' => ''         , 'path_root' => 'C:/'.'/dirs/'         , 'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'/dirs/name:internal_path'                     => ['protocol' => ''         , 'path_root' => 'C:/'.'/dirs/name'     , 'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'/dirs/.type:internal_path'                    => ['protocol' => ''         , 'path_root' => 'C:/'.'/dirs/.type'    , 'path_file' => 'internal_path', 'target' => 'file'],
            'C:/'.'/dirs/name.type:internal_path'                => ['protocol' => ''         , 'path_root' => 'C:/'.'/dirs/name.type', 'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.':internal_path'                => ['protocol' => 'container', 'path_root' => 'C:/'.''               , 'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'name:internal_path'            => ['protocol' => 'container', 'path_root' => 'C:/'.'name'           , 'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'.type:internal_path'           => ['protocol' => 'container', 'path_root' => 'C:/'.'.type'          , 'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'name.type:internal_path'       => ['protocol' => 'container', 'path_root' => 'C:/'.'name.type'      , 'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'/:internal_path'               => ['protocol' => 'container', 'path_root' => 'C:/'.'/'              , 'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'/name:internal_path'           => ['protocol' => 'container', 'path_root' => 'C:/'.'/name'          , 'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'/.type:internal_path'          => ['protocol' => 'container', 'path_root' => 'C:/'.'/.type'         , 'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'/name.type:internal_path'      => ['protocol' => 'container', 'path_root' => 'C:/'.'/name.type'     , 'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'dirs/:internal_path'           => ['protocol' => 'container', 'path_root' => 'C:/'.'dirs/'          , 'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'dirs/name:internal_path'       => ['protocol' => 'container', 'path_root' => 'C:/'.'dirs/name'      , 'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'dirs/.type:internal_path'      => ['protocol' => 'container', 'path_root' => 'C:/'.'dirs/.type'     , 'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'dirs/name.type:internal_path'  => ['protocol' => 'container', 'path_root' => 'C:/'.'dirs/name.type' , 'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'/dirs/:internal_path'          => ['protocol' => 'container', 'path_root' => 'C:/'.'/dirs/'         , 'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'/dirs/name:internal_path'      => ['protocol' => 'container', 'path_root' => 'C:/'.'/dirs/name'     , 'path_file' => 'internal_path', 'target' => 'file'],
            'container://'.'C:/'.'/dirs/.type:internal_path'     => ['protocol' => 'container', 'path_root' => 'C:/'.'/dirs/.type'    , 'path_file' => 'internal_path', 'target' => 'file'],
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
                $handle = fopen($path_internal, 'rb'); stream_set_read_buffer($handle, 0); stream_set_write_buffer($handle, 0); $gotten&= fread($handle,  1) === '0'         ; fclose($handle);
                $handle = fopen($path_internal, 'rb'); stream_set_read_buffer($handle, 0); stream_set_write_buffer($handle, 0); $gotten&= fread($handle,  2) === '01'        ; fclose($handle);
                $handle = fopen($path_internal, 'rb'); stream_set_read_buffer($handle, 0); stream_set_write_buffer($handle, 0); $gotten&= fread($handle,  3) === '012'       ; fclose($handle);
                $handle = fopen($path_internal, 'rb'); stream_set_read_buffer($handle, 0); stream_set_write_buffer($handle, 0); $gotten&= fread($handle,  4) === '0123'      ; fclose($handle);
                $handle = fopen($path_internal, 'rb'); stream_set_read_buffer($handle, 0); stream_set_write_buffer($handle, 0); $gotten&= fread($handle,  5) === '01234'     ; fclose($handle);
                $handle = fopen($path_internal, 'rb'); stream_set_read_buffer($handle, 0); stream_set_write_buffer($handle, 0); $gotten&= fread($handle,  6) === '012345'    ; fclose($handle);
                $handle = fopen($path_internal, 'rb'); stream_set_read_buffer($handle, 0); stream_set_write_buffer($handle, 0); $gotten&= fread($handle,  7) === '0123456'   ; fclose($handle);
                $handle = fopen($path_internal, 'rb'); stream_set_read_buffer($handle, 0); stream_set_write_buffer($handle, 0); $gotten&= fread($handle,  8) === '01234567'  ; fclose($handle);
                $handle = fopen($path_internal, 'rb'); stream_set_read_buffer($handle, 0); stream_set_write_buffer($handle, 0); $gotten&= fread($handle,  9) === '012345678' ; fclose($handle);
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

    static function test_step_code__gallery_main_make(&$test, $dpath, &$c_results) {

        $dir_etalones = DIR_ROOT.Module::get('profile_classic')->path.'files/';
        $dir_src      = DIR_ROOT.Module::get('test'           )->path.'files/';
        $dir_dst      = Temporary::DIRECTORY.'test/gallery_main/';

        $gallery_items = [];

        if (!Directory::create($dir_dst)) {
            $c_results['reports'][$dpath][] = new Text_multiline([
                'Directory "%%_directory" cannot be created!',
                'Parent directory permissions are too strict!'], ['directory' => $dir_dst]);
            $c_results['return'] = 0;
            return;
        }

        $info = [
            'gallery-main-0.picture' => ['src_name' => '1000x1500-1.png', 'size' => ['small', 'middle', 'big']],
            'gallery-main-1.picture' => ['src_name' => '1000x1500-2.png', 'size' => ['small', 'middle', 'big']],
            'gallery-main-2.picture' => ['src_name' => '1000x1500-3.png', 'size' => ['small', 'middle', 'big']],
            'gallery-main-3.picture' => ['src_name' => '1500x1000-4.png', 'size' => ['small', 'middle', 'big']],
            'gallery-main-4.picture' => ['src_name' => '1500x1000-5.png', 'size' => ['small', 'middle', 'big']],
            'gallery-main-5.picture' => ['src_name' => '1500x1000-6.png', 'size' => ['small', 'middle', 'big']],
            'gallery-main-6.mp3'     => ['src_name' => 'audio.mp3'                                            ],
            'gallery-main-7.audio'   => ['src_name' => 'audio.mp3',       'size' => ['small', 'middle', 'big'], 'cover'  => 'cover.png' ],
            'gallery-main-8.mp4'     => ['src_name' => 'video.mp4'                                            ],
            'gallery-main-9.video'   => ['src_name' => 'video.mp4',       'size' => ['small', 'middle', 'big'], 'poster' => 'poster.png'],
        ];

        # delete: …/tmp/test/gallery_main/gallery-main-0.picture
        # delete: …/tmp/test/gallery_main/gallery-main-1.picture
        # delete: …/tmp/test/gallery_main/gallery-main-2.picture …
        foreach ($info as $c_dst_name => $c_info) {
            if (file_exists($dir_dst.$c_dst_name)) {
                if (File::delete($dir_dst.$c_dst_name)) {
                    $c_results['reports'][$dpath][] = new Text(
                        'File "%%_file" was deleted.', [
                        'file' => $dir_dst.$c_dst_name
                    ]);
                } else {
                    $c_results['reports'][$dpath][] = new Text_multiline([
                        'File "%%_file" was not deleted!',
                        'Directory permissions are too strict!'], [
                        'file' => $dir_dst.$c_dst_name]);
                    $c_results['return'] = 0;
                    return;
                }
            }
        }

        foreach ($info as $c_dst_name => $c_info) {

            # move: …/module_test/files/1000x1500-1.png → …/tmp/test/gallery_main/1000x1500-1.png
            # move: …/module_test/files/1000x1500-2.png → …/tmp/test/gallery_main/1000x1500-2.png
            # move: …/module_test/files/1000x1500-3.png → …/tmp/test/gallery_main/1000x1500-3.png
            $c_file             = new File($dir_src. $c_info['src_name']);
            $c_result_copy = $c_file->copy($dir_dst, $c_info['src_name'], true);
            if ($c_result_copy) {
                $c_results['reports'][$dpath][] = new Text(
                    'File "%%_file" was copied to "%%_to".', [
                    'file' => $dir_src.$c_info['src_name'],
                    'to'   => $dir_dst.$c_info['src_name']
                ]);
            } else {
                $c_results['reports'][$dpath][] = new Text_multiline([
                    'File "%%_file" was not copied to "%%_to"!',
                    'Directory permissions are too strict!'], [
                    'file' => $dir_src.$c_info['src_name'],
                    'to'   => $dir_dst.$c_info['src_name']]);
                $c_results['return'] = 0;
                return;
            }

            switch ((new File($c_dst_name))->type_get()) {
                case 'picture':
                    $c_file_history = new File_history;
                    if ($c_file_history->init_from_fin($c_file->path_get_relative())) {
                        # make container: …/tmp/test/gallery_main/1000x1500-1.png → …/tmp/test/gallery_main/1000x1500-1.picture
                        # make container: …/tmp/test/gallery_main/1000x1500-2.png → …/tmp/test/gallery_main/1000x1500-2.picture
                        # make container: …/tmp/test/gallery_main/1000x1500-3.png → …/tmp/test/gallery_main/1000x1500-3.picture
                        if ($c_file_history->container_picture_make(Core::array_keys_map($c_info['size']))) {
                            $c_results['reports'][$dpath][] = new Text(
                                'File "%%_file" was converted to "%%_to".', [
                                'file' => $c_file->path_get_absolute(),
                                'to'   => $c_file_history->get_current_path()]);
                            # rename: …/tmp/test/gallery_main/1000x1500-1.picture → …/tmp/test/gallery_main/gallery-main-0.picture
                            # rename: …/tmp/test/gallery_main/1000x1500-2.picture → …/tmp/test/gallery_main/gallery-main-1.picture
                            # rename: …/tmp/test/gallery_main/1000x1500-3.picture → …/tmp/test/gallery_main/gallery-main-2.picture
                            $c_file = new File($c_file_history->get_current_path());
                            if ($c_file->move($dir_dst, $c_dst_name)) {
                                $gallery_items[]= ['object' => $c_file_history];
                                     $c_results['reports'][$dpath][] = new Text('File "%%_file" was moved to "%%_to".',         ['file' => $c_file_history->get_current_path(), 'to' => $dir_dst.$c_dst_name]);
                            } else { $c_results['reports'][$dpath][] = new Text('File "%%_file" was not moved to "%%_to"!',     ['file' => $c_file_history->get_current_path(), 'to' => $dir_dst.$c_dst_name]); $c_results['return'] = 0; return; }
                        }     else { $c_results['reports'][$dpath][] = new Text('File "%%_file" was not converted to "%%_to"!', ['file' => $c_file_history->get_current_path(), 'to' => '*.picture'         ]); $c_results['return'] = 0; return; }
                    }         else { $c_results['reports'][$dpath][] = new Text('picture error: init_from_fin()');                                                                                              $c_results['return'] = 0; return; }
                    break;

                case 'audio':
                    $c_file_history = new File_history;
                    if ($c_file_history->init_from_fin($c_file->path_get_relative())) {
                        # make container: …/tmp/test/gallery_main/audio.mp3 → …/tmp/test/gallery_main/audio.audio
                        if ($c_file_history->container_audio_make(Core::array_keys_map($c_info['size']), isset($c_info['cover']) ? $dir_src.$c_info['cover'] : null)) {
                            $c_results['reports'][$dpath][] = new Text(
                                'File "%%_file" was converted to "%%_to".', [
                                'file' => $c_file->path_get_absolute(),
                                'to'   => $c_file_history->get_current_path()]);
                            # rename: …/tmp/test/gallery_main/audio.audio → …/tmp/test/gallery_main/gallery-main-7.audio
                            $c_file = new File($c_file_history->get_current_path());
                            if ($c_file->move($dir_dst, $c_dst_name)) {
                                $gallery_items[]= ['object' => $c_file_history];
                                     $c_results['reports'][$dpath][] = new Text('File "%%_file" was moved to "%%_to".',         ['file' => $c_file_history->get_current_path(), 'to' => $dir_dst.$c_dst_name]);
                            } else { $c_results['reports'][$dpath][] = new Text('File "%%_file" was not moved to "%%_to"!',     ['file' => $c_file_history->get_current_path(), 'to' => $dir_dst.$c_dst_name]); $c_results['return'] = 0; return; }
                        }     else { $c_results['reports'][$dpath][] = new Text('File "%%_file" was not converted to "%%_to"!', ['file' => $c_file_history->get_current_path(), 'to' => '*.audio'           ]); $c_results['return'] = 0; return; }
                    }         else { $c_results['reports'][$dpath][] = new Text('audio error: init_from_fin()');                                                                                                $c_results['return'] = 0; return; }
                    break;

                case 'video':
                    $c_file_history = new File_history;
                    if ($c_file_history->init_from_fin($c_file->path_get_relative())) {
                        # make container: …/tmp/test/gallery_main/video.mp4 → …/tmp/test/gallery_main/video.video
                        if ($c_file_history->container_video_make(Core::array_keys_map($c_info['size']), isset($c_info['poster']) ? $dir_src.$c_info['poster'] : null)) {
                            $c_results['reports'][$dpath][] = new Text(
                                'File "%%_file" was converted to "%%_to".', [
                                'file' => $c_file->path_get_absolute(),
                                'to'   => $c_file_history->get_current_path()]);
                            # rename: …/tmp/test/gallery_main/video.video → …/tmp/test/gallery_main/gallery-main-9.video
                            $c_file = new File($c_file_history->get_current_path());
                            if ($c_file->move($dir_dst, $c_dst_name)) {
                                $gallery_items[]= ['object' => $c_file_history];
                                     $c_results['reports'][$dpath][] = new Text('File "%%_file" was moved to "%%_to".',         ['file' => $c_file_history->get_current_path(), 'to' => $dir_dst.$c_dst_name]);
                            } else { $c_results['reports'][$dpath][] = new Text('File "%%_file" was not moved to "%%_to"!',     ['file' => $c_file_history->get_current_path(), 'to' => $dir_dst.$c_dst_name]); $c_results['return'] = 0; return; }
                        }     else { $c_results['reports'][$dpath][] = new Text('File "%%_file" was not converted to "%%_to"!', ['file' => $c_file_history->get_current_path(), 'to' => '*.video'           ]); $c_results['return'] = 0; return; }
                    }         else { $c_results['reports'][$dpath][] = new Text('video error: init_from_fin()');                                                                                                $c_results['return'] = 0; return; }
                    break;

                case 'mp3':
                case 'mp4':
                    # rename: …/tmp/test/gallery_main/audio.mp3 → …/tmp/test/gallery_main/gallery-main-6.mp3
                    # rename: …/tmp/test/gallery_main/video.mp4 → …/tmp/test/gallery_main/gallery-main-8.mp4
                    $c_old_path = $c_file->path_get_absolute();
                    if ($c_file->move($dir_dst, $c_dst_name)) {
                        $gallery_items[]= ['object' => new File_history($c_file->path_get_relative())];
                             $c_results['reports'][$dpath][] = new Text('File "%%_file" was moved to "%%_to".',     ['file' => $c_old_path, 'to' => $dir_dst.$c_dst_name]);
                    } else { $c_results['reports'][$dpath][] = new Text('File "%%_file" was not moved to "%%_to"!', ['file' => $c_old_path, 'to' => $dir_dst.$c_dst_name]); $c_results['return'] = 0; return; }
                    break;
            }
        }

        # comparison with etalones
        foreach ($info as $c_dst_name => $c_info) {
            $c_file_etalone = new File($dir_etalones.$c_dst_name);
            $c_file_compare = new File($dir_dst     .$c_dst_name);
            $c_gotten   = $c_file_compare->hash_get();
            $c_expected = $c_file_etalone->hash_get();
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'hash: '.$c_dst_name, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'hash: '.$c_dst_name, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        $gallery_data = Storage_Data::data_to_text($gallery_items, 'items');
        $c_results['reports'][$dpath][] = $gallery_data;
    }

}
