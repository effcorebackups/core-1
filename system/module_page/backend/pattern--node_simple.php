<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class node_simple {

  public $template;
  public $attributes = [];
  public $weight = 0;

  function __construct($attributes = [], $weight = 0) {
    $this->weight = $weight;
    foreach ($attributes as $id => $c_attribute) {
      $this->attribute_insert($id, $c_attribute);
    }
  }

  ##################
  ### attributes ###
  ##################

  function attribute_select($key, $scope = 'attributes') {
    return $this->{$scope}[$key] ?? null;
  }

  function attributes_select($scope = 'attributes') {
    return $this->{$scope};
  }

  function attribute_insert($key, $data, $scope = 'attributes') {
    if (is_array($data)) {
      foreach ($data as $c_key => $c_value) {
        $this->{$scope}[$key][$c_key] = $c_value;
      }
    } else {
      $this->{$scope}[$key] = $data;
    }
  }

  function attribute_delete($key, $scope = 'attributes') {
    unset($this->{$scope}[$key]);
  }

  ##############
  ### render ###
  ##############

  function render() {
    if ($this->template) {
      return (new template($this->template, [
        'attributes' => $this->render_attributes(),
        'self'       => $this->render_self(),
      ]))->render();
    } else {
      return $this->render_self();
    }
  }

  function render_attributes() {
    return core::data_to_attr($this->attributes_select());
  }

  function render_self() {
    return $this->title ?? '';
  }

}}