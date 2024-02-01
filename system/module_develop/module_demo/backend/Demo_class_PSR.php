<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace Vendor\Model;

class Demo_class_PSR
{

    public $publicAttribute;
    protected $protectedAttribute;
    private $privateAttribute;

    public static $publicStaticAttribute;
    protected static $protectedStaticAttribute;
    private static $privateStaticAttribute;

    public function mainMethod(int $arg1, bool &$arg2, ?array $arg3 = [], ... $otherArgs) : ?array
    {
        return null;
    }

}
