<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Compiler\OperandsExtractor;
use Elfennol\MonkeyPhp\SysObject\AtomSysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\Catalog\HashMapItemSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\HashMapSysObject;
use Elfennol\MonkeyPhp\SysObject\HashKey;
use Elfennol\MonkeyPhp\Vm\AbstractVmRule;
use Elfennol\MonkeyPhp\Vm\VmContext;

/**
 * @extends AbstractVmRule<OpCode::HashMap>
 */
readonly class OpHashMapRule extends AbstractVmRule
{
    public function __construct(private OperandsExtractor $operandsExtractor, private HashKey $hashKey)
    {
    }

    protected function execute(VmContext $context, OpCode $opCode): void
    {
        $totalElements = $this->operandsExtractor->readFromByteCode($context->currentReader(), $opCode)[0];
        $elements = [];
        for ($i = 0; $i < $totalElements; $i += 2) {
            /** @var AtomSysObjectInterface $value */
            $value = $context->stack->pop();
            /** @var AtomSysObjectInterface $key */
            $key = $context->stack->pop();
            $elements[$this->hashKey->hash($key)] = new HashMapItemSysObject($key, $value);
        }
        $elements = array_reverse($elements);

        $context->stack->push(new HashMapSysObject($elements));
    }

    protected function support(OpCode $opCode): bool
    {
        return OpCode::HashMap === $opCode;
    }
}
