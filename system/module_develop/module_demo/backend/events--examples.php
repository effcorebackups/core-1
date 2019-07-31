<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          abstract class events_examples {

  static function on_before_build($event, $page) {
  }

  static function on_after_build($event, $page) {
  }

  static function on_before_render($event, $page, $template) {
  }

  static function on_decorator_build_before($event, $decorator) {
  }

  static function on_decorator_build_after($event, $decorator) {
  }

  static function on_selection_before_build($event, $selection) {
  }

  static function on_selection_build_after($event, $selection) {
  }

  static function on_tab_before_build($event, $tab) {
  }

  static function on_tab_after_build($event, $tab) {
  }

  static function on_tree_before_build($event, $tree) {
  }

  static function on_tree_after_build($event, $tree) {
  }

  static function on_email_send_before($event, &$to, &$subject, &$body, &$from, &$encoding, &$form, &$items) {
  }

  static function on_instance_select_before($event, $instance) {
  }

  static function on_instance_select_after($event, $instance) {
  }

}}
