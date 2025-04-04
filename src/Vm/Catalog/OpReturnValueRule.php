<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Vm\AbstractVmRule;
use Elfennol\MonkeyPhp\Vm\VmContext;

/**
 * @extends AbstractVmRule<OpCode::ReturnValue>
 */
readonly class OpReturnValueRule extends AbstractVmRule
{
    protected function execute(VmContext $context, OpCode $opCode): void
    {
        $returnValue = $context->stack->pop();
        $context->callStack->pop();
        $context->stack->push($returnValue);
    }

    protected function support(OpCode $opCode): bool
    {
        return OpCode::ReturnValue === $opCode;
    }
}
