<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore { # indicates that the control is the complex element
          interface complex_control {
  function  name_get_complex();
  function value_get_complex();
  function value_set_complex($value);
}}
