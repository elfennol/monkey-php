<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Coder;

readonly class Definition
{
    /**
     * @param int[] $operandWidths
     */
    public function __construct(public OpCode $opCode, public array $operandWidths)
    {
    }
}
