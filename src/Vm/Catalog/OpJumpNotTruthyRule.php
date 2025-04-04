<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Compiler\OperandsExtractor;
use Elfennol\MonkeyPhp\SysObject\Catalog\BoolSysObject;
use Elfennol\MonkeyPhp\Vm\AbstractVmRule;
use Elfennol\MonkeyPhp\Vm\VmContext;
use Elfennol\MonkeyPhp\Vm\VmException;
use Elfennol\MonkeyPhp\Vm\VmExceptionType;

/**
 * @extends AbstractVmRule<OpCode::JumpNotTruthy>
 */
readonly class OpJumpNotTruthyRule extends AbstractVmRule
{
    public function __construct(private OperandsExtractor $operandsExtractor)
    {
    }

    protected function execute(VmContext $context, OpCode $opCode): void
    {
        $operands = $this->operandsExtractor->readFromByteCode($context->currentReader(), $opCode);
        $condition = $context->stack->pop();

        if (!$condition instanceof BoolSysObject) {
            throw new VmException(
                VmExceptionType::InvalidSysObject,
                [
                    'opCodeName' => $opCode->name,
                    'instPointer' => $context->currentReader()->getInstPointer(),
                    'operand' => $condition->type(),
                ],
            );
        }

        if (false === $condition->nativeValue()) {
            $context->currentReader()->setInstPointer($operands[0]);
        }
    }

    protected function support(OpCode $opCode): bool
    {
        return OpCode::JumpNotTruthy === $opCode;
    }
}
