<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use const \effcore\dir_root;
          use \effcore\cache;
          use \effcore\entity;
          use \effcore\message;
          use \effcore\module;
          use \effcore\text;
          abstract class events_module_update {

  static function on_update_files($event, $bundle_id) {
    $bundle = module::bundle_get($bundle_id);
    if ($bundle) {
      $repo_path = realpath(dir_root.$bundle->path.'/..');
      if ($repo_path !== false) {
        $result = [];
        $commands = [
          'git -C '.$repo_path.' clean  -f -d',
          'git -C '.$repo_path.' reset --hard',
          'git -C '.$repo_path.' pull'];
        foreach ($commands as $c_num => $c_command) {
          $return_var = null;
          $result['command-'.$c_num] = $c_command;
          exec($c_command, $result, $return_var);
          if ($return_var !== 0) break;
        }
        cache::update_global();
        message::insert('All caches was reset.');
        return $result;
      }
    }
  }

  static function on_update_data_1000($update) {
    $entity = entity::get('message');
    if ($entity->install())
         {message::insert(new text('Entity "%%_entity" was installed.',     ['entity' => $entity->name])         ); return true; }
    else {message::insert(new text('Entity "%%_entity" was not installed!', ['entity' => $entity->name]), 'error'); return false;}
  }

}}