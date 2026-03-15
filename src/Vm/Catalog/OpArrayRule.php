<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Compiler\OperandsExtractor;
use Elfennol\MonkeyPhp\SysObject\AtomSysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\Catalog\ArraySysObject;
use Elfennol\MonkeyPhp\Vm\AbstractVmRule;
use Elfennol\MonkeyPhp\Vm\VmContext;

/**
 * @extends AbstractVmRule<OpCode::Array>
 */
readonly class OpArrayRule extends AbstractVmRule
{
    public function __construct(private OperandsExtractor $operandsExtractor)
    {
    }

    protected function execute(VmContext $context, OpCode $opCode): void
    {
        $totalElements = $this->operandsExtractor->readFromByteCode($context->currentReader(), $opCode)[0];
        $elements = [];
        for ($i = 0; $i < $totalElements; $i++) {
            /** @var AtomSysObjectInterface $sysObj */
            $sysObj = $context->stack->pop();
            $elements[] = $sysObj;
        }
        $elements = array_reverse($elements);

        $context->stack->push(new ArraySysObject($elements));
    }

    protected function support(OpCode $opCode): bool
    {
        return OpCode::Array === $opCode;
    }
}
