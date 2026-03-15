<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm;

use Elfennol\MonkeyPhp\Coder\OpCode;

interface VmRuleInterface
{
    public function apply(VmContext $context, OpCode $opCode): bool;
}
