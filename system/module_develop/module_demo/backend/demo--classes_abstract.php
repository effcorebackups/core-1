<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          const my_const_1 = 'static property priv';
          use const \effcore\br;
          use \effcore\markup;
          abstract class demo_class_ab_1 {

  static public    $static_property_publ;
  static protected $static_property_prot;
  static private   $static_property_priv = my_const_1;

  static public function static_method_1() {
  # return 'static property priv'
    return static::$static_property_priv;
  }

}}



namespace effcore\modules\demo {
          use const \effcore\nl;
          use \effcore\locale;
          abstract class demo_class_ab_2 extends demo_class_ab_1 {

  static private $static_property_priv = my_const_1.' modified';
  static private $static_property_priv_new = 'static property priv new';

  static public function static_method_1() {
  # return 'static property priv modified'
    return static::$static_property_priv;
  }

  static public function static_method_2() {
  # return 'static property priv new'
    return static::$static_property_priv_new;
  }

}}
