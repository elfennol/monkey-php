<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Compiler\OperandsExtractor;
use Elfennol\MonkeyPhp\Vm\AbstractVmRule;
use Elfennol\MonkeyPhp\Vm\VmContext;
use Elfennol\MonkeyPhp\Vm\VmException;
use Elfennol\MonkeyPhp\Vm\VmExceptionType;

/**
 * @extends AbstractVmRule<OpCode::GetLocal>
 */
readonly class OpGetLocalRule extends AbstractVmRule
{
    public function __construct(private OperandsExtractor $operandsExtractor)
    {
    }

    protected function execute(VmContext $context, OpCode $opCode): void
    {
        $operands = $this->operandsExtractor->readFromByteCode($context->currentReader(), $opCode);
        $index = $operands[0];

        if (!isset($context->callStack->current()->locals[$index])) {
            throw new VmException(VmExceptionType::UninitializedLocal, ['index' => $index]);
        }

        $context->stack->push($context->callStack->current()->locals[$index]);
    }

    protected function support(OpCode $opCode): bool
    {
        return OpCode::GetLocal === $opCode;
    }
}
