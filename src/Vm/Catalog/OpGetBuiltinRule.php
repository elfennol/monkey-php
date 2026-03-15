<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Compiler\OperandsExtractor;
use Elfennol\MonkeyPhp\Vm\AbstractVmRule;
use Elfennol\MonkeyPhp\Vm\VmContext;

/**
 * @extends AbstractVmRule<OpCode::GetBuiltin>
 */
readonly class OpGetBuiltinRule extends AbstractVmRule
{
    public function __construct(private OperandsExtractor $operandsExtractor)
    {
    }

    protected function execute(VmContext $context, OpCode $opCode): void
    {
        $operands = $this->operandsExtractor->readFromByteCode($context->currentReader(), $opCode);
        $index = $operands[0];

        $context->stack->push($context->builtins[$index]);
    }

    protected function support(OpCode $opCode): bool
    {
        return OpCode::GetBuiltin === $opCode;
    }
}
