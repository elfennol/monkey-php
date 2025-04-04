<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\SysObject\Catalog\IntSysObject;
use Elfennol\MonkeyPhp\Vm\AbstractVmRule;
use Elfennol\MonkeyPhp\Vm\SingletonObj\FalseObj;
use Elfennol\MonkeyPhp\Vm\SingletonObj\TrueObj;
use Elfennol\MonkeyPhp\Vm\VmContext;
use Elfennol\MonkeyPhp\Vm\VmException;
use Elfennol\MonkeyPhp\Vm\VmExceptionType;

/**
 * @extends AbstractVmRule<OpCode::GreaterThan>
 */
readonly class OpGreaterThanRule extends AbstractVmRule
{
    protected function execute(VmContext $context, OpCode $opCode): void
    {
        $right = $context->stack->pop();
        $left = $context->stack->pop();

        if ($right instanceof IntSysObject && $left instanceof IntSysObject) {
            $context->stack->push(
                $left->nativeValue() > $right->nativeValue() ? TrueObj::get() : FalseObj::get()
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
        return OpCode::GreaterThan === $opCode;
    }
}
