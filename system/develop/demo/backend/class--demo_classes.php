<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          const my_const_2 = 'const value';
          use const \effcore\br;
          use \effcore\markup;
          class demo_class_1 {

  public    $property_publ;
  protected $property_prot;
  private   $property_priv = 'property priv';

  public function method_1() {
    return $this->property_priv; # return 'property priv'
  }

  ###########################
  ### static declarations ###
  ###########################

  static public    $static_property_publ;
  static protected $static_property_prot;
  static private   $static_property_priv = my_const_2;

  static public function static_method_1() { # return 'const value'
    return static::$static_property_priv;
  }

}}



namespace effcore\modules\demo {
          use const \effcore\nl;
          use \effcore\locale;
          class demo_class_2 extends demo_class_1 {

  private $property_priv = 'property priv modified';
  private $property_priv_new = 'property priv new';

  public function method_1() {
    return $this->property_priv; # return 'property priv modified'
  }

  public function method_2() {
    return $this->property_priv_new; # return 'property priv new'
  }

  ###########################
  ### static declarations ###
  ###########################

  static private $static_property_priv = my_const_2.' modified';
  static private $static_property_priv_new = 'static property priv new';

  static public function static_method_1() {
    return static::$static_property_priv; # return 'const value modified'
  }

  static public function static_method_2() {
    return static::$static_property_priv_new; # return 'static property priv new'
  }

}}
