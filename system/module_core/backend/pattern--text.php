<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class text extends text_raw {

  function render() {
    return translation::get($this->text, $this->args);
  }

}}