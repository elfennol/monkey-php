<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\ByteCode;

readonly class ByteCodeReaderBuilder
{
    public function build(ByteCode $byteCode): ByteCodeReader
    {
        return new ByteCodeReader($byteCode);
    }
}
