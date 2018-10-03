<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\manage_instances;
          use \effcore\page;
          abstract class events_form_instance_delete {

  static function on_submit($form, $items) {
    manage_instances::instance_delete_by_entity_name_and_instance_id(
      page::current_get()
    );
  }

}}