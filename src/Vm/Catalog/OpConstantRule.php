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
 * @extends AbstractVmRule<OpCode::Const>
 */
readonly class OpConstantRule extends AbstractVmRule
{
    public function __construct(private OperandsExtractor $operandsExtractor)
    {
    }

    protected function execute(VmContext $context, OpCode $opCode): void
    {
        $constantPointer = $this->operandsExtractor->readFromByteCode($context->currentReader(), $opCode);
        $constant = $context->currentReader()->getConstant($constantPointer[0]);

        if ($constant->isNone()) {
            throw new VmException(
                VmExceptionType::InvalidConstantPointer,
                [
                    'constPointer' => $constantPointer[0],
                    'opCodeName' => $opCode->name
                ],
            );
        }

        $context->stack->push($constant->unwrap());
    }

    protected function support(OpCode $opCode): bool
    {
        return OpCode::Const === $opCode;
    }
}
