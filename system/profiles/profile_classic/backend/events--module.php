<?php

  ######################################################################
  ### Copyright © 20NN—20NN Author/Rightholder. All rights reserved. ###
  ######################################################################

namespace effcore\modules\profile_classic {
          use \effcore\color_preset;
          use \effcore\field;
          use \effcore\message;
          use \effcore\module;
          use \effcore\page;
          use \effcore\text_multiline;
          abstract class events_module {

  static $is_failed_installation = false;

  static function on_install($event) {
    $module = module::get('profile_classic');
    if (page::get_current()->id === 'install') {
      color_preset::apply('original_classic');
      $module->install();
    }
    if (page::get_current()->id !== 'install') {
      $page_ids = [];
      if (page::get_by_id('about'       )) $page_ids[] = 'about';
      if (page::get_by_id('contact'     )) $page_ids[] = 'contact';
      if (page::get_by_id('front'       )) $page_ids[] = 'front';
      if (page::get_by_id('login'       )) $page_ids[] = 'login';
      if (page::get_by_id('logout'      )) $page_ids[] = 'logout';
      if (page::get_by_id('recovery'    )) $page_ids[] = 'recovery';
      if (page::get_by_id('registration')) $page_ids[] = 'registration';
      if (page::get_by_id('user_edit'   )) $page_ids[] = 'user_edit';
      if (page::get_by_id('user'        )) $page_ids[] = 'user';
      if (!count($page_ids)) {
        color_preset::apply('original_classic');
        $module->install();
      } else {
        static::$is_failed_installation = true;
        message::insert(new text_multiline([
          'Unable to install the profile "%%_profile" because the system already has Pages with the following IDs: %%_ids',
          'Uninstall the existing profile first.'], ['profile' => $module->title, 'ids' => implode(', ', $page_ids)]), 'warning'
        );
      }
    }
  }

  static function on_uninstall($event) {
    color_preset::reset();
    $module = module::get('profile_classic');
    $module->uninstall();
  }

  static function on_enable($event) {
    if (!static::$is_failed_installation) {
      $module = module::get('profile_classic');
      $module->enable();
    }
  }

  static function on_disable($event) {
    $module = module::get('profile_classic');
    $module->disable();
  }

}}