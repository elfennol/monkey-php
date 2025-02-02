<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Context;

use Elfennol\MonkeyPhp\SysObject\Catalog\MacroBuiltinSysObject;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;
use Elfennol\MonkeyPhp\Utils\Option\Some;

readonly class MacroBuiltins implements MacroBuiltinsInterface
{
    /**
     * @param array<string, MacroBuiltinSysObject> $builtinFns
     */
    public function __construct(private array $builtinFns)
    {
    }

    public function get(string $name): Option
    {
        if (!isset($this->builtinFns[$name])) {
            return new None();
        }

        return new Some($this->builtinFns[$name]);
    }
}
