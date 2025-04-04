<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Builtins;

use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;

interface BuiltinInterface
{
    public function exec(SysObjectInterface ...$args): SysObjectInterface;
}
