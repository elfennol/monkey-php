<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject;

use Elfennol\MonkeyPhp\Utils\Type\Hashable;
use Stringable;

interface ConstantTypeInterface extends SysObjectInterface, Hashable, Stringable
{
}
