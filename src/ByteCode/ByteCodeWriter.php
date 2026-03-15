<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\ByteCode;

use Elfennol\MonkeyPhp\SysObject\ConstantTypeInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;
use Elfennol\MonkeyPhp\Utils\Type\Byte;

class ByteCodeWriter
{
    /**
     * @var Byte[] $instructions
     */
    private array $instructions = [];

    /**
     * @var array<int, ConstantTypeInterface> $constants
     */
    private array $constants;

    /**
     * @param Option<array<int, ConstantTypeInterface>> $constant
     */
    public function __construct(Option $constant = new None())
    {
        $this->constants = $constant->isNone() ? [] : $constant->unwrap();
    }

    /**
     * @param Byte[] $instruction
     */
    public function writeInstruction(array $instruction): void
    {
        array_push($this->instructions, ...$instruction);
    }

    /**
     * @param Byte[] $instruction
     */
    public function replaceInstruction(int $pos, array $instruction): void
    {
        array_splice($this->instructions, $pos, count($instruction), $instruction);
    }

    public function size(): int
    {
        return count($this->instructions);
    }

    public function writeConstant(ConstantTypeInterface $constant): int
    {
        $key = array_find_key(
            $this->constants,
            fn (SysObjectInterface $currentConstant) => $currentConstant->hash() === $constant->hash()
        );

        if (null !== $key) {
            return $key;
        }

        $this->constants[] = $constant;

        return array_key_last($this->constants);
    }

    public function byteCode(): ByteCode
    {
        return new ByteCode($this->instructions, $this->constants);
    }

    /**
     * @return array<int, ConstantTypeInterface>
     */
    public function constants(): array
    {
        return $this->constants;
    }
}
