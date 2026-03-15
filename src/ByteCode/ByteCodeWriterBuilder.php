<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\ByteCode;

use Elfennol\MonkeyPhp\SysObject\ConstantTypeInterface;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;

readonly class ByteCodeWriterBuilder
{
    /**
     * @param Option<array<int, ConstantTypeInterface>> $constant
     */
    public function build(Option $constant = new None()): ByteCodeWriter
    {
        return new ByteCodeWriter($constant);
    }
}
