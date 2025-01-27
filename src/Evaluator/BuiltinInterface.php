<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator;

use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;

/**
 * @internal
 */
interface BuiltinInterface
{
    public function exec(SysObjectInterface ...$args): SysObjectInterface;
}
