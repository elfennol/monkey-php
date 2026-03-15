<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm;

use Elfennol\MonkeyPhp\Coder\OpCode;

/**
 * @template TOpCode of OpCode
 */
abstract readonly class AbstractVmRule implements VmRuleInterface
{
    public function apply(VmContext $context, OpCode $opCode): bool
    {
        if (!$this->support($opCode)) {
            return false;
        }

        $this->execute($context, $opCode);

        return true;
    }

    /**
     * @param TOpCode $opCode
     */
    abstract protected function execute(VmContext $context, OpCode $opCode): void;

    /**
     * @phpstan-assert-if-true TOpCode $opCode
     */
    abstract protected function support(OpCode $opCode): bool;
}
