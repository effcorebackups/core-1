<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class manage_trees {

  static function tree_select($page) {
    return new markup('div', [], 'tree_select');
  }

  static function tree_insert($page) {
    return new markup('div', [], 'tree_insert');
  }

  static function tree_update($page) {
  }

  static function tree_delete($page) {
  }

}}