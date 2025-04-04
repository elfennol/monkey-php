<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\SysObject\AtomSysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\Catalog\ArraySysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\HashMapSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\IntSysObject;
use Elfennol\MonkeyPhp\SysObject\HashKey;
use Elfennol\MonkeyPhp\Vm\AbstractVmRule;
use Elfennol\MonkeyPhp\Vm\VmContext;
use Elfennol\MonkeyPhp\Vm\VmException;
use Elfennol\MonkeyPhp\Vm\VmExceptionType;

/**
 * @extends AbstractVmRule<OpCode::Index>
 */
readonly class OpIndexRule extends AbstractVmRule
{
    public function __construct(private HashKey $hashKey)
    {
    }

    protected function execute(VmContext $context, OpCode $opCode): void
    {
        $index = $context->stack->pop();
        $left = $context->stack->pop();

        if ($left instanceof ArraySysObject && $index instanceof IntSysObject) {
            if ($index->nativeValue() < 0 || $index->nativeValue() > count($left->elements()) - 1) {
                throw new VmException(
                    VmExceptionType::UndefinedArrayKey,
                    [
                        'opCodeName' => $opCode->name,
                        'instPointer' => $context->currentReader()->getInstPointer(),
                        'indexValue' => $index->nodeValue(),
                    ],
                );
            }

            $context->stack->push($left->elements()[$index->nativeValue()]);

            return;
        }

        if ($left instanceof HashMapSysObject && $index instanceof AtomSysObjectInterface) {
            $hashKeyIndex = $this->hashKey->hash($index);
            if (!isset($left->elements()[$hashKeyIndex])) {
                throw new VmException(
                    VmExceptionType::UndefinedArrayKey,
                    [
                        'opCodeName' => $opCode->name,
                        'instPointer' => $context->currentReader()->getInstPointer(),
                        'indexValue' => $index->nodeValue(),
                    ],
                );
            }

            $context->stack->push($left->elements()[$hashKeyIndex]->value());

            return;
        }

        throw new VmException(
            VmExceptionType::InvalidSysObject,
            [
                'opCodeName' => $opCode->name,
                'instPointer' => $context->currentReader()->getInstPointer(),
            ],
        );
    }

    protected function support(OpCode $opCode): bool
    {
        return OpCode::Index === $opCode;
    }
}
