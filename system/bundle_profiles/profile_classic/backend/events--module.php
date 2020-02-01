<?php

  ######################################################################
  ### Copyright © 20NN—20NN Author/Rightholder. All rights reserved. ###
  ######################################################################

namespace effcore\modules\profile_classic {
          use \effcore\message;
          use \effcore\module;
          use \effcore\page;
          use \effcore\text_multiline;
          abstract class events_module {

  static $conflict_has_been = false;

  static function on_install($event) {
    $page_ids = [];
    if (page::get('about'       )) $page_ids[] = 'about';
    if (page::get('contact'     )) $page_ids[] = 'contact';
    if (page::get('front'       )) $page_ids[] = 'front';
    if (page::get('login'       )) $page_ids[] = 'login';
    if (page::get('logout'      )) $page_ids[] = 'logout';
    if (page::get('recovery'    )) $page_ids[] = 'recovery';
    if (page::get('registration')) $page_ids[] = 'registration';
    if (!count($page_ids)) {
      $module = module::get('profile_classic');
      $module->install();
    } else {
      static::$conflict_has_been = true;
      message::insert(new text_multiline([
        'This profile cannot be installed because the system already has Pages with the following IDs: %%_ids',
        'Uninstall the existing profile first.'], ['ids' => implode(', ', $page_ids)]), 'warning'
      );
    }
  }

  static function on_uninstall($event) {
    $module = module::get('profile_classic');
    $module->uninstall();
  }

  static function on_enable($event) {
    if (!static::$conflict_has_been) {
      $module = module::get('profile_classic');
      $module->enable();
    }
  }

  static function on_disable($event) {
    $module = module::get('profile_classic');
    $module->disable();
  }

}}