<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\SysObject\Catalog\BoolSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\IntSysObject;
use Elfennol\MonkeyPhp\Vm\AbstractVmRule;
use Elfennol\MonkeyPhp\Vm\SingletonObj\FalseObj;
use Elfennol\MonkeyPhp\Vm\SingletonObj\TrueObj;
use Elfennol\MonkeyPhp\Vm\VmContext;
use Elfennol\MonkeyPhp\Vm\VmException;
use Elfennol\MonkeyPhp\Vm\VmExceptionType;

/**
 * @extends AbstractVmRule<OpCode::Equal>
 */
readonly class OpEqualRule extends AbstractVmRule
{
    protected function execute(VmContext $context, OpCode $opCode): void
    {
        $right = $context->stack->pop();
        $left = $context->stack->pop();

        if (
            ($right instanceof IntSysObject && $left instanceof IntSysObject)
            || ($right instanceof BoolSysObject && $left instanceof BoolSysObject)
        ) {
            $context->stack->push(
                $left->nativeValue() === $right->nativeValue() ? TrueObj::get() : FalseObj::get()
            );

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
        return OpCode::Equal === $opCode;
    }
}
