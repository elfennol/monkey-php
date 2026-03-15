<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Compiler\OperandsExtractor;
use Elfennol\MonkeyPhp\Vm\AbstractVmRule;
use Elfennol\MonkeyPhp\Vm\VmContext;

/**
 * @extends AbstractVmRule<OpCode::Jump>
 */
readonly class OpJumpRule extends AbstractVmRule
{
    public function __construct(private OperandsExtractor $operandsExtractor)
    {
    }

    protected function execute(VmContext $context, OpCode $opCode): void
    {
        $operands = $this->operandsExtractor->readFromByteCode($context->currentReader(), $opCode);
        $context->currentReader()->setInstPointer($operands[0]);
    }

    protected function support(OpCode $opCode): bool
    {
        return OpCode::Jump === $opCode;
    }
}
