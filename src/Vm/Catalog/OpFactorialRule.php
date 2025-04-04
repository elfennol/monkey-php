<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\SysObject\Catalog\IntSysObject;
use Elfennol\MonkeyPhp\Vm\AbstractVmRule;
use Elfennol\MonkeyPhp\Vm\VmContext;
use Elfennol\MonkeyPhp\Vm\VmException;
use Elfennol\MonkeyPhp\Vm\VmExceptionType;

/**
 * @extends AbstractVmRule<OpCode::Factorial>
 */
readonly class OpFactorialRule extends AbstractVmRule
{
    protected function execute(VmContext $context, OpCode $opCode): void
    {
        $operand = $context->stack->pop();

        if ($operand instanceof IntSysObject) {
            $value = $operand->nativeValue();
            if ($value < 0) {
                throw new VmException(
                    VmExceptionType::OperandInvalid,
                    [
                        'opCodeName' => $opCode->name,
                        'instPointer' => $context->currentReader()->getInstPointer(),
                        'operandValue' => $value,
                        'message' => 'Factorial operand must be positive.',
                    ],
                );
            }

            $context->stack->push(new IntSysObject((string)$this->factorial($value)));

            return;
        }

        throw new VmException(
            VmExceptionType::InvalidSysObject,
            [
                'opCodeName' => $opCode->name,
                'instPointer' => $context->currentReader()->getInstPointer(),
                'operand' => $operand->type(),
            ],
        );
    }

    private function factorial(int $value): int
    {
        if ($value < 0) {
            throw new VmException(
                VmExceptionType::OperandInvalid,
                [
                    'value' => $value,
                ],
            );
        }

        if ($value <= 1) {
            return 1;
        }

        return $value * $this->factorial($value - 1);
    }

    protected function support(OpCode $opCode): bool
    {
        return OpCode::Factorial === $opCode;
    }
}
