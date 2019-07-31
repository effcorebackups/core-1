<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          abstract class events_examples {

  static function on_page_build_before($event, $page) {
  }

  static function on_page_build_after($event, $page) {
  }

  static function on_page_render_before($event, $page, $template) {
  }

  static function on_decorator_build_before($event, $decorator) {
  }

  static function on_decorator_build_after($event, $decorator) {
  }

  static function on_selection_build_before($event, $selection) {
  }

  static function on_selection_build_after($event, $selection) {
  }

  static function on_tab_build_before($event, $tab) {
  }

  static function on_tab_build_after($event, $tab) {
  }

  static function on_tree_build_before($event, $tree) {
  }

  static function on_tree_build_after($event, $tree) {
  }

  static function on_email_send_before($event, &$to, &$subject, &$body, &$from, &$encoding, &$form, &$items) {
  }

  static function on_instance_select_before($event, $instance) {
  }

  static function on_instance_select_after($event, $instance, $result) {
  }

  static function on_instance_insert_before($event, $instance) {
  }

  static function on_instance_insert_after($event, $instance, $result) {
  }

  static function on_instance_update_before($event, $instance) {
  }

  static function on_instance_update_after($event, $instance, $result) {
  }

  static function on_instance_delete_before($event, $instance) {
  }

  static function on_instance_delete_after($event, $instance, $result) {
  }

}}
