<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\manage_instances;
          use \effcore\page;
          use \effcore\text;
          use \effcore\url;
          abstract class events_form_instance_update {

  static function on_init($form, $items) {
    manage_instances::instance_update(page::current_get(), true); # emulation for access checking
    $items['fields']->child_insert(
      new text('instance_update is UNDER CONSTRUCTION')
    );
  # @todo: make functionality
  }

  static function on_submit($form, $items) {
    $base        = page::current_get()->args_get('base');
    $entity_name = page::current_get()->args_get('entity_name');
    $instance_id = page::current_get()->args_get('instance_id');
    switch ($form->clicked_button->value_get()) {
      case 'update':
      # @todo: make functionality
        break;
      case 'cancel':
        url::go(url::back_url_get() ?: $base.'/select/'.$entity_name);
        break;
    }
  }

}}