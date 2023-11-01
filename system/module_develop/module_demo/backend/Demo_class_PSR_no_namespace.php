<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

class Vendor_Model_DemoClassPSRNoNamespace
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
