<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\core;

use const effcore\DIR_DYNAMIC;
use const effcore\DIR_ROOT;
use effcore\Cache;
use effcore\Core;
use effcore\Entity;
use effcore\Message;
use effcore\Module;
use effcore\Text;

abstract class Events_Module_update {

    static function on_update_files($event, $bundle_id) {
        return static::on_update_files__git($event, $bundle_id);
    }

    static function on_repo_restore($event, $bundle_id) {
        return static::on_repo_restore__git($event, $bundle_id);
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function on_update_files__git($event, $bundle_id) {
        $bundle = Module::bundle_get($bundle_id);
        if ($bundle) {
            $stderr_to_stdout = '2>&1';
            $repo_path = realpath(DIR_ROOT.$bundle->path.$bundle->repo_directory);
            if ($repo_path !== false) {
                $result = [];
                $commands = [
                    'hostname '                             .$stderr_to_stdout,
                    'whoami '                               .$stderr_to_stdout,
                    'git --version '                        .$stderr_to_stdout,
                    'cd '.$repo_path.' && git clean  -f -d '.$stderr_to_stdout,
                    'cd '.$repo_path.' && git reset --hard '.$stderr_to_stdout,
                    'cd '.$repo_path.' && git pull '        .$stderr_to_stdout];
                foreach ($commands as $c_num => $c_command) {
                    $return_var = null;
                    $result['command-'.$c_num] = '$ '.$c_command;
                    exec($c_command, $result, $return_var);
                    if ($return_var !== 0) break;
                }
                Cache::update_global();
                Message::insert('All caches was reset.');
                return $result;
            }
        }
    }

    static function on_repo_restore__git($event, $bundle_id) {
        $bundle = Module::bundle_get($bundle_id);
        if ($bundle) {
            $stderr_to_stdout        = '2>&1';
            $stderr_to_stdout_to_nul = '2>&1 > nul & exit 0';
            $repo_path_cur = realpath(DIR_ROOT.$bundle->path.$bundle->repo_directory);
            $repo_path_tmp = realpath(DIR_DYNAMIC.'tmp').DIRECTORY_SEPARATOR.'.git_restore-'.$bundle_id;
            if ($repo_path_cur !== false) {
                $result = [];
                if (Core::php_is_on_win()) {
                    $commands = [
                        'hostname '                                                                               .$stderr_to_stdout,
                        'whoami '                                                                                 .$stderr_to_stdout,
                        'git --version '                                                                          .$stderr_to_stdout,
                        'del /f /s /q '.$repo_path_tmp.                                                        ' '.$stderr_to_stdout_to_nul,
                        'rmdir  /s /q '.$repo_path_tmp.                                                        ' '.$stderr_to_stdout_to_nul,
                        'git clone --branch='.$bundle->repo_branch.' '.$bundle->repo_origin.' '.$repo_path_tmp.' '.$stderr_to_stdout,
                        'del /f /s /q '.$repo_path_cur.'\\.git '.                                              ' '.$stderr_to_stdout_to_nul,
                        'rmdir  /s /q '.$repo_path_cur.'\\.git '.                                              ' '.$stderr_to_stdout_to_nul,
                        'xcopy  /e /i '.$repo_path_tmp.'\\.git '.$repo_path_cur.'\\.git'.                      ' '.$stderr_to_stdout,
                        'del /f /s /q '.$repo_path_tmp.                                                        ' '.$stderr_to_stdout_to_nul,
                        'rmdir  /s /q '.$repo_path_tmp.                                                        ' '.$stderr_to_stdout_to_nul
                    ];
                } else {
                    $commands = [
                        'hostname '                                                                               .$stderr_to_stdout,
                        'whoami '                                                                                 .$stderr_to_stdout,
                        'git --version '                                                                          .$stderr_to_stdout,
                        'rm -rf '.$repo_path_tmp.                                                              ' '.$stderr_to_stdout,
                        'git clone --branch='.$bundle->repo_branch.' '.$bundle->repo_origin.' '.$repo_path_tmp.' '.$stderr_to_stdout,
                        'rm -rf '.$repo_path_cur.'/.git '                                                         .$stderr_to_stdout,
                        'mv '    .$repo_path_tmp.'/.git '.$repo_path_cur.                                      ' '.$stderr_to_stdout,
                        'rm -rf '.$repo_path_tmp.                                                              ' '.$stderr_to_stdout
                    ];
                }
                foreach ($commands as $c_num => $c_command) {
                    $return_var = null;
                    $result['command-'.$c_num] = '$ '.$c_command;
                    exec($c_command, $result, $return_var);
                    if ($return_var !== 0) break;
                }
                return $result;
            }
        }
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function on_update_data_1000($update) {
        $entity = Entity::get('message');
        if ($entity->install())
             {Message::insert(new Text('Entity "%%_entity" was installed.',     ['entity' => $entity->name])         ); return true; }
        else {Message::insert(new Text('Entity "%%_entity" was not installed!', ['entity' => $entity->name]), 'error'); return false;}
    }

}
