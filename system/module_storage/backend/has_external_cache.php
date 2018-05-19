<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {

  interface has_external_cache {

    # indicates that the cache for marked pattern should be separated by files

    static function get_not_external_properties();

  }

}