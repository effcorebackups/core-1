<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
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
      $rendered = preg_replace_callback('%(?<spaces>[ ]{0,})'.'\\%\\%_'.
                                         '(?<name>[a-z0-9_]{1,64})'.
                                '(?:\\{'.'(?<args>[^\\}\\n]{1,1024})'.'\\}|)%S', function ($c_match) {
        return isset(            $c_match['name'])  &&
               isset($this->args[$c_match['name']]) &&
                     $this->args[$c_match['name']] !== '' ? $c_match['spaces'].
                     $this->args[$c_match['name']] : '';
      },     $rendered);
      return $rendered;
    }
  }

}}