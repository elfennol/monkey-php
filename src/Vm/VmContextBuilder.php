<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm;

use Elfennol\MonkeyPhp\ByteCode\ByteCode;
use Elfennol\MonkeyPhp\ByteCode\ByteCodeReader;
use Elfennol\MonkeyPhp\SysObject\Builtins\BuiltinName;
use Elfennol\MonkeyPhp\SysObject\Context\BuiltinsInterface;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;

readonly class VmContextBuilder
{
    public function __construct(private BuiltinsInterface $builtinsInterface)
    {
    }

    /**
     * @param Option<Globals> $globals
     */
    public function build(ByteCode $byteCode, Option $globals = new None()): VmContext
    {
        $callStack = new CallStack();
        $callStack->push(new Frame(new ByteCodeReader($byteCode)));

        $builtins = [];
        foreach (BuiltinName::cases() as $i => $builtin) {
            $builtins[$i] = $this->builtinsInterface->get($builtin->value)->unwrap();
        }

        return new VmContext(
            $callStack,
            new Stack(),
            $globals->isNone() ? new Globals() : $globals->unwrap(),
            $builtins
        );
    }
}
