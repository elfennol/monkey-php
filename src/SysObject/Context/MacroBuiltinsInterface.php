<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Context;

use Elfennol\MonkeyPhp\SysObject\Catalog\MacroBuiltinSysObject;
use Elfennol\MonkeyPhp\Utils\Option\Option;

interface MacroBuiltinsInterface
{
    /**
     * @return Option<MacroBuiltinSysObject>
     */
    public function get(string $name): Option;
}
