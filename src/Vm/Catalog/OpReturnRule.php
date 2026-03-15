<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Vm\AbstractVmRule;
use Elfennol\MonkeyPhp\Vm\SingletonObj\UnitObj;
use Elfennol\MonkeyPhp\Vm\VmContext;

/**
 * @extends AbstractVmRule<OpCode::Return>
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
readonly class OpReturnRule extends AbstractVmRule
{
    protected function execute(VmContext $context, OpCode $opCode): void
    {
        $context->callStack->pop();
        $context->stack->push(UnitObj::get());
    }

    protected function support(OpCode $opCode): bool
    {
        return OpCode::Return === $opCode;
    }
}
