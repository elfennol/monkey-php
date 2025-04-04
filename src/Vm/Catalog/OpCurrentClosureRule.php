<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Vm\AbstractVmRule;
use Elfennol\MonkeyPhp\Vm\VmContext;

/**
 * @extends AbstractVmRule<OpCode::CurrentClosure>
 */
readonly class OpCurrentClosureRule extends AbstractVmRule
{
    protected function execute(VmContext $context, OpCode $opCode): void
    {
        $context->stack->push($context->callStack->current()->closure->unwrap());
    }

    protected function support(OpCode $opCode): bool
    {
        return OpCode::CurrentClosure === $opCode;
    }
}
