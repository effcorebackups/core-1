<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class diagram extends container {

  public $tag_name = 'x-diagram';
  public $type = 'linear'; # linear|circular
  public $slices = [];

  function __construct($title = null, $type = null, $attributes = [], $weight = 0) {
    if ($type) $this->type = $type;
    parent::__construct(null, $title, null, $attributes, [], $weight);
  }

  function build() {
    $this->attribute_insert('data-type', $this->type);
    switch ($this->type) {
      case 'linear':
        $diagram = new markup('dl');
        $this->child_insert($diagram, 'diagram');
        foreach ($this->slices as $c_slice) {
          $diagram->child_insert(new markup('dt', [], $c_slice->title));
          $diagram->child_insert(new markup('dd', [], [
            $c_slice->complex_value ?
            $c_slice->complex_value.' ('.locale::format_persent($c_slice->persent_value, 1).')' :
                                         locale::format_persent($c_slice->persent_value, 1),
            new markup('x-scale', [
              'class' => ['scope' => core::to_css_class($c_slice->title)],
              'style' => ['width: '.(int)$c_slice->persent_value.'%']
            ])
          ]));
        }
        break;
      case 'circular':
        break;
    }
  }

  function slice_add($title, $persent_value, $complex_value = null, $color = null) {
    $this->slices[] = (object)[
      'title'         => $title,
      'persent_value' => $persent_value,
      'complex_value' => $complex_value,
      'color'         => $color
    ];
  }

  function render() {
    $this->build();
    return parent::render();
  }

}}