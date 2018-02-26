<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {

  interface has_different_cache {

    # indicates that the cache for marked pattern should be separated by files

    static function get_non_different_properties();

  }

}