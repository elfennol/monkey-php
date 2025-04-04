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
 * @extends AbstractVmRule<OpCode::Pow>
 */
readonly class OpPowRule extends AbstractVmRule
{
    protected function execute(VmContext $context, OpCode $opCode): void
    {
        $right = $context->stack->pop();
        $left = $context->stack->pop();

        if ($right instanceof IntSysObject && $left instanceof IntSysObject) {
            if (!$right->isPositive()) {
                throw new VmException(
                    VmExceptionType::OperandInvalid,
                    [
                        'opCodeName' => $opCode->name,
                        'instPointer' => $context->currentReader()->getInstPointer(),
                        'rightValue' => $right->nodeValue(),
                    ],
                    'Exponent must be positive.'
                );
            }

            $context->stack->push(new IntSysObject((string)($left->nativeValue() ** $right->nativeValue())));

            return;
        }

        throw new VmException(
            VmExceptionType::InvalidSysObject,
            [
                'opCodeName' => $opCode->name,
                'instPointer' => $context->currentReader()->getInstPointer(),
                'leftSysObjectType' => $left->type(),
                'rightSysObjectType' => $right->type()
            ],
        );
    }

    protected function support(OpCode $opCode): bool
    {
        return OpCode::Pow === $opCode;
    }
}
