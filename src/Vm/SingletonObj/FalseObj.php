<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm\SingletonObj;

use Elfennol\MonkeyPhp\SysObject\Catalog\BoolSysObject;

class FalseObj
{
    private static BoolSysObject $instance;

    public static function get(): BoolSysObject
    {
        if (!isset(self::$instance)) {
            self::$instance = new BoolSysObject('false');
        }

        return self::$instance;
    }
}
