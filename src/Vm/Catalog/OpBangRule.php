<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\SysObject\Catalog\BoolSysObject;
use Elfennol\MonkeyPhp\Vm\AbstractVmRule;
use Elfennol\MonkeyPhp\Vm\SingletonObj\FalseObj;
use Elfennol\MonkeyPhp\Vm\SingletonObj\TrueObj;
use Elfennol\MonkeyPhp\Vm\VmContext;
use Elfennol\MonkeyPhp\Vm\VmException;
use Elfennol\MonkeyPhp\Vm\VmExceptionType;

/**
 * @extends AbstractVmRule<OpCode::Bang>
 */
readonly class OpBangRule extends AbstractVmRule
{
    protected function execute(VmContext $context, OpCode $opCode): void
    {
        $operand = $context->stack->pop();

        if ($operand instanceof BoolSysObject) {
            $context->stack->push(true === $operand->nativeValue() ? FalseObj::get() : TrueObj::get());

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

    protected function support(OpCode $opCode): bool
    {
        return OpCode::Bang === $opCode;
    }
}
