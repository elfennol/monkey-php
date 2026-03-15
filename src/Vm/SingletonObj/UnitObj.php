<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm\SingletonObj;

use Elfennol\MonkeyPhp\SysObject\Catalog\UnitSysObject;

class UnitObj
{
    private static UnitSysObject $instance;

    public static function get(): UnitSysObject
    {
        if (!isset(self::$instance)) {
            self::$instance = new UnitSysObject();
        }

        return self::$instance;
    }
}
