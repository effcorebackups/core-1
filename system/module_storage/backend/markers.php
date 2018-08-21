<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
# indicates that the cache for marked pattern should be separated by files
  interface has_external_cache {
    static function not_external_properties_get();
  }
}

namespace effcore {
# indicates that the __construct() should be called after the data load
  interface has_post_constructor {}
}

namespace effcore {
# indicates that the __post_init() should be called after the data load
  interface has_post_init {}
}

namespace effcore {
# indicates that the __post_parse() should be called after parse the data
  interface has_post_parse {}
}
