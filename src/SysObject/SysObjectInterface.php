<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject;

interface SysObjectInterface
{
    public function type(): SysObjectType;
}
