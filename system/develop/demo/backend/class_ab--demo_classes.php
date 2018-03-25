<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          const my_const_1 = 300;
          use const \effcore\br;
          use \effcore\markup;
          abstract class demo_class_ab_1 {

  static public    $property_1;
  static protected $property_2;
  static private   $property_3 = my_const_1;

  static public function static_method_1() {return static::$property_3;} # return 300

}}



namespace effcore\modules\demo {
          use const \effcore\nl;
          use \effcore\locale;
          abstract class demo_class_ab_2 extends demo_class_ab_1 {

  static private $property_3 = my_const_1 + 100;
  static private $property_4 = 'new property #4';

  static public function static_method_1() {return static::$property_3;} # return 400
  static public function static_method_2() {return static::$property_4;} # return 'new property #4'

}}
