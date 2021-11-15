<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class node_simple {

  public $template;
  public $is_xml_style = false;
  public $attributes = [];
  public $weight = 0;
  public $is_builded = false;

  function __construct($attributes = [], $weight = 0) {
    $this->weight = $weight;
    foreach ($attributes as $c_key => $c_value) {
      $this->attribute_insert($c_key, $c_value);
    }
  }

  ##################
  ### attributes ###
  ##################

  function attribute_select($name, $scope = 'attributes') {
    return $this->{$scope}[$name] ?? null;
  }

  function attributes_select($scope = 'attributes') {
    return $this->{$scope};
  }

  function attribute_insert($name, $data, $scope = 'attributes', $at_first = false) {
    if (is_array($this->{$scope}) !== true) $this->{$scope} = [];
    if ($at_first === true) {unset($this->{$scope}[$name]); $this->{$scope} = [$name => $data] + $this->{$scope};}
    if ($at_first !== true)        $this->{$scope}[$name] =                             $data;
    return $this;
  }

  function attribute_delete($name, $scope = 'attributes') {
    unset($this->{$scope}[$name]);
    return $this;
  }

  ##############
  ### render ###
  ##############

  function render() {
    if ($this->template) {
      return (template::make_new($this->template, [
        'attributes' => $this->render_attributes(),
        'self'       => $this->render_self(),
      ]))->render();
    } else {
      return $this->render_self();
    }
  }

  function render_attributes() {
    if ($this->is_xml_style)
         return core::data_to_attributes($this->attributes_select(), true);
    else return core::data_to_attributes($this->attributes_select()      );
  }

  function render_self() {
    return $this->title ?? '';
  }

}}