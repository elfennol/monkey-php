<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\ByteCode;

use Elfennol\MonkeyPhp\SysObject\ConstantTypeInterface;
use Elfennol\MonkeyPhp\Utils\Type\Byte;

readonly class ByteCode
{
    /**
     * @param Byte[] $instructions
     * @param array<int, ConstantTypeInterface> $constants
     */
    public function __construct(public array $instructions, public array $constants)
    {
    }
}
