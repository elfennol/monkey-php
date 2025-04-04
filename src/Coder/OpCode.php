<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Coder;

enum OpCode: int
{
    case Const = 0;

    public function mnemonic(): string
    {
        return strtolower($this->name);
    }
}
