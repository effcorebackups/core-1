<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class diagram extends container {

  public $tag_name = 'x-diagram';
  public $type = 'linear'; # linear|radial
  public $slices = [];

  function __construct($title = null, $type = null, $attributes = [], $weight = 0) {
    if ($type) $this->type = $type;
    parent::__construct(null, $title, null, $attributes, [], $weight);
  }

  function build() {
    $this->attribute_insert('data-type', $this->type);
    switch ($this->type) {

      case 'linear':
        foreach ($this->slices as $c_slice) {
          $x_slice = new markup('x-slice');
          $x_slice->child_insert(new markup('x-param', [], $c_slice->title));
          $x_slice->child_insert(new markup('x-value', [], [
            $c_slice->complex_value ?
            $c_slice->complex_value.' ('.locale::format_persent($c_slice->persent_value, 1).')' :
                                         locale::format_persent($c_slice->persent_value, 1),
            new markup('x-scale', [
              'class' => ['scope' => core::to_css_class($c_slice->title)],
              'style' => ['width: '.(int)$c_slice->persent_value.'%']
            ])
          ]));
          $this->child_insert($x_slice);
        }
        break;

      case 'radial':
        $coords = ['r' => '25%', 'cx' => '50%', 'cy' => '50%'];
        $diagram = new markup_xml('svg', ['viewBox' => '0 0 64 64', 'width' => '100', 'height' => '100']);
        $legends = new markup('x-legends');
        $diagram->child_insert(new markup_xml_simple('circle', $coords + ['style' => 'stroke: lightgray; stroke-width: 30%; fill: none']));
        $this->child_insert($diagram, 'diagram');
        $this->child_insert($legends, 'legends');
        $c_offset = 0;
        foreach ($this->slices as $c_slice) {
          $c_percent = (int)$c_slice->persent_value;
          $diagram->child_insert(new markup_xml_simple('circle', $coords + ['style' =>
            'stroke: '.$c_slice->color.'; '.
            'stroke-dasharray: '.$c_percent.' 100; '.
            'stroke-dashoffset: '.$c_offset.'; '.
            'stroke-width: 30%; '.
            'fill: none']));
          $c_offset -= $c_percent;
          $x_legend = new markup('x-legend');
          $x_legend->child_insert(new markup('x-color', ['style' => 'background: '.$c_slice->color]));
          $x_legend->child_insert(new markup('x-param', [], $c_slice->title));
          $x_legend->child_insert(new markup('x-value', [], [
            $c_slice->complex_value ?
            $c_slice->complex_value.' ('.locale::format_persent($c_slice->persent_value, 1).')' :
                                         locale::format_persent($c_slice->persent_value, 1)
          ]));
          $legends->child_insert($x_legend);
        }
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