<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler;

use Elfennol\MonkeyPhp\ByteCode\ByteCodeReader;
use Elfennol\MonkeyPhp\Coder\Definitions;
use Elfennol\MonkeyPhp\Coder\OpCode;

readonly class OperandsExtractor
{
    public function __construct(private Definitions $definitions)
    {
    }

    /**
     * @return int[]
     */
    public function readFromByteCode(ByteCodeReader $byteCodeReader, OpCode $opCode): array
    {
        $operandWidths = $this->definitions->lookup($opCode)->unwrap()->operandWidths;
        $decOperands = [];
        foreach ($operandWidths as $operandWidth) {
            $value = 0;
            for ($bytes = 0; $bytes < $operandWidth; $bytes++) {
                $byte = $byteCodeReader->readByte()->unwrap()->toInt();
                $value = ($value << 8) | $byte;
            }
            $decOperands[] = $value;
        }

        return $decOperands;
    }
}
