<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

abstract class Demo_class_abstract {

             public           function public() {}
             protected        function protected() {}
             private          function private() {}
             public    static function publicStatic() {}
             protected static function protectedStatic() {}
             private   static function privateStatic() {}
    abstract public           function abstractPublic();
    abstract protected        function abstractProtected();
  # abstract private          function abstractPrivate();
    abstract public    static function abstractPublicStatic();
    abstract protected static function abstractProtectedStatic();
  # abstract private   static function abstractPrivateStatic();

}
