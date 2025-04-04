<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Utils\Type;

use Exception;

readonly class Byte
{
    private function __construct(private int $value)
    {
    }

    public static function fromInt(int $value): self
    {
        if (!(0 <= $value && 255 >= $value)) {
            throw new Exception('Invalid value.');
        }

        return new self($value);
    }

    public function toInt(): int
    {
        return $this->value;
    }
}
