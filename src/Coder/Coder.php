<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Coder;

use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;
use Elfennol\MonkeyPhp\Utils\Option\Some;
use Elfennol\MonkeyPhp\Utils\Type\Byte;
use Elfennol\MonkeyPhp\Utils\Type\NumberHelper;
use Exception;

/**
 *
 */
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
        $definitionOption = $this->lookup($opCode);
        if ($definitionOption->isNone()) {
            throw new CoderException(CoderExceptionType::OpCodeInvalid, ['mneOpCode' => $opCode->mnemonic()]);
        }
        $definition = $definitionOption->unwrap();

        $instruction = [];
        $index = 0;
        foreach ($operands as $operand) {
            try {
                $bytes = $this->numberHelper->toByteFixedArray($operand, $definition->operandWidths[$index]);
            } catch (Exception) {
                throw new CoderException(
                    CoderExceptionType::OperandInvalid,
                    ['mneOpCode' => $opCode->mnemonic(), 'operand' => $operand],
                );
            }

            $instruction = [Byte::fromInt($opCode->value), ...$bytes];
            $index++;
        }

        return $instruction;
    }

    /**
     * @return Option<Definition>
     */
    private function lookup(OpCode $opCode): Option
    {
        $definitions = $this->definitions->get();

        return empty($definitions[$opCode->value]) ? new None() : new Some($definitions[$opCode->value]);
    }
}
