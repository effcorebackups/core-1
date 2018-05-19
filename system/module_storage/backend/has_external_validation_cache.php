<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {

# indicates that the validation cache for marked pattern should be loaded from file
  interface has_external_validation_cache {
    static function get_validation_cache_properties();
  }

}