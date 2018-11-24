<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_timezone extends field_select {

  public $attributes = ['data-type' => 'timezone'];

  function build() {
    parent::build();
    $this->option_insert('- select -', 'not_selected');
    foreach (\DateTimeZone::listIdentifiers() as $c_id => $c_title) {
      $this->option_insert($c_title, $c_id);
    }
  }

  static function title_by_id_get($id) {
    foreach (\DateTimeZone::listIdentifiers() as $c_id => $c_title) {
      if ($c_id == $id) {
        return $c_title;
      }
    }
  }

  static function id_by_title_get($title) {
    foreach (\DateTimeZone::listIdentifiers() as $c_id => $c_title) {
      if ($c_title == $title) {
        return $c_id;
      }
    }
  }

}}