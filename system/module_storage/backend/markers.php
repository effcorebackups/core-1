<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore { # indicates that the class should clear own cache after install any new module
          interface should_clear_cache_after_on_install {
  static function cache_cleaning();
}}

namespace effcore { # indicates that the cache should be separated by files
          interface has_external_cache {
  static function not_external_properties_get();
}}

namespace effcore { # indicates that the __construct() should be called after the data load
          interface has_postconstructor {
}}

namespace effcore { # indicates that the _postinit() should be called after the data load
          interface has_postinit {
}}

namespace effcore { # indicates that the _postparse() should be called after parse the data
          interface has_postparse {
}}
