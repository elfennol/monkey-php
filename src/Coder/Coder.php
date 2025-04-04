<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Coder;

use Elfennol\MonkeyPhp\Utils\Type\Byte;
use Elfennol\MonkeyPhp\Utils\Type\NumberHelper;
use Exception;

readonly class Coder
{
    public function __construct(private Definitions $definitions, private NumberHelper $numberHelper)
    {
    }

    /**
     * @param int[] $operands
     *
     * @return Byte[]
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function make(OpCode $opCode, array $operands): array
    {
        $definitionOption = $this->definitions->lookup($opCode);
        if ($definitionOption->isNone()) {
            throw new CoderException(CoderExceptionType::OpCodeInvalid, ['mneOpCode' => $opCode->name]);
        }
        $definition = $definitionOption->unwrap();

        $instruction = [Byte::fromInt($opCode->value)];
        $index = 0;
        foreach ($operands as $operand) {
            try {
                $bytes = $this->numberHelper->toByteFixedArray($operand, $definition->operandWidths[$index]);
            } catch (Exception) {
                throw new CoderException(
                    CoderExceptionType::OperandInvalid,
                    ['mneOpCode' => $opCode->name, 'operand' => $operand],
                );
            }

            $instruction = [...$instruction, ...$bytes];
            $index++;
        }

        return $instruction;
    }
}
