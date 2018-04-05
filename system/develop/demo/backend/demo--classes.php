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
          use \effcore\locale;     # example of not fully qualified name of extends
          class demo_class_2 extends demo_class_1 {

  private $property_priv = 'property priv modified';
  private $property_priv_new = 'property priv new';

  public function method_1() {
  # return 'property priv modified'
    return $this->property_priv;
  }

  public function method_2() {
  # return 'property priv new'
    return $this->property_priv_new;
  }

  ###########################
  ### static declarations ###
  ###########################

  static private $static_property_priv = my_const_2.' modified';
  static private $static_property_priv_new = 'static property priv new';

  static public function static_method_1() {
  # return 'const value modified'
    return static::$static_property_priv;
  }

  static public function static_method_2() {
  # return 'static property priv new'
    return static::$static_property_priv_new;
  }

}}
