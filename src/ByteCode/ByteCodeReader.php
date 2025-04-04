<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\ByteCode;

use Elfennol\MonkeyPhp\SysObject\ConstantTypeInterface;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;
use Elfennol\MonkeyPhp\Utils\Option\Some;
use Elfennol\MonkeyPhp\Utils\Type\Byte;

class ByteCodeReader
{
    private int $instPointer = 0;
    public readonly int $totalInst;

    public function __construct(
        private readonly ByteCode $byteCode,
    ) {
        $this->totalInst = count($this->byteCode->instructions);
    }

    /**
     * Consumes the current byte and advances the instPointer.
     *
     * @return Option<Byte>
     */
    public function readByte(): Option
    {
        $byte = $this->getByte();
        if ($byte->isSome()) {
            $this->instPointer++;
        }

        return $byte;
    }

    /**
     * @return Option<ConstantTypeInterface>
     */
    public function getConstant(int $index): Option
    {
        if (empty($this->byteCode->constants[$index])) {
            return new None();
        }

        return new Some($this->byteCode->constants[$index]);
    }

    public function getInstPointer(): int
    {
        return $this->instPointer;
    }

    public function setInstPointer(int $instPointer): void
    {
        $this->instPointer = $instPointer;
    }

    public function getByteCode(): ByteCode
    {
        return $this->byteCode;
    }

    /**
     * @return Option<Byte>
     */
    private function getByte(): Option
    {
        if (empty($this->byteCode->instructions[$this->instPointer])) {
            return new None();
        }

        return new Some($this->byteCode->instructions[$this->instPointer]);
    }
}
