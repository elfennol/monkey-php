<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject;

use Elfennol\MonkeyPhp\Node\NodeInterface;

interface MacroSysObjectInterface extends SysObjectInterface
{
    public function node(): NodeInterface;
}
