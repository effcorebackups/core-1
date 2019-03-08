<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class template_text extends template {

  function render() {
    if (is_object($this->data) &&
                  $this->data instanceof text) {
             $this->data->args = $this->args;
      return $this->data->render();
    }
    if (is_string($this->data)) {
      $rendered = $this->data;
      $rendered = preg_replace_callback('%(?<spacer>[ ]*)'.
                                         '(?<prefix>\\%\\%_)'.
                                         '(?<name>[a-z0-9_]+)'.
                                         '(?<args>\\{[a-z0-9_,]+\\}|)%S', function($c_match) {
        return isset($c_match['prefix']) &&
               isset($c_match['name']) &&
               isset($this->args[$c_match['name']]) &&
                     $this->args[$c_match['name']] !== '' ? $c_match['spacer'].
                     $this->args[$c_match['name']] : '';
      },     $rendered);
      return $rendered;
    }
  }

}}