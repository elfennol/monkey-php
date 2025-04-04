<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm;

use Elfennol\MonkeyPhp\ByteCode\ByteCodeReader;
use Elfennol\MonkeyPhp\SysObject\Catalog\BuiltinSysObject;

readonly class VmContext
{
    /**
     * @param BuiltinSysObject[] $builtins
     */
    public function __construct(
        public CallStack $callStack,
        public Stack $stack,
        public Globals $globals,
        public array $builtins,
    ) {
    }

    public function currentReader(): ByteCodeReader
    {
        return $this->callStack->current()->byteCodeReader;
    }
}
