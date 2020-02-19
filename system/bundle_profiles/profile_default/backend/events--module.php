<?php

  ######################################################################
  ### Copyright © 20NN—20NN Author/Rightholder. All rights reserved. ###
  ######################################################################

namespace effcore\modules\profile_default {
          use \effcore\message;
          use \effcore\module;
          use \effcore\page;
          use \effcore\text_multiline;
          abstract class events_module {

  static $conflict_has_been = false;

  static function on_install($event) {
    $module = module::get('profile_default');
    $page_ids = [];
    if (page::get('front'       )) $page_ids[] = 'front';
    if (page::get('login'       )) $page_ids[] = 'login';
    if (page::get('logout'      )) $page_ids[] = 'logout';
    if (page::get('recovery'    )) $page_ids[] = 'recovery';
    if (page::get('registration')) $page_ids[] = 'registration';
    if (!count($page_ids)) {
      $module->install();
    } else {
      static::$conflict_has_been = true;
      message::insert(new text_multiline([
        'Unable to install the profile "%%_profile" because the system already has Pages with the following IDs: %%_ids',
        'Uninstall the existing profile first.'], ['profile' => $module->title, 'ids' => implode(', ', $page_ids)]), 'warning'
      );
    }
  }

  static function on_uninstall($event) {
    $module = module::get('profile_default');
    $module->uninstall();
  }

  static function on_enable($event) {
    if (!static::$conflict_has_been) {
      $module = module::get('profile_default');
      $module->enable();
    }
  }

  static function on_disable($event) {
    $module = module::get('profile_default');
    $module->disable();
  }

}}