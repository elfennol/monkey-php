<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Context;

use Elfennol\MonkeyPhp\SysObject\Catalog\BuiltinSysObject;
use Elfennol\MonkeyPhp\Utils\Option\Option;

interface BuiltinsInterface
{
    /**
     * @return Option<BuiltinSysObject>
     */
    public function get(string $name): Option;
}
