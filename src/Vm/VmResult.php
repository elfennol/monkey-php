<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm;

use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;

readonly class VmResult
{
    public function __construct(public SysObjectInterface $sysObject, public Globals $globals)
    {
    }
}
