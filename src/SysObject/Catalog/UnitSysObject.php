<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Catalog;

use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectType;

readonly class UnitSysObject implements SysObjectInterface
{
    public function type(): SysObjectType
    {
        return SysObjectType::Unit;
    }
}
