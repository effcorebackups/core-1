<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class tree_item_managed extends tree_item {

  function render() {
    $this->access = null;
    return parent::render();
  }

  function render_self() {
    return (new markup('a', $this->attributes_select(), [
      new markup('x-item-title', [], $this->title),
      new markup('x-url', [], $this->url ? str_replace('/', (new markup('em', [], '/'))->render(), $this->url) : 'no url')
    ]))->render();
  }

}}