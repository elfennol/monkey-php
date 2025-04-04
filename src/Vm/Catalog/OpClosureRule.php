<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Compiler\OperandsExtractor;
use Elfennol\MonkeyPhp\SysObject\Catalog\ClosureSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\CompiledFnSysObject;
use Elfennol\MonkeyPhp\Vm\AbstractVmRule;
use Elfennol\MonkeyPhp\Vm\VmContext;
use Elfennol\MonkeyPhp\Vm\VmException;
use Elfennol\MonkeyPhp\Vm\VmExceptionType;

/**
 * @extends AbstractVmRule<OpCode::Closure>
 */
readonly class OpClosureRule extends AbstractVmRule
{
    public function __construct(private OperandsExtractor $operandsExtractor)
    {
    }

    protected function execute(VmContext $context, OpCode $opCode): void
    {
        [$fnConstIndex, $numFreeVars] = $this->operandsExtractor->readFromByteCode($context->currentReader(), $opCode);

        $freeVars = [];
        for ($i = 0; $i < $numFreeVars; $i++) {
            array_unshift($freeVars, $context->stack->pop());
        }

        $constant = $context->currentReader()->getConstant($fnConstIndex);
        if ($constant->isNone()) {
            throw new VmException(
                VmExceptionType::InvalidConstantPointer,
                ['constPointer' => $fnConstIndex, 'opCodeName' => $opCode->name],
            );
        }

        $fn = $constant->unwrap();
        if (!$fn instanceof CompiledFnSysObject) {
            throw new VmException(VmExceptionType::InvalidSysObject, ['expected' => CompiledFnSysObject::class]);
        }

        $context->stack->push(new ClosureSysObject($fn, $freeVars));
    }

    protected function support(OpCode $opCode): bool
    {
        return OpCode::Closure === $opCode;
    }
}
