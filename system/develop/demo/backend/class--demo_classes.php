<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          const my_const_2 = 300;
          use const \effcore\br;
          use \effcore\markup;
          class demo_class_1 {

  public    $property_1;
  protected $property_2;
  private   $property_3 = 'default string';

  public function method_1() {return $this->property_3;} # return 'default string'

  ###########################
  ### static declarations ###
  ###########################

  static public    $static_property_1;
  static protected $static_property_2;
  static private   $static_property_3 = my_const_2;

  static public function static_method_1() {return static::$static_property_3;} # return 300

}}



namespace effcore\modules\demo {
          use const \effcore\nl;
          use \effcore\locale;
          class demo_class_2 extends demo_class_1 {

  private $property_3 = 'modified string';
  private $property_4 = 'new property #4';

  public function method_1() {return $this->property_3;} # return 'modified string'
  public function method_2() {return $this->property_4;} # return 'new property #4'

  ###########################
  ### static declarations ###
  ###########################

  static private $static_property_3 = my_const_2 + 100;
  static private $static_property_4 = 'new static property #4';

  static public function static_method_1() {return static::$static_property_3;} # return 400
  static public function static_method_2() {return static::$static_property_4;} # return 'new static property #4'

}}
