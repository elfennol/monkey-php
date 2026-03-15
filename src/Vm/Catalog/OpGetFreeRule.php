<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Compiler\OperandsExtractor;
use Elfennol\MonkeyPhp\Vm\AbstractVmRule;
use Elfennol\MonkeyPhp\Vm\VmContext;

/**
 * @extends AbstractVmRule<OpCode::GetFree>
 */
readonly class OpGetFreeRule extends AbstractVmRule
{
    public function __construct(private OperandsExtractor $operandsExtractor)
    {
    }

    protected function execute(VmContext $context, OpCode $opCode): void
    {
        $operands = $this->operandsExtractor->readFromByteCode($context->currentReader(), $opCode);
        $index = $operands[0];

        $closure = $context->callStack->current()->closure->unwrap();
        $context->stack->push($closure->freeVars()[$index]);
    }

    protected function support(OpCode $opCode): bool
    {
        return OpCode::GetFree === $opCode;
    }
}
